<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrainingsToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->timestamp('dateEnd')->nullable();
            $table->enum('type', ['default', 'Training'])->default('default');
            $table->text('location')->nullable();
            $table->double('credit', 8, 2);
            $table->integer('credittype_id')->unsigned()->nullable();
            $table->integer('training_id')->unsigned()->nullable();

            $table->foreign('credittype_id')->references('id')->on('credittypes')->onDelete('cascade');
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            Schema::dropIfExists('dateEnd');
            Schema::dropIfExists('type');
            Schema::dropIfExists('location');
            Schema::dropIfExists('credit');
            Schema::dropIfExists('credittype_id');
            Schema::dropIfExists('training_id');
        });
    }
}
