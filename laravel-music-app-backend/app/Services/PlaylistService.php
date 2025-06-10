<?php

namespace App\Services;

class PlaylistService {
    private PlayService $playService;
    private SongService $songService;
    public function __construct(PlayService $playService, SongService $songService) { // constructor dependency injection
        $this->playService = $playService;
        $this->songService = $songService;
    }
    public function generateDailyMix($userId) {
        $topGenre = $this->playService->getTopGenreForUser($userId); // most listened genre by the user

        if (!$topGenre) {
            return collect(); // no top genre; return empty collection 
        }

        $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre[0], 10);  // 10 songs from that genre
        $mostListenedSongs = $this->playService->getMostListenedSongs($userId, 10); // most listened 10 songs by the user
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
            ->merge($mostListenedSongs) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all();
        
        return $dailyMix;
    }
}