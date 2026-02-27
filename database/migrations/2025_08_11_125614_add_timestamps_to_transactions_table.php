<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Perintah ini akan membuat kolom created_at dan updated_at
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika di-rollback
            $table->dropTimestamps();
        });
    }
};