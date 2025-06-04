<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SongController extends Controller {
    public function show($id) {
        return Song::findOrFail($id);
    }

    public function showWithArtists(Song $song) {
        $song->load('artists');

        return response()->json($song, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))],
            'artist_ids' => 'required|array', // Expecting an array of artist IDs
            'artist_ids.*' => 'exists:artists,id', // Each ID must exist in the artists table
        ]);

        $song = Song::create([
            'title' => $validated['title'],
            'album_id' => $validated['album_id'],
            'genre' => $validated['gerne']
        ]);

        $song->artists()->sync($validated['artist_ids']);

        
        // return response($song->load('artists'), 201, [
        //     'Content-Type' => 'application/json'
        // ]);

        return response()->json($song->load('artists'), 201);
    }
}
