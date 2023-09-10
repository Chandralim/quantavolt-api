<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOtherCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_other_costs', function (Blueprint $table) {
            $table->string('project_no');
            $table->foreign('project_no')->references('no')->on('projects')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedTinyInteger('ordinal');
            $table->string('title');
            $table->integer('qty_realization');
            $table->integer('qty_assumption');
            $table->string('unit_code');
            $table->foreign('unit_code')->references('code')->on('units')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('stock_assumption');
            $table->double('price_assumption');
            $table->double('price_realization');
            $table->text('note');
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
        Schema::table('project_other_costs', function (Blueprint $table) {
            $table->dropForeign(['project_no']);
            $table->dropForeign(['unit_code']);
        });
        Schema::dropIfExists('project_other_costs');
    }
}
