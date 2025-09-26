<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bitrix_rest_commands', function (Blueprint $table) {
            $table->string('group_id')->nullable()->after('job_id'); // группа связанных команд
            $table->string('source')->nullable()->after('status');    // из metadata.source
            $table->boolean('debug')->default(false)->after('source'); // из metadata.debug
        });
    }

    public function down(): void
    {
        Schema::table('bitrix_rest_commands', function (Blueprint $table) {
            $table->dropColumn(['group_id', 'source', 'debug']);
        });
    }
};
