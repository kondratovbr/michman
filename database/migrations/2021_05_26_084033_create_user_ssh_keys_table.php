<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');

            $table->string('name');
            $table->text('public_key');

            $table->timestamps();
        });
    }
};
