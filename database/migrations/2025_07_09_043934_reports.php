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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', ['transaction', 'membership', 'revenue', 'checkin']);
            $table->foreignId('generated_by')->constrained('users');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('generated_at')->useCurrent();
            $table->string('file_path', 255);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
