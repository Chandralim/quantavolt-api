<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('action_permission_id')->nullable()->references('id')->on('action_permissions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('data_permission_id')->nullable()->references('id')->on('data_permissions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['action_permission_id']);
            $table->dropForeign(['data_permission_id']);
            $table->dropForeign(['created_by']);
            // $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('user_permissions');
    }
}
