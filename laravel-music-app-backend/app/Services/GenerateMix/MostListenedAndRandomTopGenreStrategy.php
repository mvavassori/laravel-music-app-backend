<?php

namespace App\Services\GenerateMix;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\SongServiceInterface;
use App\Contracts\Repositories\PlayRepositoryInterface;

class MostListenedAndRandomTopGenreStrategy implements GenerateMixStrategyInterface {
    private SongServiceInterface $songService;
    private PlayRepositoryInterface $playService;

    public function __construct(SongServiceInterface $songService, PlayRepositoryInterface $playService) {
        $this->songService = $songService;
        $this->playService = $playService;
    }
    public function generate($userId): array|Collection {
        $topGenre = $this->playService->getTopGenreByUser($userId); // most listened genre by the user

        if (!$topGenre) {
            return collect(); // no top genre; return empty collection 
        }
        $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre, 10);  // 10 songs from that genre
        $mostListenedSongs = $this->playService->getMostPlayedSongsByUser($userId, 10); // most listened 10 songs by the user
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
            ->merge($mostListenedSongs) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all(); // returns back an array. NOT a collection.
        
        return $dailyMix;
    }
}