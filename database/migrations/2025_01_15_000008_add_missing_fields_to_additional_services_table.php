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
        // Fields already added in additional_services table creation migration
        // This migration is kept for compatibility but does nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_services', function (Blueprint $table) {
            $table->dropColumn([
                'duration_minutes',
                'category',
                'requires_booking',
                'max_participants',
                'image'
            ]);
        });
    }
};
