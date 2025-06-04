<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller {
    // index for get all
    public function index() {
        $artists = Artist::all();
        return response()->json($artists, 200);
    }

    // show everything
    public function show($id) {
        $artistWithRelationships = Artist::with(['albums', 'songs'])->findOrFail($id);
        return response()->json($artistWithRelationships, 200);
    }

    public function showWithSongs($id) {
        $artistWithSongs = Artist::with('songs')->findOrFail($id);
        return response()->json($artistWithSongs, 200);
    }

    // ALTERNATIVE WAY: laravel automatically queries the database for that model instance based on the id provided in the URL
    public function showWithAlbums(Artist $artist) {
        $artist->load('albums');
        return response()->json($artist, 200);
    }

    // store for create
    public function store(Request $request) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|string|max:255|url',
            'album_ids' => 'nullable|array',
            'album_ids.*' => 'exists:albums,id',
            'song_ids' => 'nullable|array',
            'song_ids.*' => 'exists:songs,id'
        ]);

        // Create the artist first
        $artist = Artist::create([
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        // attach albums if provided
        if (!empty($validated['album_ids'])) {
            $artist->albums()->attach($validated['album_ids']);
        }

        // attach songs if provided
        if (!empty($validated['song_ids'])) {
            $artist->songs()->attach($validated['song_ids']);
        }

        // return the artist with relationships loaded
        return response()->json(
            $artist->load(['albums', 'songs']),
            201
        );
    }

    public function update(Request $request, $id) {
        $artist = Artist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|string|max:255|url',
            'album_ids' => 'nullable|array',
            'album_ids.*' => 'exists:albums,id',
            'song_ids' => 'nullable|array',
            'song_ids.*' => 'exists:songs,id'
        ]);

        // update basic attributes (only if provided)
        $artist->update([
            'name' => $validated['name'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        // sync relationships if provided (not attach)
        if (isset($validated['album_ids'])) {
            $artist->albums()->sync($validated['album_ids']);
        }

        if (isset($validated['song_ids'])) {
            $artist->songs()->sync($validated['song_ids']);
        }

        return response()->json($artist->load(['albums', 'songs']), 200);
    }

    public function destroy($id) {
        $artist = Artist::findOrFail($id);
        $artist->delete();

        return response()->json(['message' => 'Artist deleted successfully'], 200);
    }
}
