<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_information', function (Blueprint $table) {
          $table->increments('id');
          $table->dateTime('created_at')->nullable();
          $table->dateTime('updated_at')->nullable();
          $table->LONGTEXT('content');
          $table->integer('user_id')->unsigned()->nullable();
          $table->integer('client_id')->unsigned();
          $table->integer('service_id')->unsigned();
        
          $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
          $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
          $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_information');
    }
};
