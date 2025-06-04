<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Song;
use App\Models\Artist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // laravel collection containing 5 Artist model instances
        $artists = Artist::factory(5)->create();

        // loops through the artists collection and create 2 to 5 new songs per artist 
        foreach ($artists as $artist) {
            Song::factory(rand(2, 5))->create([
                'artist_id' => $artist->id  // we specified the artist_id in the attributes otherwise it would have created 2 to 5 new artists at each iteration 
            ]);
        }
        
    }
}
