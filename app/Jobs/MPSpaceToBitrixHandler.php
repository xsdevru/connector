<?php

namespace App\Jobs;

use App\Models\BitrixRestCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MPSpaceToBitrixHandler implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        Log::channel('rabbitmq')->info('Incoming RabbitMQ event', [
            'data' => $this->data
        ]);

        // Принудительно сбрасываем буфер в файл
        foreach (Log::channel('rabbitmq')->getLogger()->getHandlers() as $handler) {
            $handler->close();
        }
        error_log(json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL, 3, storage_path('logs/rabbitmq.log'));
        throw new \Exception('Temporary peek, do not ack');
        /*
        $eventType = $this->data['event_type'] ?? null;
        $payload = $this->data['payload'] ?? [];
        $metadata = $this->data['metadata'] ?? [];

        // Генерация CREST-команд
        $crestCommands = $this->generateCrestCommands($eventType, $payload, $metadata);

        // Полный JSON события для отладки
        $eventJson = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $groupId = uniqid('group_'); // уникальная группа команд для retry

        foreach ($crestCommands as $crestCommand) {
            try {
                BitrixRestCommand::create([
                    'job_id' => isset($this->job) ? $this->job->getJobId() : null,,
                    'group_id' => $groupId,
                    'method' => $crestCommand['method'],
                    'params' => $crestCommand['params'],
                    'status' => 'pending',
                    'source' => $metadata['source'] ?? null,
                    'debug' => filter_var($metadata['debug'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'debug_payload' => $eventJson,
                ]);
            } catch (\Throwable $e) {
                Log::error('Ошибка при сохранении BitrixRestCommand', [
                    'exception' => $e->getMessage(),
                    'data' => $this->data
                ]);

                // Пробрасываем исключение, чтобы воркер знал, что Job упала
                throw $e;
            }
        }

        Log::info('DEBUG: MPSpace события сохранены в таблицу', ['group_id' => $groupId]);
        */
    }

    protected function generateCrestCommands($eventType, $payload, $metadata): array
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
