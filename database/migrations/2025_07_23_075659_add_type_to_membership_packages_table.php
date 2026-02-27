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
        Schema::table('membership_packages', function (Blueprint $table) {
            // Tambahkan kolom 'type' jika belum ada
            if (!Schema::hasColumn('membership_packages', 'type')) {
                $table->string('type')->default('paket')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_packages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
