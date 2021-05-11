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
            $table->text('token')->nullable();
            $table->text('key')->nullable();
            $table->text('secret')->nullable();

            // Name is just for the user's convenience.
            $table->string('name')->nullable();
            
            $table->timestamps();
        });
    }
}
