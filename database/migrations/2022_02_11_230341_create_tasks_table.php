<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('task_id');
            $table->unsignedInteger('user_id');
            $table->string('task_title', 50);
            $table->string('task_description', 255);
            $table->tinyInteger('task_status')->comment('1 = Active, 2 = Inactive')->default(1);
            $table->float('task_hours');
            $table->dateTime('task_planned_start_date')->nullable();
            $table->dateTime('task_planned_end_date')->nullable();
            $table->dateTime('task_actual_start_date')->nullable();
            $table->dateTime('task_actual_end_date')->nullable();
            $table->text('task_notes')->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

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
        Schema::dropIfExists('tasks');
    }
}
