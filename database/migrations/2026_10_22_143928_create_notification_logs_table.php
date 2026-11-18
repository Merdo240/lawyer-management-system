<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('session_id')
                ->nullable()
                ->constrained('case_sessions')
                ->nullOnDelete();

            $table->string('recipient_type');
            $table->string('recipient_number', 20);
            $table->date('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending');
            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique([
                'session_id',
                'recipient_type',
                'recipient_number',
                'scheduled_at',
            ], 'notification_logs_unique_reminder');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
