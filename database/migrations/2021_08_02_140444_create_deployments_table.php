<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploymentsTable extends Migration
{
    public function up()
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->references('id')->on('projects');

            $table->string('branch');
            $table->string('commit')->nullable();

            $table->text('environment')->nullable();
            $table->text('deploy_script')->nullable();
            $table->text('gunicorn_config')->nullable();
            $table->text('nginx_config')->nullable();

            $table->timestamps();
        });
    }
}
