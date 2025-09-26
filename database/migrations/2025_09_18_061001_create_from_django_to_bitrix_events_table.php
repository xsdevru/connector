<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('from_django_to_bitrix_events', function (Blueprint $table) {
            $table->id();
            $table->json('payload');   // Сырой JSON события
            $table->timestamp('processed_at')->nullable(); // Когда успешно обработали
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('from_django_to_bitrix_events');
    }
};
