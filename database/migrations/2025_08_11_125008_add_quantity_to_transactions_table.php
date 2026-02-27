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
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom 'product_type' terlebih dahulu
            // Kita letakkan setelah kolom 'product_id' (asumsi kolom ini ada)
            $table->string('product_type')->nullable()->after('product_id');

            // Sekarang, tambahkan kolom 'quantity' setelah 'product_type'
            $table->integer('quantity')->default(1)->after('product_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus kolom dalam urutan terbalik jika di-rollback
            $table->dropColumn(['quantity', 'product_type']);
        });
    }
};