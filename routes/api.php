<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/webhooks/whatsapp', function (\Illuminate\Http\Request $request) {
    $mode = $request->query('hub_mode');
    $token = $request->query('hub_verify_token');
    $challenge = $request->query('hub_challenge');

    if (
        $mode === 'subscribe'
        && is_string($token)
        && is_string(config('services.whatsapp.verify_token'))
        && config('services.whatsapp.verify_token') !== ''
        && hash_equals(config('services.whatsapp.verify_token'), $token)
    ) {
        return response($challenge, 200)
            ->header('Content-Type', 'text/plain');
    }

    return response('Forbidden', 403);
});


Route::get('/webhooks/whatsapp', [
    WhatsAppWebhookController::class,
    'verify',
]);

Route::post('/webhooks/whatsapp', [
    WhatsAppWebhookController::class,
    'receive',
]);