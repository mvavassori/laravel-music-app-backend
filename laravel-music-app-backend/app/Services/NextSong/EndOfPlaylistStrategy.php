<?php

namespace App\Services\NextSong;

use App\Contracts\Services\SongServiceInterface;
use App\Services\GenerateMix\GenerateMixStrategyInterface;

class EndOfPlaylistStrategy implements NextSongStrategyInterface {
    private GenerateMixStrategyInterface $generateMixStrategy;
    private SongServiceInterface $songService;

    public function __construct(GenerateMixStrategyInterface $generateMixStrategy, SongServiceInterface $songService) {
        $this->generateMixStrategy = $generateMixStrategy;
        $this->songService = $songService;
    }

    public function execute(array $params): array {
        if (!$params['shouldGenerate']) {
            return [
                'song' => null,
                'song_ids' => $params['song_ids']
            ];
        }
        // generate a new mix of songs tailored to the user
        $newSongs = $this->generateMixStrategy->generate($params['user_id']);
        $newSongIds = collect($newSongs)->pluck('id')->toArray();

        $uniqueNewSongIds = array_values(array_diff($newSongIds, $params['song_ids']));

        // append new songs to the existing ones
        $updatedSongIds = array_merge($params['song_ids'], $uniqueNewSongIds);

        // if there are no new songs to add
        if (!isset($uniqueNewSongIds[0])) {
            return [
                'song' => null,
                'song_ids' => $params['song_ids']
            ];
        }

        return [
            'song' => $this->songService->getSong($uniqueNewSongIds[0]),
            'song_ids' => $updatedSongIds
        ];
    }
}