<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('server_user_ssh_key', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')->references('id')->on('servers');
            $table->foreignId('user_ssh_key_id')->references('id')->on('user_ssh_keys');

            $table->timestamps();
        });
    }
};
