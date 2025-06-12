<?php

namespace App\Contracts\Repositories;

interface SongRepositoryInterface {
    public function find($id);
    public function findWithRelations($id, array $relations);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getSongsByGenreAtRandom($genre, $limit = 10);
}