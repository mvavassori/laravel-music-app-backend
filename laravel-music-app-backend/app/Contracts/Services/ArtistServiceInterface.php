<?php

namespace App\Contracts\Services;

interface ArtistServiceInterface {
    public function getAllArtists();
    public function getArtist($id);
    public function createArtist(array $data);
    public function deleteArtist($id);
    public function updateArtist($id, array $data);
    public function getArtistWithContributions($id);
    public function getArtistWithSongs($id);
    public function getArtistWithAlbums($id);
}