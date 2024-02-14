<?php

namespace AlNutile\DocusignDriver\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectronicSignatureWebhookController
{
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
