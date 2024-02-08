<?php

namespace AlNutile\DocusignDriver;

use AlNutile\DocusignDriver\Response\Submitter;
use AlNutile\DocusignDriver\Responses\ClientContract;
use AlNutile\DocusignDriver\Responses\GetSubmissionResponse;
use AlNutile\DocusignDriver\Responses\ListAllTemplatesResponse;
use AlNutile\ElectronicSignatures\Response\SubmissionResponse;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DocusignDriver extends ClientContract
{
    protected string $baseUrl = '';

    protected string $defaultScope =
        'signature user_write group_read organization_read permission_read user_read account_read domain_read identity_provider_read user_data_redact asset_group_account_read asset_group_account_clone_write asset_group_account_clone_read';

    public function getUrlForSubmission(SubmissionResponse $submissionResponse): string
    {
        return '@TODO';
    }

    public function downloadDocument(string $submitterId): string|bool
    {
        return '@TODO';
    }

    public function getSubmitter(mixed $submitterId): Submitter
    {
        return Submitter::from([]);
    }

    public function getSubmission(mixed $submissionId): GetSubmissionResponse
    {
        return GetSubmissionResponse::from([]);
    }

    public function submit(array $submittersDto, mixed $templateId): SubmissionResponse
    {
        return SubmissionResponse::from([]);
    }

    public function uploadTemplate(string $template = 'LandAccessAgreementTemplate.pdf')
    {
        return '@TODO';
    }

    /**
     * @NOTE this one is working
     */
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
            'templates' => data_get($response->json(), 'data', []),
        ]);
    }

    public function getRestUrl(): string
    {
        return config('docusigndriver.base_url');
    }

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
     * The Refresh token is not working 100% yet
     */
    public function getDocuSignAccessToken()
    {
        if (Cache::has('docusign_access_token')) {
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
                Cache::put('docusign_access_token', $data['access_token']);

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

                    return $this->getDocuSignAccessToken();
                } else {
                    throw new \Exception('Failed to obtain access token from DocuSign '.$response->body());
                }
            }
        }
    }
}
