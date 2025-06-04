<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlbumController extends Controller
{
    public function show($id) {
        return Album::findOrFail($id);
    }

    public function showWithSongs(Album $album) {
        // eager load the 'songs' relationship
        $album->load('songs');

        return response()->json($album, 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'artist_ids' => 'required|array', // Expecting an array of artist IDs
            'artist_ids.*' => 'exists:artists,id', // Each ID must exist in the artists table
        ]);

        $album = Album::create([
            'title' => $validated['title'],
            'image_url' => $validated['image_url'] ?? null,
            'genre' => $validated['genre'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach artists to the album (many-to-many relationship)
        // The sync method will attach the given IDs to the model.
        // Any IDs not in the given array will be removed from the pivot table.
        $album->artists()->sync($validated['artist_ids']);

        return response()->json($album->load('artists'), 201);

    }
}
