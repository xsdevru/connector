<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('mpspace_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable()->index();
            $table->json('payload');
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('mpspace_messages');
    }
};
