<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('server_ssh_key_vcs_provider', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_ssh_key_id')->references('id')->on('server_ssh_keys');
            $table->foreignId('vcs_provider_id')->references('id')->on('vcs_providers');

            $table->string('external_id')->nullable();

            $table->timestamps();
        });
    }
};
