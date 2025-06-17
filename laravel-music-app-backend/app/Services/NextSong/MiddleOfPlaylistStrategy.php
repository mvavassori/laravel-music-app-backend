<?php

namespace App\Services\NextSong;

use App\Contracts\Services\SongServiceInterface;

class MiddleOfPlaylistStrategy implements NextSongStrategyInterface {
    private SongServiceInterface $songService;
    public function __construct(SongServiceInterface $songService) {
        $this->songService = $songService;
    }
    
    public function execute(array $params): array {
        $nextSongId = $params['song_ids'][$params['currentIndex'] + 1];
        
        return [
            'song' => $this->songService->getSong($nextSongId),
            'song_ids' => $params['song_ids']
        ];
    }
}