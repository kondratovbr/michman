<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daemons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')->references('id')->on('servers');

            $table->string('command', 1024 * 6);
            $table->string('username');
            $table->string('directory')->nullable();
            $table->integer('processes');
            $table->integer('start_seconds');
            $table->string('state');

            $table->timestamps();
        });
    }
};
