<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Contribution;
use App\Models\Role;
use App\Models\Album;
use App\Models\Song;
use App\Models\Artist;
use Illuminate\Database\Seeder;

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
        $roles = ['Vocalist', 'Guitarist', 'Producer', 'Writer'];
        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }

        // 2. Create 10 artists
        Artist::factory(10)->create();

        // 3. Create 5 albums
        Album::factory(5)->create();

        // 4. Create 20 songs (distributed among albums)
        Song::factory(20)->create();

        // 5. Create some contributions
        // Each song gets a vocalist and a producer
        Song::all()->each(function ($song) {
            // Add a vocalist
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Vocalist')->first()->id,
                'contributable_type' => Song::class,
                'contributable_id' => $song->id
            ]);

            // Add a producer  
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Producer')->first()->id,
                'contributable_type' => Song::class,
                'contributable_id' => $song->id
            ]);
        });

        // Each album gets a producer
        Album::all()->each(function ($album) {
            Contribution::create([
                'artist_id' => Artist::inRandomOrder()->first()->id,
                'role_id' => Role::where('name', 'Producer')->first()->id,
                'contributable_type' => Album::class,
                'contributable_id' => $album->id
            ]);
        });

    }
}
