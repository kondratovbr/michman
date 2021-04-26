<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id');

            $table->string('name');
            $table->string('type');
            // ID of a server given by the server provider.
            $table->string('external_id')->nullable();
            // IP can be null before the server is actually created by a provider.
            $table->string('ip')->nullable();

            $table->timestamps();
        });
    }
}
