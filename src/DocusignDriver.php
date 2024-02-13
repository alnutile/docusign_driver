<?php

namespace AlNutile\DocusignDriver;

use AlNutile\DocusignDriver\Responses\GetSubmissionResponse;
use AlNutile\DocusignDriver\Responses\ListAllTemplatesResponse;
use AlNutile\DocusignDriver\Responses\ResponseException;
use AlNutile\DocusignDriver\Responses\SubmissionResponse;
use AlNutile\DocusignDriver\Responses\Submitter;
use AlNutile\DocusignDriver\Responses\TemplateDto;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocusignDriver extends ClientContract
{
    protected string $baseUrl = '';

    protected string $defaultScope =
        'signature user_write group_read organization_read permission_read user_read account_read domain_read identity_provider_read user_data_redact asset_group_account_read asset_group_account_clone_write asset_group_account_clone_read';

    public function getUrlForSubmission(SubmissionResponse $submissionResponse): string
    {
        return '@TODO';
    }

    /**
     * @NOTE
     * Provide envelopeId to download all the combined documents.
     */
    public function downloadDocument(string $submitterId): string|bool
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $path = Storage::path(config('docusigndriver.storage_path'));
        if (! file_exists($path)) {
            Storage::makeDirectory(config('docusigndriver.storage_path'));
        }

        $uuid = Str::uuid().'.pdf';
        $document = Storage::path(config('docusigndriver.storage_path')).'/'.$uuid;

        $response = $client
            ->sink($document)
            ->get("/restapi/v2.1/accounts/$accountId/envelopes/$submitterId/documents/combined");

        if ($response->status() !== 200) {
            throw new ResponseException($response->body());
        }

        return $document;
    }

    /**
     * @NOTE Get the recipient of the envelope from the api
     *
     * @param  string  $submitterId  [
     *                               'envelopeId' => 'uuid,
     *                               'recipientId' => 'uuid'
     *                               ]
     */
    public function getSubmitter(mixed $submitterId): Submitter
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $response = $client
            ->get(sprintf(
                "/restapi/v2.1/accounts/$accountId/envelopes/%s/recipients?include_tabs=true",
                $submitterId['envelopeId'],
            ));

        if ($response->status() !== 200) {
            throw new ResponseException($response->body());
        }

        $recipients = json_decode($response->body(), true);

        if (empty($recipients['signers'])) {
            throw new ResponseException('No submitter found.');
        }

        $sender = collect($recipients['signers'])
            ->where('recipientIdGuid', $submitterId['recipientId'])
            ->where('creationReason', 'sender')
            ->where('status', 'completed')
            ->first();

        if (empty($sender)) {
            throw new ResponseException('No submitter found.');
        }

        return Submitter::from([
            'id' => $sender['recipientId'],
            'submission_id' => $submitterId['envelopeId'],
            'uuid' => $sender['recipientIdGuid'],
            'email' => $sender['email'],
            'slug' => '',
            'sent_at' => $sender['sentDateTime'],
            'completed_at' => $sender['signedDateTime'],
            'name' => $sender['name'],
            'phone' => '',
            'values' => $sender['tabs'] ?? [],
        ]);
    }

    /**
     * @NOTE Get the envelope from the api.
     *
     * @param  string  $submissionId  EnvelopeId.
     */
    public function getSubmission(mixed $submissionId): GetSubmissionResponse
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $response = $client
            ->get("/restapi/v2.1/accounts/$accountId/envelopes/$submissionId?include=recipients,tabs");

        if ($response->status() !== 200) {
            throw new ResponseException($response->body());
        }

        $envelope = json_decode($response->body(), true);

        $submitters = collect($envelope['recipients']['signers'] ?? [])
            ->where('creationReason', 'sender')
            ->map(function ($submitter) use ($submissionId) {
                return [
                    'id' => $submitter['recipientId'],
                    'submission_id' => $submissionId,
                    'uuid' => $submitter['recipientIdGuid'],
                    'email' => $submitter['email'],
                    'slug' => '',
                    'sent_at' => $submitter['sentDateTime'],
                    'completed_at' => $submitter['signedDateTime'] ?? '',
                    'name' => $submitter['name'],
                    'phone' => '',
                    'values' => $submitter['tabs'] ?? [],
                ];
            })
            ->toArray();

        return GetSubmissionResponse::from([
            'id' => 0,
            'source' => $envelope['envelopeLocation'],
            'audit_log_url' => '',
            'submitters' => $submitters,
            'template' => new TemplateDto(
                '',
                $envelope['templatesUri'],
                '',
                []
            ),
            'submission_events' => [],
        ]);
    }

    /**
     * @NOTE
     * This is the most important one
     * Using the API it makes an Envelope of the existing template
     * We send up an array of labels and names to prefill
     *
     * https://developers.docusign.com/docs/esign-rest-api/reference/envelopes/envelopes/create/
     * https://developers.docusign.com/docs/esign-rest-api/reference/envelopes/enveloperecipienttabs/#tab-types
     *
     * @param  array  $submittersDto  [
     *                                "email" => 'jane@example.com',
     *                                "name" => 'Jane doe',
     *                                "roleName" => "signer",
     *                                'tabs' => [
     *                                'numericalTabs' => [
     *                                [
     *                                "tabLabel" => "Age",
     *                                "numericalValue" => 72.00,
     *                                ],
     *                                ],
     *                                'textTabs' => [
     *                                [
     *                                "tabLabel" => "Bio",
     *                                "value" => "Developer",
     *                                ],
     *                                ],
     *                                ],
     *                                ]
     */
    public function submit(array $submittersDto, mixed $templateId): SubmissionResponse
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $payload = [
            'templateId' => $templateId,
            'templateRoles' => $submittersDto,
            'status' => 'sent',
        ];

        $response = $client
            ->post("/restapi/v2.1/accounts/$accountId/envelopes", $payload);

        if ($response->status() !== 201) {
            throw new ResponseException($response->body());
        }

        $result = json_decode($response->body(), true);

        return SubmissionResponse::from([
            'id' => 0,
            'submission_id' => 0,
            'uuid' => $result['envelopeId'],
            'email' => '',
            'phone' => '',
            'slug' => '',
            'sent_at' => $result['statusDateTime'],
            'values' => [],
        ]);
    }

    /**
     * @NOTE
     * The DocuSign UI can be used to make the template
     * This is not really needed
     * We can give it a try later if all goes well
     */
    public function uploadTemplate(string $template = 'LandAccessAgreementTemplate.pdf')
    {
        return '@TODO';
    }


    public function getTemplate(string $templateId): array
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $response = $client->get("/restapi/v2.1/accounts/$accountId/templates/$templateId");

        if ($response->status() !== 200) {
            throw new ResponseException($response->body());
        }

        return $response->json();
    }


    public function listTemplates(): ListAllTemplatesResponse
    {
        $this->baseUrl = config('docusigndriver.rest_url');

        $client = $this->getClient();

        $accountId = config('docusigndriver.account_id');

        $response = $client->get("/restapi/v2.1/accounts/$accountId/templates");

        if ($response->status() !== 200) {
            throw new ResponseException($response->body());
        }

        return ListAllTemplatesResponse::from([
            'templates' => data_get($response->json(), 'envelopeTemplates', []),
        ]);
    }

    public function getRestUrl(): string
    {
        return config('docusigndriver.base_url');
    }

    /**
     * @NOTE
     * This uses some of the docusign library to get the token
     * and then make the client to use in the other functions
     */
    public function getClient()
    {
        $accessToken = $this->getDocuSignAccessToken();

        $client = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);

        return $client;
    }

    /**
     * @NOTE
     * This is the tricky part for me
     * I used their library but then
     * did this to see how it worked.
     * You can change it back
     * But it does work but
     * It does not do refresh well
     */
    public function createJwtToken(): string
    {
        $scope = $this->defaultScope;
        $client_id = config('docusigndriver.integrator_key');
        $user_id = config('docusigndriver.user_id');
        $expires_in = config('docusigndriver.expires_in');
        $rsa_private_key = config('docusigndriver.rsa_private_key');

        $now = time();

        $aud = str(config('docusigndriver.base_url'))->after('https://')->toString();

        $claim = [
            'iss' => $client_id,
            'sub' => $user_id,
            'aud' => $aud,
            'iat' => $now,
            'exp' => $now + (int) $expires_in * 60,
            'scope' => $scope,
        ];

        $jwt = JWT::encode($claim, $rsa_private_key, 'RS256');

        return $jwt;
    }

    /**
     * @TODO
     * This is used from the above to get the JWT Token
     * Or show the initial consent URL to open in a screen
     * They have tons of vidoes on this
     *
     * @see https://developers.docusign.com/platform/auth/jwt/jwt-get-token/
     */
    public function getDocuSignAccessToken()
    {
        $expires_in = config('docusigndriver.expires_in');

        if (Cache::has('docusign_access_token') && Cache::has('docusign_access_token_valid')) {
            return Cache::get('docusign_access_token');
        } else {
            $jwtToken = $this->createJwtToken();

            $auth_url = config('docusigndriver.auth_host');

            $response = Http::asForm()
                ->withHeader('X-DocuSign-SDK', 'PHP')->post($auth_url, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwtToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Cache::put('docusign_access_token', $data['access_token'], $expires_in * 60);
                Cache::put('docusign_access_token_valid', 1, ($expires_in - 2) * 60);

                return $data['access_token'];
            } else {
                if ($response->status() === 400) {
                    /**
                     * This is the consent url
                     * Afte this we just need to do a refresh of the cache
                     */
                    $client_id = config('docusigndriver.integrator_key');

                    return config('docusigndriver.base_url').'/oauth/auth?prompt=login&response_type=code&'
                    .http_build_query(
                        [
                            'scope' => 'impersonation+'.$this->defaultScope,
                            'client_id' => $client_id,
                            'redirect_uri' => route('docusign.callback'),
                        ]
                    );
                } elseif ($response->status() === 401) {
                    Cache::forget('docusign_access_token');
                    Cache::forget('docusign_access_token_valid');

                    return $this->getDocuSignAccessToken();
                } else {
                    throw new \Exception('Failed to obtain access token from DocuSign '.$response->body());
                }
            }
        }
    }
}
