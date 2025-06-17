<?php

namespace App\Services\NextSong;

class SongNotFoundStrategy implements NextSongStrategyInterface {
    public function execute(array $params): array {
        return [
            'song' => null,
            'song_ids' => $params['song_ids']
        ];
    }
}