<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_status_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider_message_id', 255);
            $table->string('status', 20);
            $table->timestamps();

            $table->unique(['provider_message_id', 'status'], 'whatsapp_event_message_status_unique');

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_status_events');
    }
};
