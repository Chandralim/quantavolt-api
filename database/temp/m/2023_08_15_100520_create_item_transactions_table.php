<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->string('item_code');
            $table->foreign('item_code')->references('code')->on('items')->onDelete('restrict')->onUpdate('cascade');
            $table->date('date');
            $table->text('note');
            $table->integer('in');
            $table->integer('out');
            $table->integer('reminder');
            $table->integer('no_ref');
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
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->dropForeign(['item_code']);
        });
        Schema::dropIfExists('item_transactions');
    }
}
