<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSshKeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ssh_keys', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id');

            $table->string('username');
            $table->string('private_key');
            $table->string('public_key');
            $table->string('private_key_path')->nullable();
            $table->string('public_key_path')->nullable();

            $table->timestamps();
        });
    }
}
