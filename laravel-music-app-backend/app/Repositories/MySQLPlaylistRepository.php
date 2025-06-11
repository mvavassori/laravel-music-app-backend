<?php
namespace App\Repositories;

use App\Models\Playlist;
use App\Contracts\Repositories\PlaylistRepositoryInterface;

class MySQLPlaylistRepository implements PlaylistRepositoryInterface {
    public function find($id) {
        return Playlist::findOrFail($id);
    }
    public function findWithRelations($id, array $relations) {
        return Playlist::with($relations)->findOrFail($id);
    }
    public function create(array $data) {
        return Playlist::create($data);
    }
    public function update($id, array $data) {
        $playlist = $this->find($id);
        $playlist->update($data);
        return $playlist;
    }
    public function delete($id) {
        return Playlist::destroy($id);
    }
    public function findByUser($userId) {
        return Playlist::with('songs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function findByUserAndType($userId, $type, $date = null) {
        $query = Playlist::with('songs')
            ->where('user_id', $userId)
            ->where('type', $type);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        return $query->first();
    }
    public function attachSongs($playlistId, array $songIds) {
        $playlist = $this->find($playlistId);
        $playlist->songs()->attach($songIds);
        return $playlist;
    }
}