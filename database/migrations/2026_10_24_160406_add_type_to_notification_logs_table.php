
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->string('type')
                ->default('client_reminder')
                ->after('session_id');

            $table->string('idempotency_key')
                ->nullable()
                ->unique()
                ->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn(['type', 'idempotency_key']);
        });
    }
};
