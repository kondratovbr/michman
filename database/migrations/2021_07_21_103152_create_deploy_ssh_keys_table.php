<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploySshKeysTable extends Migration
{
    public function up()
    {
        Schema::create('deploy_ssh_keys', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->references('id')->on('projects');

            $table->text('public_key');
            $table->text('private_key');

            $table->timestamps();
        });
    }
}
