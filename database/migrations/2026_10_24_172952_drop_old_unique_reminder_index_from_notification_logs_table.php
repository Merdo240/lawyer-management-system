
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->index('session_id', 'notification_logs_session_id_index');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropUnique('notification_logs_unique_reminder');
        });
    }

    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->unique(
                [
                    'session_id',
                    'recipient_type',
                    'recipient_number',
                    'scheduled_at',
                ],
                'notification_logs_unique_reminder'
            );
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropIndex('notification_logs_session_id_index');
        });
    }
};
