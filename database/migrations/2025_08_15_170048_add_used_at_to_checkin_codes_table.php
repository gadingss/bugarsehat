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
        // Perintah untuk menambahkan kolom ke tabel yang sudah ada
        Schema::table('checkin_codes', function (Blueprint $table) {
            // Menambahkan kolom 'used_at' setelah kolom 'expires_at'
            // nullable() berarti kolom ini boleh kosong
            $table->timestamp('used_at')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_codes', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('used_at');
        });
    }
};