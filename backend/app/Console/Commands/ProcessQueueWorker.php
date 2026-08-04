<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:process-queue-worker')]
#[Description('Command description')]
class ProcessQueueWorker extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
