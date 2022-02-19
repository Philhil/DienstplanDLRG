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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->string('title');
            $table->LONGTEXT('content');
            $table->dateTime('dateStart')->nullable()->default(null);
            $table->dateTime('dateEnd')->nullable()->default(null);
            $table->boolean('mandatory')->default(true);
            $table->boolean('passwordConfirmationRequired')->default(false);

            $table->integer('qualification_id')->unsigned()->nullable();
            $table->foreign('qualification_id')->references('id')->on('qualifications');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveys');
    }
};
