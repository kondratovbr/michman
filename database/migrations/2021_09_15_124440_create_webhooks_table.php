<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();

            $table->foreignId('project_id')->references('id')->on('projects');

            $table->string('type');
            $table->string('status');
            $table->string('external_id')->nullable();

            $table->timestamps();
        });
    }
}
