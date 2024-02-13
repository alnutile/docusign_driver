<?php

namespace AlNutile\DocusignDriver\Http\Controllers;

use AlNutile\DocusignDriver\DocusignDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectronicSignatureWebhookController
{
    private DocusignDriver $docusignDriver;

    public function __construct(DocusignDriver $docusignDriver)
    {
        $this->docusignDriver = $docusignDriver;
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $webhook = $request->all();

        if ($webhook['event'] !== 'recipient-completed') {
            return new JsonResponse([], 200);
        }

        \Log::info($webhook);

        return new JsonResponse($webhook, 200);
    }

    public function submit()
    {
        $templateId = '8cc418dc-8eb6-46eb-93f4-1ced851d5f84';
        $submitters = [
            [
                "email" => 'aqi64u@gmail.com',
                "name" => 'Testing',
                "roleName" => "signer",
                'tabs' => [
                    'textTabs' => [
                        [
                            "tabLabel" => "AgreementDay",
                            "value" => "15",
                        ],
                        [
                            'tabLabel' => 'AgreementMonth',
                            'value' => 'Feb',
                        ],
                        [
                            'tabLabel' => 'AgreementYear',
                            'value' => 'Feb',
                        ],
                        [
                            'tabLabel' => 'LandOwnerName',
                            'value' => 'Feb',
                        ],
                        [
                            'tabLabel' => 'PropertyStreetAddress',
                            'value' => '5396 North Reese Avenue',
                        ],
                        [
                            'tabLabel' => 'PropertyCity',
                            'value' => 'Fresno',
                        ],
                        [
                            'tabLabel' => 'PropertyZip',
                            'value' => '93722',
                        ],
                        [
                            'tabLabel' => 'ApnNumber',
                            'value' => '1234',
                        ],
                        [
                            'tabLabel' => 'LandOwnerAddressLine1',
                            'value' => 'line1',
                        ],
                        [
                            'tabLabel' => 'LandOwnerAddressLine2',
                            'value' => 'line2',
                        ],
                        [
                            'tabLabel' => 'LandOwnerAddressLine3',
                            'value' => 'line3',
                        ],
                        [
                            'tabLabel' => 'LandOwnerPrintedName',
                            'value' => 'line3',
                        ],
                        [
                            'tabLabel' => 'LandOwnerPhone',
                            'value' => '+11234567890',
                        ],
                        [
                            'tabLabel' => 'LandOwnerEmail',
                            'value' => 'aqi@gmail.com',
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->docusignDriver->submit($submitters, $templateId)->toJson();

        return response()->json($result);
    }
}
