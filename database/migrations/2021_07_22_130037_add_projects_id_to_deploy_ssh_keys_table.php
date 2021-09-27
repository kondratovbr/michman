<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectsIdToDeploySshKeysTable extends Migration
{
    public function up()
    {
        Schema::table('deploy_ssh_keys', function (Blueprint $table) {
            $table->foreignId('project_id')->references('id')->on('projects');
        });
    }
}
