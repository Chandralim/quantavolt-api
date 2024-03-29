<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalDataPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('internal.data_permissions');

        Schema::create('internal.data_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('table_name');
            $table->unsignedTinyInteger('ordinal');
            $table->string('field_name');
            $table->text('description');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal.data_permissions');
    }
}
