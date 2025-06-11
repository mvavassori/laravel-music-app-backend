<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Play>
 */
class PlayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'song_id' => Song::factory()
            // 'user_id' => User::all()->random()->id,
            // 'song_id' => Song::all()->random()->id
        ];
    }
}
