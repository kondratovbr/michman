<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhook_calls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('webhook_id')->references('id')->on('webhooks');

            $table->string('type');
            $table->string('url');
            $table->string('external_id');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->text('exception')->nullable();
            $table->boolean('processed')->nullable();

            $table->timestamps();
        });
    }
};
