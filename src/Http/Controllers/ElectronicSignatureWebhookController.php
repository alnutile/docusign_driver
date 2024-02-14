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
}
