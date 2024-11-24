<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("This is Dev Test command");
        $this->line("line displays a line in black");
        $this->fail("fail displays a warning");
    }
}
