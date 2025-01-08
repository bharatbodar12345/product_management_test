<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRandomProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-random-products';

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
        // $count = $this->argument('count');
        \App\Models\Product::factory()->count(10)->create();
        $this->info("Successfully created random products.");
    }
}
