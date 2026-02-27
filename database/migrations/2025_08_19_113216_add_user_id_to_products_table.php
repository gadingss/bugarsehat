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
        Schema::table('products', function (Blueprint $table) {
            // Menambahkan kolom user_id sebagai foreign key ke tabel users
            $table->foreignId('user_id')
                  ->nullable() // Boleh kosong jika ada produk yang tidak terkait member
                  ->after('id') // Posisi kolom setelah 'id' (opsional)
                  ->constrained('users') // Membuat foreign key constraint ke tabel 'users'
                  ->onDelete('cascade'); // Jika user dihapus, produknya juga ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['user_id']);
            // Hapus kolomnya
            $table->dropColumn('user_id');
        });
    }
};