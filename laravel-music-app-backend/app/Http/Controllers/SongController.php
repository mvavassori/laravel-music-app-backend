<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SongController extends Controller {
    public function show($id) {
        $song = Song::findOrFail($id);
        return response()->json($song, 200);
    }

    public function showWithArtists($id) {
        $song = Song::with('artists')->findOrFail($id);
        return response()->json($song, 200);
    }

    public function showWithAlbum($id) {
        $songWithAlbum = Song::with('album')->findOrFail($id);
        return response()->json($songWithAlbum, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))],
            'artist_ids' => 'required|array', // expecting an array of artist IDs
            'artist_ids.*' => 'exists:artists,id', // each id must exist in the artists table
        ]);

        $song = Song::create([
            'title' => $validated['title'],
            'album_id' => $validated['album_id'],
            'genre' => $validated['genre']
        ]);

        $song->artists()->sync($validated['artist_ids']);


        // EXAMPLE with specified headers
        // return response($song->load('artists'), 201, [
        //     'Content-Type' => 'application/json'
        // ]);

        // return songs with relationships loaded
        return response()->json($song->load('artists'), 201);
    }

    public function update(Request $request, $id) {
        $song = Song::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))],
            'artist_ids' => 'nullable|array',
            'artist_ids.*' => 'exists:artists,id',
        ]);

        // update fields provided
        $song->update($request->validated());

        // update artist relationships using sync() if provided
        if (isset($validated['artist_ids'])) {
            $song->artists()->sync($validated['artist_ids']);
        }

        return response()->json($song->load('artists'), 200);
    }

    public function destroy($id) {
        $song = Song::findOrFail($id);
        $song->delete();

        return response()->json(['message' => 'Song deleted successfully'], 200);
    }
}
