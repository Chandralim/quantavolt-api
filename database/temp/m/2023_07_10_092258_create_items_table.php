<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->string('code')->unique()->primary();
            $table->text('name');
            $table->string('unit_code');
            $table->foreign('unit_code')->references('code')->on('units')->onDelete('restrict')->onUpdate('cascade');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->float('size')->nullable();
            $table->string('color')->nullable();
            $table->integer('stock_min');
            $table->text('description')->nullable();
            $table->double('capital_price')->nullable();
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('items');
    }
}
