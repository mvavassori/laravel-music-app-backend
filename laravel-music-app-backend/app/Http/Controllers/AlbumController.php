<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller {
    public function show($id) {
        $albumWithRelationships = Album::with(['songs', 'artists'])->findOrFail($id);
        return response()->json($albumWithRelationships, 200);
    }

    public function showWithSongs($id) {
        $albumWithSongs = Album::with('songs')->findOrFail($id);
        return response()->json($albumWithSongs, 200);
    }

    public function showWithArtists($id) {
        $albumWithArtists = Album::with('artists')->findOrFail($id);
        return response()->json($albumWithArtists, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'artist_ids' => 'required|array', // expecting an array of artist ids
            'artist_ids.*' => 'exists:artists,id', // each id must exist in the artists table
        ]);

        $album = Album::create([
            'title' => $validated['title'],
            'image_url' => $validated['image_url'] ?? null,
            'genre' => $validated['genre'],
            'description' => $validated['description'] ?? null,
        ]);


        // any ids not in the given array will be removed from the intermediate table.
        // use this when you don't need to "add relationships" over time; they're pretty static.
        // otherwise use:
        // if (!empty($validated['album_ids'])) {
        //     $album->artists()->attach($validated['artist_ids']);
        // }
        $album->artists()->sync($validated['artist_ids']);

        return response()->json($album->load('artists'), 201);
    }

    public function update(Request $request, $id) {
        $album = Album::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'sometimes|required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'artist_ids' => 'nullable|array',
            'artist_ids.*' => 'exists:artists,id',
        ]);

        $album->update([
            'title' => $validated['title'] ?? $album->title,
            'image_url' => $validated['image_url'] ?? $album->image_url,
            'genre' => $validated['genre'] ?? $album->genre,
            'description' => $validated['description'] ?? $album->description,
        ]);

        if (isset($validated['artist_ids'])) {
            $album->artists()->sync($validated['artist_ids']);
        }

        return response()->json($album->load('artists'), 200);
    }

    public function destroy($id) {
        $album = Album::findOrFail($id);
        $album->delete();

        return response()->json(['message' => 'Album deleted successfully'], 200);
    }
}
