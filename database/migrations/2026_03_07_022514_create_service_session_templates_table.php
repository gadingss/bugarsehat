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
        Schema::create('service_session_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('additional_services')->onDelete('cascade');
            $table->integer('session_number');
            $table->string('topic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_session_templates');
    }
};
