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
        // Fields already added in membership_packages table creation migration
        // This migration is kept for compatibility but does nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_packages', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'image'
            ]);
        });
    }
};
