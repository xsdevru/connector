<?php

namespace App\Jobs;

use App\Models\FromDjangoToBitrixEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable; // <-- добавляем
use Illuminate\Support\Facades\Log;

class SaveRawEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            FromDjangoToBitrixEvent::create([
                'payload' => $this->data,
            ]);
            Log::info('Saved raw event', ['data' => $this->data]);
        } catch (\Throwable $e) {
            Log::error('Failed to save raw event', ['exception' => $e->getMessage()]);
        }
    }
}
