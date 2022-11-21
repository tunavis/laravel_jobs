<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_schedule_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('interview_schedule_id')->unsigned();

            $table->foreign('interview_schedule_id')->references('id')->on('interview_schedules')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->text('comment')->nullable()->default(null);
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
        Schema::dropIfExists('interview_schedule_comments');
    }
}
