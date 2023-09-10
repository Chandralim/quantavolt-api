<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->string('quotation_no');
            $table->foreign('quotation_no')->references('no')->on('quotations')->onDelete('restrict')->onUpdate('cascade');
            $table->string('quotation_item_code');
            $table->foreign('quotation_item_code')->references('code')->on('quotation_items')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedTinyInteger('ordinal');
            $table->integer('qty');
            $table->double('selling_price');
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
        Schema::table('quotation_details', function (Blueprint $table) {
            $table->dropForeign(['quotation_no']);
            $table->dropForeign(['quotation_item_code']);
        });
        Schema::dropIfExists('quotation_details');
    }
}
