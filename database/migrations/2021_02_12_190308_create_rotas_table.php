<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotasTable extends Migration
{

    public function up()
    {
        Schema::create('rotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->double('time_calc')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('rotas');
    }
}
