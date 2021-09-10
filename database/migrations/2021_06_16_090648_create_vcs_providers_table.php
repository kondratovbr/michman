<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcsProvidersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vcs_providers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->references('id')->on('users');

            $table->string('provider');

            $table->string('external_id');
            $table->string('nickname');
            // Some APIs use token-based authentication and some use key/secret.
            $table->text('token')->nullable();
            $table->text('key')->nullable();
            $table->text('secret')->nullable();

            $table->timestamps();
        });
    }
}
