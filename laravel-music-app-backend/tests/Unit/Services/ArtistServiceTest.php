<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
// use PHPUnit\Framework\TestCase;
use App\Models\Contribution;
use App\Services\ArtistService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArtistServiceTest extends TestCase
{
    use RefreshDatabase;

    private ArtistService $artistService;
    protected function setUp(): void {
        parent::setUp();
        // let Laravel's container resolve the service
        $this->artistService = $this->app->make(ArtistService::class);
    }
    
    public function test_get_all_artists_returns_all_artists() {
        Artist::factory()->count(3)->create();

        $artists = $this->artistService->getAllArtists();

        $this->assertCount(3, $artists);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $artists);
    }

    public function test_get_artist_returns_artist() {
        $artistCreated = Artist::factory()->create([
            'name' => 'Artistone',
            'bio' => 'A bio',
            'image_url' => 'https://example.com/assets/img.jpg'
        ]);
        $artist = $this->artistService->getArtist($artistCreated->id);

        $this->assertEquals($artistCreated->id, $artist->id);
        $this->assertEquals($artistCreated->name, 'Artistone');
        $this->assertInstanceOf(Artist::class, $artist);
    }

    public function test_get_artist_with_contributions() {
        $artistCreated = Artist::factory()->create([
            'name' => 'Artistone 1',
            'bio' => 'A bio',
            'image_url' => 'https://example.com/assets/img.jpg'
        ]);

        $roleCreated = Role::factory()->create([
            'name' => 'Singer'
        ]);

        $albumCreated = Album::factory()->create();

        $songCreated1 = Song::factory([
            'title' => 'Song uno',
            'album_id' => $albumCreated->id,
            'genre' => 'pop'
        ])->create();
        $songCreated2 = Song::factory([
            'title' => 'Song due',
            'album_id' => $albumCreated->id,
            'genre' => 'pop'
        ])->create();
        $songCreated3 = Song::factory([
            'title' => 'Song tre',
            'album_id' => $albumCreated->id,
            'genre' => 'pop'
        ])->create(); 

        Contribution::factory()->create([
            'artist_id' => $artistCreated->id,
            'role_id' => $roleCreated->id,
            'contributable_type' => Song::class,
            'contributable_id' => $songCreated1->id
        ]);

        Contribution::factory()->create([
            'artist_id' => $artistCreated->id,
            'role_id' => $roleCreated->id,
            'contributable_type' => Song::class,
            'contributable_id' => $songCreated2->id
        ]);

        Contribution::factory()->create([
            'artist_id' => $artistCreated->id,
            'role_id' => $roleCreated->id,
            'contributable_type' => Song::class,
            'contributable_id' => $songCreated3->id
        ]);

        $result = $this->artistService->getArtistWithContributions($artistCreated->id);
        $contributionTypes = $result->contributions->pluck('contributable_type')->unique();
        $this->assertContains(Song::class, $contributionTypes);
        $this->assertContains(Album::class, $contributionTypes);
    }
}
