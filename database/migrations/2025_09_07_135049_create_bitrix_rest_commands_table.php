<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bitrix_rest_commands', function (Blueprint $table) {
            $table->id();
            $table->string('job_id'); // связь с Laravel Job
            $table->string('method'); // метод Bitrix REST
            $table->json('params'); // параметры команды
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamps(); // created_at и updated_at
            $table->timestamp('sent_at')->nullable(); // время отправки в Bitrix
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitrix_rest_commands');
    }
};
