<?php

namespace App\Services\NextSong;

interface NextSongStrategyInterface {
    public function execute(array $params): array;
}