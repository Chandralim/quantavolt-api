<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_workers', function (Blueprint $table) {
            $table->string('project_no');
            $table->foreign('project_no')->references('no')->on('projects')->onDelete('restrict')->onUpdate('cascade');

            $table->unsignedTinyInteger('ordinal');

            $table->foreignId('employee_no')->nullable()->references('no')->on('employees')->onDelete('restrict')->onUpdate('cascade');
            $table->string('fullname');

            $table->string('type');
            $table->string('working_day');
            $table->double('cost');
            $table->string('day_realization')->nullable();
            $table->double('price_realization')->nullable();
            $table->text('note')->nullable();

            $table->foreignId('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('project_workers', function (Blueprint $table) {
            $table->dropForeign(['project_no']);
            $table->dropForeign(['employee_no']);
        });
        Schema::dropIfExists('project_workers');
    }
}
