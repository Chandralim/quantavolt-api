<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_commissions', function (Blueprint $table) {
            $table->string('project_no');
            $table->foreign('project_no')->references('no')->on('projects')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedTinyInteger('ordinal');
            $table->string('title');
            $table->double('price');
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
        Schema::table('project_commissions', function (Blueprint $table) {
            $table->dropForeign(['project_no']);
        });
        Schema::dropIfExists('project_commissions');
    }
}
