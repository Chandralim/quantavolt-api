<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassRoomMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_room_members', function (Blueprint $table) {
            $table->foreignId('class_room_id')->references('id')->on('class_rooms')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('class_room_members');
    }
}
