<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_api_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('created_at');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->string('api_token');
            $table->string('ip_address');
            // $table->text('device_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_api_tokens');
    }
}
