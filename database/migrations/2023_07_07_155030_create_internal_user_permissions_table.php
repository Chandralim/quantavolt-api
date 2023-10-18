<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('internal.user_permissions');

        Schema::create('internal.user_permissions', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('internal.users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('action_permission_id')->nullable()->references('id')->on('internal.action_permissions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('data_permission_id')->nullable()->references('id')->on('internal.data_permissions')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('internal_created_at')->nullable();
            $table->foreignId('internal_created_by')->nullable()->references('id')->on('internal.users')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            // $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('internal.user_permissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['action_permission_id']);
            $table->dropForeign(['data_permission_id']);
            $table->dropForeign(['created_by']);
            // $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('internal.user_permissions');
    }
}
