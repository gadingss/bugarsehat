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
        Schema::create('membership_packet_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membership_packet_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->foreign('membership_packet_id')->references('id')->on('membership_packages')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('additional_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_packet_services');
    }
};
