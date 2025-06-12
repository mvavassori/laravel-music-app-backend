<?php

namespace App\Repositories;

use App\Models\Album;
use Illuminate\Support\Facades\DB;
use App\Contracts\Repositories\AlbumRepositoryInterface;

class MySQLAlbumRepository implements AlbumRepositoryInterface {
    public function find($id) {
        return Album::findOrFail($id);
    }

    public function findWithRelations($id, array $relations) {
        $albumWithSongs = Album::with($relations)->findOrFail($id);
        return $albumWithSongs;
    }

    public function create($data) {
        $album = null;
        DB::transaction(function () use ($data, &$album) {
            $album = Album::create([
                'title' => $data['title'],
                'image_url' => $data['image_url'] ?? null,
                'genre' => $data['genre'],
                'description' => $data['description'] ?? null,
            ]);
            $album->contributions()->createMany($data['contributions']);
        });

        return $album->load(['contributions.artist', 'contributions.role']);
    }

    public function update($id, $data) {
        $album = $this->find($id);
        DB::transaction(function () use ($data, &$album) {
            $album->update(array_intersect_key($data, array_flip(['title', 'image_url', 'genre', 'description'])));

            // $album->update([
            //     'title' => $data['title'] ?? $album->title,
            //     'image_url' => $data['image_url'] ?? $album->image_url,
            //     'genre' => $data['genre'] ?? $album->genre,
            //     'description' => $data['description'] ?? $album->description
            // ]);

            
            if (isset($data['contributions'])) {
                $album->contributions()->delete(); // remove existing
                $album->contributions()->createMany($data['contributions']);
            }
        });

        return $album->load(['contributions.artist', 'contributions.role']);
    }

    public function delete($id) {
        Album::destroy($id);
    }
}