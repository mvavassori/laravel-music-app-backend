<?php

namespace App\Contracts\Repositories;

interface PlayRepositoryInterface {
    public function create(array $data);
    public function getMostPlayedSongsByUser($userId, $limit = 10);
    public function getTopGenreByUser($userId);
}