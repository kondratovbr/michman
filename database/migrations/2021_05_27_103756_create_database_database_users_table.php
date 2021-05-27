<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseDatabaseUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('database_database_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('database_id');
            $table->foreignId('database_user_id');

            $table->timestamps();
        });
    }
}
