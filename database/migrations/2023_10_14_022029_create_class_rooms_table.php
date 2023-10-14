<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId('homeroom_teacher_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('institute_id')->references('id')->on('institutes')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('created_at')->nullable();
            $table->unsignedBigInteger('updated_at')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('internal_created_at')->nullable();
            $table->unsignedBigInteger('internal_updated_at')->nullable();
            $table->foreignId('internal_created_by')->nullable()->references('id')->on('internal.users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('internal_updated_by')->nullable()->references('id')->on('internal.users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_rooms');
    }
}
