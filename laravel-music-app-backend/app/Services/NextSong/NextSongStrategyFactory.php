<?php

namespace App\Services\NextSong;

use App\Contracts\Services\SongServiceInterface;
use App\Services\GenerateMix\GenerateMixStrategyInterface;

class NextSongStrategyFactory {
    private GenerateMixStrategyInterface $generateMixStrategy;
    private SongServiceInterface $songService;
    public function __construct(GenerateMixStrategyInterface $generateMixStrategy, SongServiceInterface $songService) {
        $this->generateMixStrategy = $generateMixStrategy;
        $this->songService = $songService;
    }


    public function create(int|false $currentIndex, array $songIds) {

        // song not found 
        if ($currentIndex === false) {
            return new SongNotFoundStrategy();
        }
        
        // we're at the last song in the playlist
        if ($currentIndex === count($songIds) - 1) {
            return new EndOfPlaylistStrategy(generateMixStrategy: $this->generateMixStrategy, songService: $this->songService);
        }

        // standard middle or start of the playlist
        return new MiddleOfPlaylistStrategy(songService: $this->songService);
    }
}