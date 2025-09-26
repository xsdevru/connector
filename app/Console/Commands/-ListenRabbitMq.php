<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Jobs\SaveRawEventJob;
use App\Jobs\MPSpaceToBitrixHandler;

class ListenRabbitMq extends Command
{
    protected $signature = 'rabbitmq:listen';
    protected $description = 'Listen RabbitMQ FromDjangoToBitrix queue and dispatch jobs';

    public function handle()
    {
        $this->info('Starting RabbitMQ listener...');

        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );

        $channel = $connection->channel();
        $queueName = env('RABBITMQ_QUEUE', 'FromDjangoToBitrix');

        // Подписываемся на очередь
        //$channel->queue_declare($queueName, false, true, false, false);

        $callback = function ($msg) {
            $data = json_decode($msg->body, true);

            // Всегда сохраняем сырое событие
            SaveRawEventJob::dispatch($data)->onQueue('FromDjangoToBitrix');

            // Обработка CREST-команд
            //MPSpaceToBitrixHandler::dispatch($data)->onQueue('FromDjangoToBitrix');

            // Подтверждаем сообщение (ack)
            $msg->ack();

            $this->info('Dispatched jobs for event: ' . ($data['event_type'] ?? 'unknown'));
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
