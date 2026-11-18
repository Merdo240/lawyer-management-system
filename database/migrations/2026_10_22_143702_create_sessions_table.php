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
        Schema::create('case_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('legal_cases')
                ->cascadeOnDelete();

            $table->date('scheduled_at');
            $table->text('details')->nullable();
            $table->string('status')->default('scheduled');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_sessions');
    }
};
