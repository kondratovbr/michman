<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerUserSshKeyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('server_user_ssh_key', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('user_ssh_key_id');

            $table->timestamps();
        });
    }
}
