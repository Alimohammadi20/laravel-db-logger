<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('level');
            $table->string('uri');
            $table->string('type');
            $table->string('service');
            $table->float('response_time');
            $table->string('status_code')->nullable();
            $table->text('message')->nullable();
            $table->string('user')->nullable();
            $table->foreignId('input_id')->nullable()->constrained('log_contexts')->onDelete('cascade');
            $table->foreignId('output_id')->nullable()->constrained('log_contexts')->onDelete('cascade');
            $table->foreignId('context_id')->nullable()->constrained('log_contexts')->onDelete('cascade');
            $table->foreignId('extra_data_id')->nullable()->constrained('log_contexts')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
