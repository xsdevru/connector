<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BitrixService
{
    protected string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('bitrix.webhook_url');

        if (!$this->webhookUrl) {
            throw new \Exception('Webhook URL не задан в config/bitrix.php или .env');
        }
    }

    /**
     * Вызов метода Bitrix24
     *
     * @param string $method Например: 'app.info', 'crm.lead.add'
     * @param array $params Параметры запроса
     * @return array
     */
    public function call(string $method, array $params = []): array
    {
        $url = rtrim($this->webhookUrl, '/') . '/' . $method;

        $response = Http::post($url, $params);

        if (!$response->successful()) {
            throw new \Exception('Ошибка запроса Bitrix24: ' . $response->status() . ' ' . $response->body());
        }

        return $response->json();
    }
}
