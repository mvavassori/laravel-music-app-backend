<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        // $artists = Artist::pluck('id')->toArray();
        return [
            'title' => $this->faker->words(3, true),
            // 'artist_id' => $this->faker->randomElement($artists), // this will get an artist randomly from the existing ones
            'artist_id' => Artist::factory(), // it should create a new artist if no artist is provided
            'album' => $this->faker->words(2, true),
            'genre' => fake()->randomElement(array_keys(Song::GENRES)),
        ];
    }
}
