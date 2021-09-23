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
            $table->uuid('uuid')->unique()->index();

            $table->foreignId('project_id')->references('id')->on('projects');

            $table->string('provider');
            $table->string('type');
            $table->string('url');
            $table->text('secret');
            $table->string('state');
            $table->string('external_id')->nullable();

            $table->timestamps();
        });
    }
}
