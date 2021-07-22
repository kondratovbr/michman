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

            $table->string('domain');
            $table->json('aliases');
            $table->boolean('allow_sub_domains');
            $table->string('type');
            $table->string('root');
            $table->string('python_version')->nullable();

            $table->string('vcs_provider')->nullable();
            $table->string('repo')->nullable();
            $table->string('branch')->nullable();
            $table->boolean('use_deploy_key');

            $table->timestamps();
        });
    }
}
