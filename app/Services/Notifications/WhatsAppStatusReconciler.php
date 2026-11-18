<?php namespace App\Services\Notifications;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class WhatsAppStatusReconciler
{
    private const RANK = [
        'accepted' => 0,
        'sent' => 1,
        'delivered' => 2,
        'read' => 3,
    ];

    public function record(string $messageId, string $status): void
    {
        if ($messageId === '' || !in_array($status, ['sent', 'delivered', 'read', 'failed'], true)) {
            return;
        }

        DB::transaction(function () use ($messageId, $status) {
            DB::table('whatsapp_status_events')->insertOrIgnore([
                'provider_message_id' => $messageId,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->reconcileInsideTransaction($messageId);
        }, 3);
    }

    public function reconcile(string $messageId): void
    {
        if ($messageId === '') {
            return;
        }

        DB::transaction(function () use ($messageId) {
            $this->reconcileInsideTransaction($messageId);
        }, 3);
    }

    private function reconcileInsideTransaction(string $messageId): void
    {
        $notification = NotificationLog::query()->where('provider_message_id', $messageId)->lockForUpdate()->first();

        if (!$notification) {
            return;
        }

        $events = DB::table('whatsapp_status_events')->where('provider_message_id', $messageId)->pluck('status')->all();

        $current = $notification->status;

        if ($current === 'failed') {
            return;
        }

        $bestRank = self::RANK[$current] ?? -1;
        $bestStatus = $current;

        foreach ($events as $event) {
            $rank = self::RANK[$event] ?? -1;

            if ($rank > $bestRank) {
                $bestRank = $rank;
                $bestStatus = $event;
            }
        }

        if ($bestStatus !== $current && $bestRank >= 0) {
            $notification->update([
                'status' => $bestStatus,
                'sent_at' => $notification->sent_at ?? now(),
                'failure_code' => null,
                'error_message' => null,
            ]);
        }

        // A delivery failure is only terminal if delivery
        // or reading has not already been confirmed.
        if (in_array('failed', $events, true) && !in_array($notification->status, ['delivered', 'read'], true) && array_key_exists($notification->status, self::RANK)) {
            $notification->update([
                'status' => 'failed',
                'failure_code' => 'meta_delivery_failed',
                'error_message' => 'Meta reported delivery failure.',
            ]);
        }
    }
}
