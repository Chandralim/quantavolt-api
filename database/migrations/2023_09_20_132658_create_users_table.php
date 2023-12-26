<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at');
            $table->id();
            $table->string('email', 255);
            $table->string('password');
            $table->string('phone_number', 30)->nullable();
            $table->string('api_token')->nullable();
            $table->string('sensor_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}


// Trial  2 sensor 3 hari  selama coba : 1 minggu
// Student 3 sensor 2 minggu perpanjang : 1 bulan warning 1 minggu 3 hari expired saat bulan berakhir
// Basic Bussiness 10 sensor 3 bulan perpanjang : 6 bulan warning 1minggu 3 hari note di hari h 2 hari. bisa lht monitoring tapi gkbisa kirim data
