<?php

use AlNutile\DocusignDriver\Http\Controllers\ElectronicSignatureWebhookController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api')->group(function () {
    Route::any('/webhooks/electronic_signature_system', [ElectronicSignatureWebhookController::class, 'handleWebhook'])
        ->name('docusign.webhook');
    Route::any('/docusign/callback', function() {
        Log::info('Docusign callback', request()->all());
        return response()->json(['message' => 'OK']);
    })->name('docusign.callback');
});
