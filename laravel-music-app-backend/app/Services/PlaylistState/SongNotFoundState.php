<?php

namespace App\Services\PlaylistState;

class SongNotFoundState implements PlaylistStateInterface {
    public function getNextSong(PlaylistContext $context): array {
        return [
            'song' => null,
            'song_ids' => $context->songIds
        ];
    }
}