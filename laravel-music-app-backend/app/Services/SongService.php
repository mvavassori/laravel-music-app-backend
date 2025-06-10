<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Support\Facades\DB;

class SongService {

    public function getSong($id) {
        $song = Song::findOrFail($id);
        return $song;
    }

    public function getSongWithContributions(int $id): Song {
        return Song::with(['contributions.role', 'contributions.artist'])->findOrFail($id);
    }

    public function getSongWithArtists(int $id): Song {
        return Song::with('contributions.artist')->findOrFail($id);
    }

    public function getSongWithAlbum(int $id): Song {
        return Song::with('album')->findOrFail($id); // SELECT * FROM songs WHERE id = 1; -- example result album_id = 2 // SELECT * FROM albums WHERE id = 2;
    }

    public function getSongComplete(int $id): Song {
        return Song::with(['album', 'contributions.role', 'contributions.artist'])->findOrFail($id);
    }

    public function createSong($data) {
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

    public function updateSong($song, $data) {
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

    public function deleteSong(Song $song) {
        $song->delete();
    }

    public function getSongsByGenreAtRandom($genre, $limit = 10) {
        $getFromGenre = Song::where('genre', $genre)
            ->inRandomOrder() // picks songs with specified genre randomly
            ->limit($limit)
            ->get();

        return $getFromGenre;
    }    
}