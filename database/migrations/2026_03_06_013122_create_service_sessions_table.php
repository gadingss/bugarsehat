<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_transaction_id')->constrained('additional_service_transactions')->onDelete('cascade');
            $table->integer('session_number');
            $table->string('topic')->nullable();
            $table->dateTime('scheduled_date')->nullable();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'attended', 'missed'])->default('pending');
            $table->foreignId('checkin_id')->nullable()->constrained('checkin_logs')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_sessions');
    }
};
