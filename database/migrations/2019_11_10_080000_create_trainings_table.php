<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('client_id')->unsigned(); 
            $table->LONGTEXT('content');
            $table->timestamp('date');
            $table->timestamp('dateEnd')->nullable()->default(null);
            $table->text('location')->nullable()->default(null);
            
            $table->timestamp('sendbydatetime')->nullable()->default(null);
            $table->timestamp('sended')->nullable()->default(null);
            $table->timestamps();
            
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainings');
    }
}
