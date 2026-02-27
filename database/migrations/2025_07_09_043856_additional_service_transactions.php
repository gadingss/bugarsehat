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
        Schema::create('additional_service_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('service_id')->constrained('additional_services');
            $table->timestamp('transaction_time')->useCurrent();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->datetime('scheduled_date')->nullable();
            $table->datetime('completed_date')->nullable();
            $table->enum('status', ['paid', 'pending', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_service_transactions');
    }
};
