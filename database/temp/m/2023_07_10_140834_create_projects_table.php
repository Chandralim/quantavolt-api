<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->string('no')->unique()->primary();
            $table->string('title');
            $table->date('date');
            $table->string('location');
            $table->string('customer_code');
            $table->foreign('customer_code')->references('code')->on('customers')->onDelete('restrict')->onUpdate('cascade');
            $table->string('type');
            $table->date('date_start');
            $table->date('date_finish')->nullable();
            $table->string('status');
            $table->text('note')->nullable();
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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['customer_code']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('projects');
    }
}
