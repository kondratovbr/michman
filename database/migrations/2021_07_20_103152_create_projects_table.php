<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('vcs_provider_id')
                ->nullable()
                ->references('id')->on('vcs_providers');

            $table->string('domain');
            $table->json('aliases');
            $table->boolean('allow_sub_domains');
            $table->string('type');
            $table->string('root');
            $table->string('python_version')->nullable();

            $table->string('repo')->nullable();
            $table->string('branch')->nullable();
            $table->string('app_name')->nullable();
            $table->boolean('use_deploy_key');
            $table->string('requirements_file')->nullable();

            $table->timestamps();
        });
    }
}
