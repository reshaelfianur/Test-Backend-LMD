<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnLaratrustTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1 = Read And Write, 2 = Read Only, 3 = Not Allowed')->after('description');
            $table->unsignedInteger('submod_id')->after('type');

            $table->foreign('submod_id')->references('submod_id')->on('sub_modules')
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
        Schema::table('permissions', function ($table) {
            $table->dropColumn('type');
            
            $table->dropForeign(['submod_id']);
            $table->dropColumn('submod_id');
        });
    }
}
