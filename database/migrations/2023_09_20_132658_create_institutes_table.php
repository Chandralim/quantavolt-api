<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->unsignedBigInteger('internal_created_at')->nullable();
            $table->unsignedBigInteger('internal_updated_at')->nullable();
            $table->id();
            $table->string('link_name')->unique();
            $table->string('name')->unique();
            $table->text('address');
            $table->string('contact_number', 20);
            $table->string('contact_person', 50);
            $table->unsignedBigInteger('active_until')->nullable();
            $table->foreignId('internal_marketer_by')->references('id')->on('internal.users')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('institutes');
    }
}
