<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploymentServerTable extends Migration
{
    public function up()
    {
        Schema::create('deployment_server', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deployment_id')->references('id')->on('deployments');
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->boolean('successful')->nullable();

            $table->timestamps();
        });
    }
}
