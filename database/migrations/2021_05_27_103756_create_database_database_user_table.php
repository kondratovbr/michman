<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('database_database_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('database_id')->references('id')->on('databases');
            $table->foreignId('database_user_id')->references('id')->on('database_users');

            $table->timestamps();
        });
    }
};
