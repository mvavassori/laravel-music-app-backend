<?php

namespace App\Services;

use App\Contracts\Repositories\AlbumRepositoryInterface;
use App\Contracts\Services\AlbumServiceInterface;

class AlbumService implements AlbumServiceInterface {
    private AlbumRepositoryInterface $albumRepository;
    public function __construct(AlbumRepositoryInterface $albumRepository) {
        $this->albumRepository = $albumRepository;
    }

    public function getAlbum($id) {
        return $this->albumRepository->find($id);
    }

    public function getAlbumWithSongs($id) {
        return $this->albumRepository->findWithRelations($id, ['songs']);
    }

    public function getAlbumWithContributions($id) {
        return $this->albumRepository->findWithRelations($id, ['contributions.role', 'contributions.artist']);
    }

    public function getAlbumComplete($id) {
        return $this->albumRepository->findWithRelations($id, ['songs', 'contributions.role', 'contributions.artist']);
    }

    public function createAlbum($data) {
        return $this->albumRepository->create($data);
    }

    public function updateAlbum($id, $data) {
        return $this->albumRepository->update($id, $data);
    }

    public function deleteAlbum($id) {
        return $this->albumRepository->delete($id);
    }
}