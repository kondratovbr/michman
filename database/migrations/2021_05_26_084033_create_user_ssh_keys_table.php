<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSshKeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->string('username');
            $table->text('public_key');
            $table->string('name');

            $table->timestamps();
        });
    }
}
