<?php

namespace App\Services;

use App\Contracts\Repositories\ArtistRepositoryInterface;
use App\Contracts\Services\ArtistServiceInterface;

class ArtistService implements ArtistServiceInterface {
    private ArtistRepositoryInterface $artistRepository;
    public function __construct(ArtistRepositoryInterface $artistRepository) {
        $this->artistRepository = $artistRepository;
    }
    public function getAllArtists() {
        return $this->artistRepository->all();
    }

    public function getArtist($id) {
        return $this->artistRepository->find($id);
    }

    public function getArtistWithContributions($id) {
        return $this->artistRepository->findWithContributions($id);
    }

    public function getArtistWithSongs($id) {
        return $this->artistRepository->findWithSongs($id);
    }

    public function getArtistWithAlbums($id) {
        return $this->artistRepository->findWithAlbums($id);
    }

    public function createArtist($data) {
        return $this->artistRepository->create($data);
    }

    public function updateArtist($id, array $data) {
        // only include non null fields
        $filtered = array_filter($data, function ($value) {
            return $value !== null;
        });

        return $this->artistRepository->update($id, $filtered);
    }

    public function deleteArtist($id) {
        return $this->artistRepository->delete($id);
    }
}