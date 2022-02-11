<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_modules', function (Blueprint $table) {
            $table->increments('am_id');
            $table->unsignedInteger('mod_id');
            $table->unsignedBigInteger('role_id');
            $table->tinyInteger('am_rights')->default('2')->comment('1 = Yes, 2 = No');

            $table->timestamps();

            $table->foreign('mod_id')->references('mod_id')->on('modules')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('role_id')->references('id')->on('roles')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_modules');
    }
}
