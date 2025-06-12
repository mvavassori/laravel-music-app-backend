<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Play;
use App\Models\Role;
use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Contribution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         // 1. Create basic roles
        // $roles = ['Vocalist', 'Guitarist', 'Producer', 'Writer', 'Drummer', 'Bassist'];
        // foreach ($roles as $roleName) {
        //     Role::create(['name' => $roleName]);
        // }

        // 2. Create users (including a test user)
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 9 more random users
        User::factory(9)->create();

        // 3. Create 20 artists
        Artist::factory(20)->create();

        // 4. Create 10 albums with varied genres
        $genres = ['rock',
                'pop',
                'jazz',
                'classical',
                'hip-hop',
                'country',
                'electronic',
                'r&b',
                'metal',
                'folk'
            ];
        
        Album::factory(10)->create()->each(function ($album) use ($genres) {
            $album->update(['genre' => $genres[array_rand($genres)]]);
        });

        // 5. Create 50 songs distributed among albums
        Song::factory(50)->create()->each(function ($song, $index) use ($genres) {
            // Assign to random album
            $album = Album::inRandomOrder()->first();
            $song->update([
                'album_id' => $album->id,
                'genre' => $album->genre // Songs inherit album genre
            ]);
        });

        // 6. Create contributions for songs
        Song::all()->each(function ($song) {
            // Add a vocalist (always)
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Vocalist')->first()->id,
                'contributable_type' => Song::class,
                'contributable_id' => $song->id
            ]);

            // Add a producer (always)
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Producer')->first()->id,
                'contributable_type' => Song::class,
                'contributable_id' => $song->id
            ]);

            // Randomly add 0-2 more contributors
            $additionalContributors = rand(0, 2);
            for ($i = 0; $i < $additionalContributors; $i++) {
                Contribution::create([
                    'artist_id' => Artist::inRandomOrder()->first()->id,
                    'role_id' => Role::whereNotIn('name', ['Vocalist', 'Producer'])->inRandomOrder()->first()->id,
                    'contributable_type' => Song::class,
                    'contributable_id' => $song->id
                ]);
            }
        });

        // 7. Create contributions for albums
        Album::all()->each(function ($album) {
            // Producer for the album
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Producer')->first()->id,
                'contributable_type' => Album::class,
                'contributable_id' => $album->id
            ]);

            // Sometimes add additional contributors
            if (rand(0, 1)) {
                Contribution::create([
                    'artist_id' => Artist::inRandomOrder()->first()->id,
                    'role_id' => Role::inRandomOrder()->first()->id,
                    'contributable_type' => Album::class,
                    'contributable_id' => $album->id
                ]);
            }
        });

        // 8. Create plays (listening history)
        User::all()->each(function ($user) {
            // Each user has played 20-100 songs
            $playCount = rand(20, 100);
            
            for ($i = 0; $i < $playCount; $i++) {
                $song = Song::inRandomOrder()->first();
                
                Play::create([
                    'user_id' => $user->id,
                    'song_id' => $song->id,
                ]);
            }
        });

        // 9. Create playlists
        User::all()->each(function ($user) {
            // Create 1-3 custom playlists per user
            $playlistCount = rand(1, 3);
            
            for ($i = 0; $i < $playlistCount; $i++) {
                $playlistNames = [
                    'My Favorites', 'Workout Mix', 'Chill Vibes', 'Party Time',
                    'Study Music', 'Road Trip', 'Morning Coffee', 'Night Drive'
                ];
                
                $playlist = Playlist::create([
                    'name' => $playlistNames[array_rand($playlistNames)] . ' ' . ($i + 1),
                    'description' => 'A great collection of songs',
                    'type' => 'custom',
                    'user_id' => $user->id
                ]);

                // Add 5-15 random songs to each playlist
                $songCount = rand(5, 15);
                $songs = Song::inRandomOrder()->limit($songCount)->pluck('id');
                $playlist->songs()->attach($songs);
            }

            // Create a daily mix for some users (created today)
            if (rand(0, 1)) {
                $topGenre = Play::where('user_id', $user->id)
                    ->join('songs', 'plays.song_id', '=', 'songs.id')
                    ->select('songs.genre')
                    ->groupBy('songs.genre')
                    ->orderByRaw('COUNT(*) DESC')
                    ->value('genre');
                    
                // $topGenre = Play::where('user_id', $user->d) // grab all the plays of the specified user
                //     ->select('songs.genre', DB::raw('COUNT(*) as plays_count')) // SELECT songs.genre, COUNT(*) as plays_count FROM plays... // without DB:raw laravel would have assumed COUNT(*) was a column name.
                //     ->join('songs', 'plays.song_id', '=', 'songs.id')   // join with songs
                //     ->groupBy('songs.genre')
                //     ->orderByDesc('plays_count')
                //     ->limit(1) // top genre
                //     ->pluck('genre'); // don't include the counts i.e. play_count column

                if ($topGenre) {
                    $dailyMix = Playlist::create([
                        'name' => 'Daily Mix ' . now()->format('Y-m-d'),
                        'description' => 'Your personalized daily mix',
                        'type' => 'daily_mix',
                        'user_id' => $user->id
                    ]);

                    // Add songs from top genre and most played
                    $genreSongs = Song::where('genre', $topGenre)
                        ->inRandomOrder()
                        ->limit(10)
                        ->pluck('id');
                    
                    $mostPlayed = Play::where('user_id', $user->id)
                        ->select('song_id')
                        ->groupBy('song_id')
                        ->orderByRaw('COUNT(*) DESC')
                        ->limit(10)
                        ->pluck('song_id');

                    $allSongs = $genreSongs->merge($mostPlayed)->unique()->shuffle();
                    $dailyMix->songs()->attach($allSongs);
                }
            }
        });


    }
}
