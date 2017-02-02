<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_present_log')) {
            Schema::create('user_present_log', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('action_type')->nullable();
                $table->dateTime('action_time')->nullable();
                $table->dateTime('status')->default('PENDING');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_present_log');
    }
}
