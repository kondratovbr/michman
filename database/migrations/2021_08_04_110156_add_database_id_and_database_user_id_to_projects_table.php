<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatabaseIdAndDatabaseUserIdToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('database_id')->nullable()->references('id')->on('databases');
            $table->foreignId('database_user_id')->nullable()->references('id')->on('database_users');
        });
    }
}
