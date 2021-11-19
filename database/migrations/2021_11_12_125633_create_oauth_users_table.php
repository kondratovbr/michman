<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthUsersTable extends Migration
{
    public function up()
    {
        Schema::create('oauth_users', function (Blueprint $table) {
            $table->id();

            $table->string('provider');
            $table->string('oauth_id');
            $table->string('nickname');

            $table->foreignId('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }
}
