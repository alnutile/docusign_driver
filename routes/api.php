<?php

use AlNutile\DocusignDriver\Http\Controllers\ElectronicSignatureWebhookController;
use Illuminate\Support\Facades\Route;

Route::any('/webhooks/electronic_signature_system', [ElectronicSignatureWebhookController::class, 'handleWebhook']);
