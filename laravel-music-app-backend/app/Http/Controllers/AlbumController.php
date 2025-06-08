<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumController extends Controller {
    public function show($id) {
        $album = Album::findOrFail($id);
        return response()->json($album, 200);
    }

    public function showWithSongs($id) {
        $albumWithSongs = Album::with('songs')->findOrFail($id); // SELECT FROM albums WHERE id = 25 LIMIT 1; // SELECT FROM songs WHERE songs.album_id IN (25);
        return response()->json($albumWithSongs, 200);
    }

    public function showWithContributions($id) {
        $album = Album::with(['contributions.role', 'contributions.artist'])->findOrFail($id);
        return response()->json($album, 200);
    }

    public function showComplete($id) {
        $albumWithRelationships = Album::with(['songs', 'contributions.role', 'contributions.artist'])->findOrFail($id);
        return response()->json($albumWithRelationships, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'contributions' => 'required|array|min:1', // contribution field required, it must be an array and there must be at least one element in the array
            'contributions.*.artist_id' => 'required|exists:artists,id', // contributions.* = For EACH item in the contributions array. Look at the artist_id field. It must be present. The value must exist in the artists table's id column
            'contributions.*.role_id' => 'required|exists:roles,id', // contributions.* = For EACH item in the contributions array. Look at the role_id field. It must be present. The value must exist in the roles table's id column
        ]);

        $album = null;

        try {
            DB::transaction(function () use ($validated, &$album) {
                $album = Album::create([
                    'title' => $validated['title'],
                    'image_url' => $validated['image_url'] ?? null,
                    'genre' => $validated['genre'],
                    'description' => $validated['description'] ?? null,
                ]);
                $album->contributions()->createMany($validated['contributions']);
            });

            return response()->json($album->load(['contributions.artist', 'contributions.role']), 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create album and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $id) {
        $album = Album::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'image_url' => 'nullable|url|max:255',
            'genre' => 'sometimes|required|in:' . implode(',', array_keys(Album::GENRES)),
            'description' => 'nullable|string',
            'contributions' => 'sometimes|array|min:1',
            'contributions.*.artist_id' => 'required|exists:artists,id',
            'contributions.*.role_id' => 'required|exists:roles,id'
        ]);

        try {
            DB::transaction(function () use ($validated, &$album, $request) {
                // update fields provided
                $album->update($request->validated());

                // update artist relationships using sync() if provided
                if (isset($validated['contributions'])) {
                    $album->contributions()->delete(); // remove existing
                    $album->contributions()->createMany($validated['contributions']);
                }
            });
            return response()->json($album->load(['contributions.artist', 'contributions.role']), 200);
        } catch (\Throwable $th) {
            Log::error("Failed to update album and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        $album = Album::findOrFail($id);
        $album->delete();

        return response()->json(['message' => 'Album deleted successfully'], 204);
    }
}
