<?php namespace App\Http\Controllers;

use App\Services\Notifications\WhatsAppStatusReconciler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $token = config('services.whatsapp.verify_token');

        if (!is_string($token) || $token === '' || $request->query('hub_mode') !== 'subscribe' || !hash_equals($token, (string) $request->query('hub_verify_token', ''))) {
            return response('Forbidden', 403);
        }

        return response((string) $request->query('hub_challenge', ''), 200);
    }

    public function receive(Request $request, WhatsAppStatusReconciler $reconciler): Response
    {
        $secret = config('services.whatsapp.app_secret');

        if (!is_string($secret) || $secret === '') {
            return response('Webhook not configured', 503);
        }

        $signature = $request->header('X-Hub-Signature-256', '');

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (!is_string($signature) || !hash_equals($expected, $signature)) {
            return response('Invalid signature', 403);
        }

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return response('Invalid payload', 400);
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            foreach ($entry['changes'] ?? [] as $change) {
                if (!is_array($change)) {
                    continue;
                }

                $statuses = $change['value']['statuses'] ?? [];

                if (!is_array($statuses)) {
                    continue;
                }

                foreach ($statuses as $event) {
                    if (!is_array($event)) {
                        continue;
                    }

                    $messageId = $event['id'] ?? null;
                    $status = $event['status'] ?? null;

                    if (!is_string($messageId) || !is_string($status)) {
                        continue;
                    }

                    $reconciler->record($messageId, $status);
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }
}
