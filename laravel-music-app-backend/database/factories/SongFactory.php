<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\Album;
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
            'album_id' => Album::all()->random()->id,// select an existing album
            'genre' => fake()->randomElement(array_keys(Song::GENRES)),
        ];
    }
}
