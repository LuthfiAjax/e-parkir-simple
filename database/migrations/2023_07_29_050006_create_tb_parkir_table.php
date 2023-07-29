<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbParkirTable extends Migration
{
    
    public function up()
    {
        Schema::create('tb_parkir', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unicode', 10);
            $table->string('nopol', 15);
            $table->Integer('clock_in')->nullable();
            $table->Integer('clock_out')->nullable();
            $table->Integer('price')->nullable();
            $table->Integer('status')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('tb_parkir');
    }
}
