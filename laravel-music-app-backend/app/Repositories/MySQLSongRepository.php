<?php

namespace App\Repositories;

use App\Models\Song;
use Illuminate\Support\Facades\DB;
use App\Contracts\Repositories\SongRepositoryInterface;

class MySQLSongRepository implements SongRepositoryInterface {
    public function find($id) {
        return Song::findOrFail($id);
    }
    public function findWithRelations($id, array $relations) {
        return Song::with($relations)->findOrFail($id);
    }
    public function create(array $data) {
        $song = null;
        DB::transaction(function () use ($data, &$song) {
            $song = Song::create([
                'title' => $data['title'],
                'album_id' => $data['album_id'] ?? null,
                'genre' => $data['genre'] ?? null
            ]);

            // create the contribution
            $song->contributions()->createMany($data['contributions']);
        });
        return $song->load(['contributions.artist', 'contributions.role']);
    }
    public function update($id, array $data) {
        $song = $this->find($id);
        DB::transaction(function() use ($song, $data) {
            // update fields provided
            $song->update(array_intersect_key($data, array_flip(['title', 'album_id', 'genre'])));

            if (isset($data['contributions'])) {
                $song->contributions()->delete(); // remove existing
                $song->contributions()->createMany($data['contributions']);
            }
        });
        return $song->load(['contributions.artist', 'contributions.role']);
    }
    public function delete($id) {
        return Song::destroy($id);
    }
    public function getSongsByGenreAtRandom($genre, $limit = 10) {
        return Song::where('genre', $genre)
            ->inRandomOrder() // picks songs with specified genre randomly
            ->limit($limit)
            ->get();
    }

}