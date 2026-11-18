<?php namespace App\Services\Notifications;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class NotificationSender
{
    public function __construct(private NotificationProviderReadiness $readiness, private WhatsAppTemplateSender $templateSender, private WhatsAppStatusReconciler $reconciler) {}

    public function send(NotificationLog $notification): bool
    {
        if ($notification->status !== 'processing') {
            return false;
        }

        if (!$this->readiness->isReady()) {
            return false;
        }

        $payload = json_decode((string) $notification->message, true);

        if (!is_array($payload) || !isset($payload['template'], $payload['parameters']) || !is_array($payload['parameters'])) {
            $this->saveFailure($notification, 'invalid_payload', 'Notification payload is invalid.');

            return false;
        }

        $templateName = match ($payload['template']) {
            'client' => config('services.whatsapp.client_template'),
            'lawyer' => config('services.whatsapp.lawyer_template'),
            default => null,
        };

        $expectedCount = match ($payload['template']) {
            'client' => 2,
            'lawyer' => 3,
            default => 0,
        };

        if (!is_string($templateName) || trim($templateName) === '' || count($payload['parameters']) !== $expectedCount || count(array_filter($payload['parameters'], fn($value) => is_string($value) || is_numeric($value))) !== $expectedCount) {
            $this->saveFailure($notification, 'invalid_template', 'Notification template is invalid.');

            return false;
        }

        $result = $this->templateSender->send($notification->recipient_number, $templateName, array_values($payload['parameters']));

        $updated = DB::transaction(function () use ($notification, $result) {
            $updated = NotificationLog::query()
                ->whereKey($notification->id)
                ->where('status', 'processing')
                ->where('processing_started_at', $notification->processing_started_at)
                ->update([
                    'status' => $result->status,
                    'provider_message_id' => $result->providerMessageId,
                    'failure_code' => $result->failureCode,
                    'error_message' => $result->errorMessage,
                    'processing_started_at' => null,
                ]);

            if ($updated === 1 && $result->status === 'accepted' && is_string($result->providerMessageId) && $result->providerMessageId !== '') {
                $this->reconciler->reconcile($result->providerMessageId);
            }

            return $updated;
        }, 3);

        return $updated === 1 && $result->status === 'accepted';
    }

    private function saveFailure(NotificationLog $notification, string $code, string $message): void
    {
        NotificationLog::query()
            ->whereKey($notification->id)
            ->where('status', 'processing')
            ->where('processing_started_at', $notification->processing_started_at)
            ->update([
                'status' => 'failed',
                'failure_code' => $code,
                'error_message' => $message,
                'processing_started_at' => null,
            ]);
    }
}
