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
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('user_fullname');
            $table->string('user_email');
            $table->string('username');
            $table->text('password');
            $table->tinyInteger('user_need_change_password')->default('1')->comment('1 = Yes, 2 = No');
            $table->tinyInteger('user_status')->default('1')->comment('1 = Active, 2 = Inactive');
            $table->dateTime('user_last_login')->nullable()->default(null);
            $table->dateTime('user_last_reset')->nullable()->default(null);
            $table->dateTime('user_last_lock')->nullable()->default(null);
            $table->dateTime('user_last_change_password')->nullable()->default(null);
            $table->text('api_token')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
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
