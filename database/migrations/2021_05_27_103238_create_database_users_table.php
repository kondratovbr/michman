<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('database_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->string('name');
            // Password is stored encrypted and only temporarily - while the user is being created on a server.
            $table->text('password')->nullable();
            $table->unsignedInteger('tasks')->default(0);

            $table->timestamps();
        });
    }
}
