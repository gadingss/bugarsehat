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
        Schema::table('additional_service_transactions', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('amount');
            $table->foreignId('validated_by')->nullable()->constrained('users')->after('status');
            $table->timestamp('validated_at')->nullable()->after('validated_by');

            // Rename transaction_time to transaction_date to match model
            if (Schema::hasColumn('additional_service_transactions', 'transaction_time')) {
                $table->renameColumn('transaction_time', 'transaction_date');
            }
        });

        // Change status from enum to string for more flexibility
        Schema::table('additional_service_transactions', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_service_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('additional_service_transactions', 'transaction_date')) {
                $table->renameColumn('transaction_date', 'transaction_time');
            }

            $table->dropForeign(['validated_by']);
            $table->dropColumn(['payment_proof', 'validated_by', 'validated_at']);
        });

        // Reverting status to enum might be tricky if new values exist
        // So we'll leave it as string or revert back to the original enum if possible
    }
};
