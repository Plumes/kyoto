<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('nickname', 128);
            $table->string('avatar',256);
            $table->unsignedTinyInteger('gender');
            $table->string('password',128);
            $table->string('encrypt',8);
            $table->unsignedTinyInteger('account_status')->default(0);;
            $table->string('email')->nullable();
            $table->string('introduction')->nullable()->comment('用户自我介绍');
            $table->timestamp('last_login_time')->nullable()->comment('用户上次登陆时间');
            $table->string('api_token')->nullable();
            $table->timestamp('api_token_expire_time')->nullable();

            $table->timestamps();

            $table->index('api_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('users');
    }
}
