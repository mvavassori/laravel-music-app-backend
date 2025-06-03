<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Artist;

class ciao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ciao';

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
        Artist::factory()->create();
        $artist = Artist::get();

        // $artist->name = "John";
        dd($artist);
    }
}
