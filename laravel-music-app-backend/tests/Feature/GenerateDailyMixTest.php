<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Play;
use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateDailyMixTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     public function generates_daily_mix_playlist_for_user(): void
    {
        $user = User::factory()->create();

        $albums = Album::factory(5)->create();

        $songs = Song::factory(40)->create();

        $plays = Play::factory(2000)->create();

        $response = $this->getJson('/api/v1/playlists/daily-mix/1');
        $response->assertStatus(200);

        
    }
}
