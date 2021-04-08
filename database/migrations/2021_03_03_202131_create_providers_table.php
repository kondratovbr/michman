<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');

            $table->string('provider');

            // Some APIs use token-based authentication and some use key/secret.
            $table->string('token')->nullable();
            $table->string('key')->nullable();
            $table->string('secret')->nullable();

            // Name is just for the user's convenience.
            $table->string('name')->nullable();

            $table->timestamps();

            // One user cannot add the same token or key multiple times.
            $table->unique(['user_id', 'token']);
            $table->unique(['user_id', 'key']);
        });
    }
}
