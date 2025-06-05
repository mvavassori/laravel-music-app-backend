<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Album;
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

        // make 5 artists and make 5 new songs for each
        // Artist::factory(5)->hasSongs(5)->create();

        // loops through the artists collection and create 2 to 5 new songs per artist 
        // foreach ($artists as $artist) {
        //     Song::factory(rand(2, 5))->create([
        //         'artist_id' => $artist->id  // we specified the artist_id in the attributes otherwise it would have created 2 to 5 new artists at each iteration 
        //     ]);
        // }

        // Plan
        // 1. create 4 artists
        // 2. create 2 albums
        // 3. attach artists to albums
        // 4. create 5 songs (each will have a random album_id from the existing ones)
        // 5. attach artists to songs

        // 1
        $artists = Artist::factory(4)->create(); // Creates 4 artists

        // 2
        $albums = Album::factory(2)->create(); // Creates 2 albums

        // 3
        foreach ($albums as $album) {
            $album->artists()->attach(
                $artists->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

        // 4
        $songs = Song::factory(5)->create(); // each song will select an existing album (see SongFactory)

        // 5
        foreach ($songs as $song) {
            $song->artists()->attach(
                $artists->random(rand(1, 2))->pluck('id')->toArray()
            );
        }
        
    }
}
