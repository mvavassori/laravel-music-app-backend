<?php

namespace App\Services\PlaylistState;

use App\Services\PlaylistState\PlaylistContext;

interface PlaylistStateInterface {
    public function getNextSong(PlaylistContext $context);
}