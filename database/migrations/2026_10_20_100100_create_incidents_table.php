<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Incidents (and their attendance lists) are kept for queries and insurance and are
     * only deleted by a manual action. Therefore the references to client, users and
     * stations are plain indexed ids WITHOUT foreign key: if one of them is deleted,
     * the id stays set and the statistic stays complete.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->index();
            $table->string('type')->default('einsatz');
            $table->string('title');
            $table->string('keyword')->nullable()->default(null);
            $table->text('location')->nullable()->default(null);
            $table->string('dispatch_number')->nullable()->default(null);
            $table->dateTime('start');
            $table->dateTime('end')->nullable()->default(null);
            $table->text('note')->nullable()->default(null);
            $table->integer('leader_user_id')->unsigned()->nullable()->default(null)->index();
            $table->integer('created_by')->unsigned()->nullable()->default(null);
            $table->dateTime('finalized_at')->nullable()->default(null);
            $table->integer('finalized_by')->unsigned()->nullable()->default(null);
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
        Schema::dropIfExists('incidents');
    }
}
