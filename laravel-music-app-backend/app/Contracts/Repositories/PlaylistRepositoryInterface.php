<?php
namespace App\Contracts\Repositories;

interface PlaylistRepositoryInterface {
    public function find($id);
    public function findWithRelations($id, array $relations);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByUser($userId);
    public function findByUserAndType($userId, $type, $date = null);
    public function attachSongs($playlistId, array $songIds);
}