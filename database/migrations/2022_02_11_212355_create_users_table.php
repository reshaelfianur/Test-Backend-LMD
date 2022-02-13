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
            $table->increments('user_id');
            $table->string('email', 50);
            $table->string('username', 25);
            $table->text('password');
            $table->string('user_full_name');
            $table->tinyInteger('user_need_change_password')->default('1')->comment('1 = Yes, 2 = No')->default(2);
            $table->tinyInteger('user_status')->comment('1 = Active, 2 = Inactive')->default(1);
            $table->tinyInteger('user_type')->default(2)->comment('1 = Admin, 2 = User');
            $table->date('user_active_date')->nullable();
            $table->date('user_inactive_date')->nullable();
            $table->dateTime('user_last_login')->nullable()->default(null);
            $table->dateTime('user_last_lock')->nullable()->default(null);
            $table->dateTime('user_last_reset_password')->nullable()->default(null);
            $table->dateTime('user_last_change_password')->nullable()->default(null);
            $table->string('user_notes', 225)->nullable()->default(null);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->rememberToken();
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
