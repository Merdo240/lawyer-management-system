<?php namespace App\Services\Notifications;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class HearingReminderValidator
{
    public function validate(NotificationLog $notification): bool
    {
        if (!in_array($notification->type, ['client_reminder', 'lawyer_reminder'], true)) {
            return false;
        }

        if (!$notification->session_id) {
            return $this->invalidate($notification, 'missing_session');
        }

        $session = DB::table('case_sessions as s')->join('legal_cases as c', 'c.id', '=', 's.case_id')->where('s.id', $notification->session_id)->select('s.id', 's.case_id', 's.scheduled_at', 's.status as session_status', 'c.status as case_status', 'c.title')->first();

        if (!$session) {
            return $this->invalidate($notification, 'session_deleted');
        }

        if ($session->session_status !== 'scheduled' || $session->case_status !== 'active') {
            return $this->invalidate($notification, 'hearing_cancelled');
        }

        $date = substr((string) $session->scheduled_at, 0, 10);

        // Reminders are sent one day before the hearing.
        if ($date !== today()->addDay()->toDateString()) {
            return $this->invalidate($notification, 'hearing_date_changed');
        }

        $clients = DB::table('case_client as cc')->join('clients as cl', 'cl.id', '=', 'cc.client_id')->where('cc.case_id', $session->case_id)->select('cl.id', 'cl.name', 'cl.whatsapp_number')->orderBy('cl.id')->get();

        if ($notification->type === 'client_reminder') {
            $parts = explode(':', (string) $notification->idempotency_key);

            if (count($parts) !== 4 || $parts[0] !== 'client' || !ctype_digit($parts[1]) || !ctype_digit($parts[2]) || (int) $parts[1] !== (int) $session->id || $parts[3] !== $date) {
                return $this->invalidate($notification, 'invalid_client_key');
            }

            $client = $clients->first(fn($item) => (int) $item->id === (int) $parts[2]);

            if (!$client) {
                return $this->invalidate($notification, 'client_removed');
            }

            $number = trim((string) $client->whatsapp_number);

            if ($number === '') {
                return $this->invalidate($notification, 'missing_client_number');
            }

            $parameters = [$client->name, $date];
            $template = 'client';
        } else {
            $expectedKey = "lawyer:{$session->id}:{$date}";

            if ($notification->idempotency_key !== $expectedKey) {
                return $this->invalidate($notification, 'invalid_lawyer_key');
            }

            $number = trim((string) config('services.lawyer.whatsapp_number', ''));

            if ($number === '') {
                return $this->invalidate($notification, 'missing_lawyer_number');
            }

            $names = $clients->pluck('name')->filter()->implode('، ');

            $parameters = [$session->title, $names ?: 'غير محدد', $date];

            $template = 'lawyer';
        }

        // Refresh the recipient and message using current database values.
        $notification->update([
            'recipient_number' => $number,
            'message' => json_encode(
                [
                    'template' => $template,
                    'parameters' => $parameters,
                ],
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            ),
        ]);

        return true;
    }

    private function invalidate(NotificationLog $notification, string $reason): bool
    {
        $notification->update([
            'status' => 'failed',
            'failure_code' => $reason,
            'error_message' => 'Reminder invalidated before sending.',
        ]);

        return false;
    }
}
