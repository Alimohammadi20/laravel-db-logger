<?php

namespace Alimi7372\DBLogger\Jobs;

use Alimi7372\DBLogger\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanUpLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = now()->subDays(config('dblogger.expire'));
        Log::whereDate('created_at', '<', $date)->delete();
    }
}
