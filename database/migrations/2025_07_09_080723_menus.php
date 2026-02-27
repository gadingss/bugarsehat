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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->unsignedBigInteger('sub_id')->nullable();
            $table->string('type')->nullable();// label, menu, sub-menu
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('order')->default(0);
            $table->boolean('shortkey')->default(false);
            $table->boolean('is_permission')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
