<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bitrix_rest_commands', function (Blueprint $table) {
            $table->longText('debug_payload')->nullable()->after('params');
        });
    }

    public function down()
    {
        Schema::table('bitrix_rest_commands', function (Blueprint $table) {
            $table->dropColumn('debug_payload');
        });
    }

};
