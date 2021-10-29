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

            // For token-based authentication (OAuth 2.0).
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();

            // For key/secret authentication (OAuth 1.0) in case some API still uses it.
            $table->text('key')->nullable();
            $table->text('secret')->nullable();

            $table->timestamps();
        });
    }
}
