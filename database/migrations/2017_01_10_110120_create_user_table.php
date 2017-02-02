<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('fname');
                $table->string('lname');
                $table->string('mname')->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->float('salary')->nullable();
                $table->date('dob')->nullable();
                $table->date('doj')->nullable();
                $table->string('role')->default('USER');
                $table->string('blood_group')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('zipcode')->nullable();
                $table->string('address')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_relation')->nullable();
                $table->string('emergency_contact_number')->nullable();
                $table->rememberToken();
                $table->timestamps();
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
        Schema::drop('users');
    }
}
