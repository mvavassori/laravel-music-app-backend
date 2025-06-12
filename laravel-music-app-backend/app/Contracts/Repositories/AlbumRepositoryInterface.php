<?php

namespace App\Contracts\Repositories;

interface AlbumRepositoryInterface {
    public function find($id);
    public function findWithRelations($id, array $relations);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}