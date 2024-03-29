<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->references('id')->on('providers');

            $table->string('name');
            $table->string('type');
            // ID of a server given by the server provider.
            $table->string('external_id')->nullable();
            $table->string('region');
            $table->string('size');
            // IP can be null before the server is actually created by a provider.
            $table->string('public_ip')->nullable();
            // The default SSH port will be used if it is null.
            $table->string('ssh_port')->nullable();
            $table->string('ssh_host_key')->nullable();
            // Sudo password is stored encrypted.
            $table->text('sudo_password')->nullable();
            $table->boolean('suitable')->nullable();
            $table->boolean('available')->nullable();

            $table->string('installed_database')->nullable();
            $table->text('database_root_password')->nullable();
            $table->string('installed_cache')->nullable();

            $table->string('state');

            $table->timestamps();
        });
    }
};
