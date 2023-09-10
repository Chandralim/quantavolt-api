<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePbgDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pbg_details', function (Blueprint $table) {
            $table->string('pbg_no');
            $table->foreign('pbg_no')->references('no')->on('pbgs')->onDelete('restrict')->onUpdate('cascade');
            $table->string('item_code');
            $table->foreign('item_code')->references('code')->on('items')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('qty');
            // $table->string('unit_code');
            // $table->foreign('unit_code')->references('code')->on('units')->onDelete('restrict')->onUpdate('cascade');
            $table->text('note')->nullable();
            $table->boolean('is_locked')->default(false);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pbg_details');
    }
}
