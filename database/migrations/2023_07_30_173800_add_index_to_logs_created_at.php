<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->index('created_at', 'idx_logs_created_at');
        });
    }

    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex('idx_logs_created_at');
        });
    }
};
