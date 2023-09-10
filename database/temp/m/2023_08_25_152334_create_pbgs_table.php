<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pbgs', function (Blueprint $table) {
            $table->string('no')->primary();
            $table->date('date');
            $table->string('pag_no');
            $table->foreign('pag_no')->references('no')->on('pags')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('pbgs');
    }
}
