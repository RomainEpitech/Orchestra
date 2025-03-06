<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\SystemLoggerService;

class LogSystemEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $details;
    public $tries = 2;
    public $backoff = 30;
    public $timeout = 30;

    public function __construct(string $type, string $details)
    {
        $this->type = $type;
        $this->details = $details;
        $this->onQueue('logging');
    }

    public function handle(SystemLoggerService $logger)
    {
        $logger->logEvent($this->type, $this->details);
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error('Failed to log system event after retries', [
            'type' => $this->type,
            'error' => $exception->getMessage()
        ]);
    }
}