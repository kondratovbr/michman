<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('server_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->string('name');
            $table->text('public_key');
            $table->text('private_key');

            $table->timestamps();
        });
    }
};
