<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerLogsTable extends Migration
{
    /** @var string The database connection that should be used by the migration. */
    protected $connection = 'db-logs';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('server_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id');

            $table->string('type');
            $table->text('command')->nullable();
            $table->integer('exit_code')->nullable();
            $table->text('content')->nullable();
            $table->string('local_file')->nullable();
            $table->string('remote_file')->nullable();
            $table->boolean('success')->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }
}
