<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\MPSpaceToBitrixHandler;

class SeedMPSpaceQueue extends Command
{
    protected $signature = 'queue:seed-mpspace {count=10}';
    protected $description = 'Создает тестовые события UserCreated и закидывает их в очередь FromDjangoToBitrix';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Создаем {$count} тестовых событий в очередь FromDjangoToBitrix...");

        for ($i = 1; $i <= $count; $i++) {
            $data = [
                "event_type" => "UserCreated",
                "timestamp" => time(),
                "payload" => [
                    "id" => $i,
                    "phone" => "+7999" . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                    "date_reg" => date('d.m.Y', strtotime("-$i days")),
                    "refferal" => "ref" . rand(1000, 9999),
                    "invited" => rand(0,1) ? "inv" . rand(1000,9999) : "",
                    "balance_test" => rand(0, 1000),
                    "utm_source" => "utm_source_$i",
                    "utm_medium" => "utm_medium_$i",
                    "utm_campaign" => "utm_campaign_$i",
                    "utm_content" => "utm_content_$i",
                    "utm_term" => "utm_term_$i",
                    "manager_default" => 1
                ],
                "metadata" => [
                    "source" => "MPSpace",
                    "debug" => rand(0,1) ? "True" : "False"
                ]
            ];

            // Отправляем Job в очередь FromDjangoToBitrix
            dispatch((new MPSpaceToBitrixHandler($data))->onQueue('FromDjangoToBitrix'));
        }

        $this->info("Готово! {$count} событий закинуты в очередь FromDjangoToBitrix.");
    }
}
