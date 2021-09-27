<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')->references('id')->on('servers');
            $table->foreignId('project_id')->references('id')->on('projects');

            $table->string('state');
            $table->string('type');
            $table->string('app')->nullable();
            $table->integer('processes')->nullable();
            $table->json('queues')->nullable();
            $table->integer('stop_seconds')->nullable();
            $table->integer('max_tasks_per_child')->nullable();
            $table->bigInteger('max_memory_per_child')->nullable();

            $table->timestamps();
        });
    }
}
