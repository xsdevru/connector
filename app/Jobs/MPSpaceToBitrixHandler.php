<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MPSpaceToBitrixHandler implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $eventType = $this->data['event_type'] ?? null;
        $payload = $this->data['payload'] ?? [];
        $metadata = $this->data['metadata'] ?? [];

        // Генерируем CREST команды
        $crestCommands = $this->generateCrestCommands($eventType, $payload, $metadata);

        // Логируем команды
        Log::info('DEBUG: MPSpace -> Bitrix CREST команды (не отправлены)', $crestCommands);

        // Выбрасываем исключение, чтобы сообщение оставалось в очереди
        throw new \Exception('DEBUG: сообщение оставлено непрочитанным');
    }

    protected function generateCrestCommands($eventType, $payload, $metadata)
    {
        $commands = [];

        switch ($eventType) {
            case 'UserCreated':
                $commands[] = [
                    'method' => 'crm.contact.add',
                    'params' => [
                        'FIELDS' => [
                            'NAME' => $payload['id'] ?? '',
                            'PHONE' => [['VALUE' => $payload['phone'] ?? '', 'VALUE_TYPE' => 'WORK']],
                            'UF_CRM_DATE_REG' => $payload['date_reg'] ?? null,
                            'UF_CRM_REFERRAL' => $payload['refferal'] ?? null,
                            'UF_CRM_INVITED' => $payload['invited'] ?? null,
                            'UF_CRM_BALANCE_TEST' => $payload['balance_test'] ?? 0,
                        ],
                    ],
                ];
                break;

            default:
                Log::warning('DEBUG: неизвестный тип события', ['event_type' => $eventType]);
        }

        return $commands;
    }
}
