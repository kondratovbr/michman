<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcsProvidersTable extends Migration
{
    public function up()
    {
        Schema::create('vcs_providers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->references('id')->on('users');

            $table->string('provider');

            $table->string('external_id');
            $table->string('nickname');

            // Encrypted serialized token data
            $table->text('token');

            $table->timestamps();
        });
    }
}
