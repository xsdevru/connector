<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BitrixService;

class BitrixTestCommand extends Command
{
    protected $signature = 'bitrix:test';
    protected $description = 'Тестовое подключение к Bitrix24 через webhook';

    public function handle(): void
    {
        $bitrix = new BitrixService();

        try {
            $response = $bitrix->call('app.info');
            $this->info("✅ Ответ Bitrix24:");
            $this->line(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            $this->error("❌ Ошибка: " . $e->getMessage());
        }
    }
}
