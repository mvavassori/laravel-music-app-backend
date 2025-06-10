<?php

namespace App\Services;

use App\Models\Album;
use Illuminate\Support\Facades\DB;

class AlbumService {


    public function getAlbum($id) {
        $album = Album::findOrFail($id);
        return $album;
    }

    public function getAlbumnWithSongs($id) {
        $albumWithSongs = Album::with('songs')->findOrFail($id); // SELECT FROM albums WHERE id = 25 LIMIT 1; // SELECT FROM songs WHERE songs.album_id IN (25);
        return $albumWithSongs;
    }

    public function getAlbumWithContributions($id) {
        $albumWithContributions = Album::with(['contributions.role', 'contributions.artist'])->findOrFail($id);
        return $albumWithContributions;
    }

    public function getAlbumComplete($id) {
        $albumComplete = Album::with(['songs', 'contributions.role', 'contributions.artist'])->findOrFail($id);
        return $albumComplete;
    }

    public function createAlbum($data) {
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

    public function updateAlbum(Album $album, $data) {
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

    public function deleteAlbum(Album $album) {
        $album->delete();
    }
}