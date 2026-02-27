<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('amount');
            $table->timestamps(); // untuk created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
