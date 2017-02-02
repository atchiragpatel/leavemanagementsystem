<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('leave_transaction')) {
            Schema::create('leave_transaction', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('leave_type')->nullable();
                $table->float('value')->nullable();
                $table->dateTime('from_date')->nullable();
                $table->dateTime('to_date')->nullable();
                $table->string('type')->nullable();
                $table->string('status')->nullable();
                $table->string('ledger')->nullable();
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
        Schema::drop('leave_transaction');
    }
}
