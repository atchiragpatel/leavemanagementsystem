<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreeateRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('request')) {
            Schema::create('request', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('leave_type')->nullable();
                $table->string('value')->nullable();
                $table->string('txn_type')->nullable();
                $table->dateTime('from_date')->nullable();
                $table->dateTime('to_date')->nullable();
                $table->string('status')->default('REQUESTED');
                $table->text('user_comments')->nullable();
                $table->text('manager_comments')->nullable();
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
        Schema::drop('request');
    }
}
