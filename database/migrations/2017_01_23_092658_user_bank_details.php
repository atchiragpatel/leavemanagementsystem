<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bank')) {
            Schema::create('bank', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('bank_name');
                $table->string('branch_name');
                $table->string('account_number');
                $table->string('ifsc_code');
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
        Schema::drop('bank');
    }
}
