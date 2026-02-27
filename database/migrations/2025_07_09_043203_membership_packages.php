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
        Schema::create('membership_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->integer('max_visits');
            $table->boolean('is_publish')->default(False);
            $table->text('description')->nullable();

            $table->text('name_label')->nullable();
            $table->text('usage')->nullable();

            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_packages');
    }
};
