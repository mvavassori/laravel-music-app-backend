<?php

namespace App\Contracts\Repositories;

interface ArtistRepositoryInterface {
    // basic CRUD operations
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    // specialized queries with relations
    public function findWithContributions($id);
    public function findWithSongs($id);
    public function findWithAlbums($id);
}