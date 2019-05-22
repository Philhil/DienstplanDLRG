<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->date('seasonStart')->default("2000-01-01");
            $table->boolean('isMailinglistCommunication')->default(false);
            $table->boolean('weeklyServiceviewEmail')->default(true);
            $table->string('mailinglistAddress')->nullable()->default(NULL);
            $table->string('mailSenderName');
            $table->string('mailReplyAddress');
            $table->time('defaultServiceStart')->default("10:00");
            $table->time('defaultServiceEnd')->default("19:00");
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
        Schema::dropIfExists('clients');
    }
}
