<?php

namespace App\Contracts\Services;

interface SongServiceInterface {

    public function getSong($id);

    public function getSongWithContributions($id);

    public function getSongWithArtists($id);

    public function getSongWithAlbum($id);

    public function getSongComplete($id);

    public function createSong(array $data);

    public function updateSong($id, array $data);

    public function deleteSong($id);

    public function getSongsByGenreAtRandom($genre, $limit = 10);
}