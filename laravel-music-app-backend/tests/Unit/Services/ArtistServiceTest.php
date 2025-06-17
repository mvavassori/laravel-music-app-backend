<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Artist;
use App\Services\ArtistService;
use App\Contracts\Repositories\ArtistRepositoryInterface;

class ArtistServiceTest extends TestCase
{

    private $artistRepositoryMock;
    private $artistService;

    protected function setUp(): void {
        parent::setUp();
        
        // Correct way to mock an interface
        $this->artistRepositoryMock = Mockery::mock(ArtistRepositoryInterface::class);
        
        // Bind it to Laravel's container so dependency injection works
        // $this->app->instance(ArtistRepositoryInterface::class, $this->artistRepositoryMock);
        
        $this->artistService = new ArtistService($this->artistRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_an_artist_successfully() {
        // Arrange
        $artistData = [
            'name' => 'John Doe',
            'bio' => 'A talented musician',
            'image_url' => 'https://example.com/image.jpg'
        ];

        $expectedArtist = new Artist([
            'id' => 1,
            'name' => 'John Doe',
            'bio' => 'A talented musician',
            'image_url' => 'https://example.com/image.jpg'
        ]);

        // Set up the mock expectation
        $this->artistRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($artistData)
            ->andReturn($expectedArtist);

        // Act
        $result = $this->artistService->createArtist($artistData);

        // Assert
        $this->assertInstanceOf(Artist::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('A talented musician', $result->bio);
        $this->assertEquals('https://example.com/image.jpg', $result->image_url);
    }
}
