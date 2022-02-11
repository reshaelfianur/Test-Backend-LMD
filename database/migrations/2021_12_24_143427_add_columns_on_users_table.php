<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('grade_from_id')->nullable()->comment('Grade From')->after('user_active_pin');
            $table->unsignedInteger('grade_to_id')->nullable()->comment('Grade To')->after('grade_from_id');
            $table->unsignedInteger('loc_id')->nullable()->comment('Location')->after('grade_to_id');
            $table->dateTime('user_active_date')->nullable()->after('loc_id');
            $table->dateTime('user_inactive_date')->nullable()->after('user_active_date');
            $table->tinyInteger('user_type')->default(4)->comment('1 = Axia User, 2 = User Admin, 3 = Super User, 4 = User')->after('user_inactive_date');
            $table->integer('created_by')->nullable()->after('created_at');
            $table->integer('updated_by')->nullable()->after('updated_at');
            $table->integer('deleted_by')->nullable()->after('deleted_at');

            $table->foreign('grade_from_id')->references('grade_id')->on('grades')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('grade_to_id')->references('grade_id')->on('grades')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('loc_id')->references('loc_id')->on('location')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
