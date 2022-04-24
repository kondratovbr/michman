<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->references('id')->on('users');

            $table->string('provider');

            // Encrypted serialized token data
            $table->text('token');

            // Name is just for the user's convenience.
            $table->string('name')->nullable();
            
            $table->timestamps();
        });
    }
};
