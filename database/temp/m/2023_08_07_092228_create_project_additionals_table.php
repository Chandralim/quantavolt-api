<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_additionals', function (Blueprint $table) {
            $table->string('project_no');
            $table->foreign('project_no')->references('no')->on('projects')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedTinyInteger('ordinal');
            $table->string('name');
            $table->integer('qty_assumption');
            $table->integer('qty_realization')->nullable();
            $table->string('unit_code');
            $table->foreign('unit_code')->references('code')->on('units')->onDelete('restrict')->onUpdate('cascade');
            $table->double('price_assumption')->nullable();
            $table->double('price_realization')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->boolean('is_locked')->default(false);
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
        Schema::table('project_additionals', function (Blueprint $table) {
            $table->dropForeign(['project_no']);
            $table->dropForeign(['unit_code']);
        });
        Schema::dropIfExists('project_additionals');
    }
}
