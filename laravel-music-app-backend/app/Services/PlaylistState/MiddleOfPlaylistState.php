<?php

namespace App\Services\PlaylistState;

class MiddleOfPlaylistState implements PlaylistStateInterface {
    public function getNextSong(PlaylistContext $context) {
        $nextSongId = $context->songIds[$context->currentIndex + 1];

        return [
            'song' => $context->songService->getSong($nextSongId),
            'song_ids' => $context->songIds
        ];
    }
}