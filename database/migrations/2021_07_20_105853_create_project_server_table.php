<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_server', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')->references('id')->on('servers');
            $table->foreignId('project_id')->references('id')->on('projects');

            $table->timestamps();
        });
    }
};
