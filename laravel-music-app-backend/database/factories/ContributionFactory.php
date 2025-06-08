<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contribution>
 */
class ContributionFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        // randomly choose between Song and Album
        $contributableType = $this->faker->randomElement([Song::class, Album::class]);

        // get a random instance of the chosen type
        $contributable = $contributableType::inRandomOrder()->first();

        return [
            'artist_id' => Artist::factory(),
            'role_id' => Role::factory(),
            'contributable_type' => $contributableType,
            'contributable_id' => $contributable ? $contributable->id : $contributableType::factory(),
        ];
    }
}
