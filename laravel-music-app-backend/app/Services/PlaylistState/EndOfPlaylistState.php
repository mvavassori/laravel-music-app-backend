<?php

namespace App\Services\PlaylistState;

class EndOfPlaylistState implements PlaylistStateInterface {
    public function getNextSong(PlaylistContext $context) {
        if ($context->shouldGenerate) {
            return $this->extendPlaylist($context);
        }

        return [
            'song' => null,
            'song_ids' => $context->songIds
        ];
    }

    private function extendPlaylist(PlaylistContext $context) {
        // generate a new mix of songs tailored to the user
        $newSongs = $context->generateMixStrategy->generate($context->userId);
        $newSongIds = collect($newSongs)->pluck('id')->toArray();

        $uniqueNewSongIds = array_values(array_diff($newSongIds, $context->songIds));

        // append new songs to the existing ones
        $updatedSongIds = array_merge($context->songIds, $uniqueNewSongIds);

        // if there are no new songs to add
        if (!isset($uniqueNewSongIds[0])) {
            return [
                'song' => null,
                'song_ids' => $context->songIds
            ];
        }

        return [
            'song' => $context->songService->getSong($uniqueNewSongIds[0]),
            'song_ids' => $updatedSongIds
        ];
    }
}