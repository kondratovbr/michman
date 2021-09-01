<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')->references('id')->on('servers');

            $table->string('type');
            $table->json('domains');
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }
}
