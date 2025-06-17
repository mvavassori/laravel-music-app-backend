<?php

namespace App\Services\PlaylistState;

class PlaylistStateFactory {
    public function createState($currentIndex, array $songIds) {
        // song not found 
        if ($currentIndex === false) {
            return new SongNotFoundState();
        }
        
        // we're at the last song in the playlist
        if ($currentIndex === count($songIds) - 1) {
            return new EndOfPlaylistState();
        }

        // standard middle or start of the playlist
        return new MiddleOfPlaylistState();
    }
}