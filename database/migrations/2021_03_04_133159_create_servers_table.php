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
            // ID of a server given by the server provider.
            $table->string('external_id');
            $table->string('ip');

            $table->timestamps();
        });
    }
}
