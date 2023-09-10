<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pag_details', function (Blueprint $table) {
            $table->string('pag_no');
            $table->foreign('pag_no')->references('no')->on('pags')->onDelete('restrict')->onUpdate('cascade');
            $table->string('item_code');
            $table->foreign('item_code')->references('code')->on('items')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('qty');
            $table->integer('qty_used');
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
        Schema::dropIfExists('pag_details');
    }
}
