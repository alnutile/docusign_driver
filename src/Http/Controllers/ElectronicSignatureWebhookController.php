<?php

namespace AlNutile\DocusignDriver\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectronicSignatureWebhookController
{
    /**
     * @see https://developers.docusign.com/platform/webhooks/connect/event-triggers/
     */
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
