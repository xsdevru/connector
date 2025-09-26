<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\MpspaceMessage;
use Illuminate\Support\Facades\Log;

class RabbitConsume extends Command
{
    protected $signature = 'rabbit:listen';
    protected $description = 'Consume RabbitMQ messages and save them to mpspace_messages table';

    public function handle()
    {
        $host = env('RABBITMQ_HOST', '127.0.0.1');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $pass = env('RABBITMQ_PASSWORD', 'guest');
        $vhost = env('RABBITMQ_VHOST', '/');
        $queue = env('RABBITMQ_QUEUE', 'FromDjangoToBitrix');

        $this->info("Connecting to RabbitMQ {$host}:{$port}, queue={$queue}");

        $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
        $channel = $connection->channel();
        //$channel->queue_declare($queue, false, true, false, false);

        $callback = function (AMQPMessage $msg) {
            try {
                $raw = $msg->getBody();
                $payload = json_decode($raw, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                $messageId = $payload['message_id'] ?? null;

                MpspaceMessage::create([
                    'message_id' => $messageId,
                    'payload'    => $payload,
                    'status'     => 'new',
                ]);

                $msg->ack();
                $this->info("Saved message_id={$messageId}");

            } catch (\Throwable $e) {
                Log::error("RabbitMQ consumer error: " . $e->getMessage());
                $msg->nack(false, true); // вернуть сообщение в очередь
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
