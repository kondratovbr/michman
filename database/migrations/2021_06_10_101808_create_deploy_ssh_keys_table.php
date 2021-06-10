<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploySshKeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deploy_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->text('public_key');
            $table->text('private_key');

            $table->timestamps();
        });
    }
}