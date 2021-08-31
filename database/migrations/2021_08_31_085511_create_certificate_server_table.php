<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateServerTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('certificate_server', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_id')->references('id')->on('certificates');
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->timestamps();
        });
    }
}
