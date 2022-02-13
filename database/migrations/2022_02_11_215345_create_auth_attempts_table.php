<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_attempts', function (Blueprint $table) {
            $table->string('ip_address', 64)->nullable();
            $table->string('captcha', 64)->index();
            $table->text('user_agent')->nullable();
            $table->integer('attempt');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_attempts');
    }
}
