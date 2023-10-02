<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_institutes', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->references('id')->on('members')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('institute_id')->nullable()->references('id')->on('institutes')->onDelete('restrict')->onUpdate('cascade');
            $table->string('role', 50);
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
        Schema::dropIfExists('member_institutes');
    }
}
