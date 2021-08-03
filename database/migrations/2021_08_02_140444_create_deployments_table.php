<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->references('id')->on('projects');

            $table->string('branch');
            $table->string('commit')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }
}
