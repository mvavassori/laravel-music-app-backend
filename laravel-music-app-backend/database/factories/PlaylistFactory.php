<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Playlist>
 */
class PlaylistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' => User::all()->random()->id,
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->words(7, true),
            'type' => $this->faker->randomElement(array_keys(Playlist::TYPES))
        ];
    }
}
