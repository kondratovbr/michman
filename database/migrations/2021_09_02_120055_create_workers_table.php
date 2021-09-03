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

            $table->string('type');
            $table->string('app')->nullable();
            $table->integer('processes');
            $table->json('queues');
            //

            $table->timestamps();
        });
    }
}
