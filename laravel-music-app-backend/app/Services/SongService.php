<?php

namespace App\Services;

use App\Contracts\Repositories\SongRepositoryInterface;
use App\Contracts\Services\SongServiceInterface;

class SongService implements SongServiceInterface {
    private SongRepositoryInterface $songRepository;
    public function __construct(SongRepositoryInterface $songRepository) {
        $this->songRepository = $songRepository;
    }
    public function getSong($id) {
        return $this->songRepository->find($id);
    }

    public function getSongWithContributions($id) {
        return $this->songRepository->findWithRelations($id, ['contributions.role', 'contributions.artist']);
    }

    public function getSongWithArtists($id) {
        return $this->songRepository->findWithRelations($id, ['contributions.artist']);
    }

    public function getSongWithAlbum($id) {
        return $this->songRepository->findWithRelations($id, ['album']);
    }

    public function getSongComplete($id) {
        return $this->songRepository->findWithRelations($id, ['album', 'contributions.role', 'contributions.artist']);
    }

    public function createSong(array $data) {
        return $this->songRepository->create($data);
    }

    public function updateSong($id, $data) {
        $this->songRepository->update($id, $data);
    }

    public function deleteSong($id) {
        return $this->songRepository->delete($id);
    }

    public function getSongsByGenreAtRandom($genre, $limit = 10) {
        return $this->songRepository->getSongsByGenreAtRandom($genre, $limit);
    }    
}