<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pags', function (Blueprint $table) {
            $table->string('no')->primary();
            $table->string('project_no')->nullable();
            $table->foreign('project_no')->nullable()->references('no')->on('projects')->onDelete('restrict')->onUpdate('cascade');
            $table->string('need')->nullable();
            $table->date('date');
            $table->string('part')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('pags');
    }
}
