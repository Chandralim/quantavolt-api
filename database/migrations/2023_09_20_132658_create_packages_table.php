<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->unsignedBigInteger('internal_created_at')->nullable();
            $table->foreignId('internal_created_by')->nullable()->references('id')->on('internal.users')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('internal_updated_at')->nullable();
            $table->foreignId('internal_updated_by')->nullable()->references('id')->on('internal.users')->onDelete('restrict')->onUpdate('cascade');
            $table->id();
            $table->string('name'); // trial , student, bussiness
            $table->smallInteger('sensor_limit'); // 2 , 3 , 10
            $table->string('record_periode_limit'); // 3 days, 2 weeks, 3 months
            $table->string('expired_time'); // 1 week , 1 month, 6 months
            $table->double('price'); // 1 week , 1 month, 6 months
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}


// Trial  2 sensor 3 hari  selama coba : 1 minggu
// Student 3 sensor 2 minggu perpanjang : 1 bulan warning 1 minggu 3 hari expired saat bulan berakhir
// Basic Bussiness 10 sensor 3 bulan perpanjang : 6 bulan warning 1minggu 3 hari note di hari h 2 hari. bisa lht monitoring tapi gkbisa kirim data

// hitung pemakaian sensor per detik * rp