<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SongController extends Controller {
    public function show($id) {
        $song = Song::findOrFail($id);
        return response()->json($song, 200);
    }

    public function showWithContributions($id) {
        $song = Song::with(['contributions.role', 'contributions.artist'])->findOrFail($id);
        return response()->json($song, 200);
    }

    public function showWithArtists($id) {
        $song = Song::with('contributions.artist')->findOrFail($id);
        return response()->json($song, 200);
    }

    public function showWithAlbum($id) {
        $songWithAlbum = Song::with('album')->findOrFail($id);
        return response()->json($songWithAlbum, 200);
    }

    public function showComplete($id) {
        $songWithRelationships = Song::with(['album','contributions.role', 'contributions.artist'])->findOrFail($id);
        return response()->json($songWithRelationships, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))],
            'contributions' => 'required|array|min:1', // contribution field required, it must be an array and there must be at least one element in the array
            'contributions.*.artist_id' => 'required|exists:artists,id', // contributions.* = For EACH item in the contributions array. Look at the artist_id field. It must be present. The value must exist in the artists table's id column
            'contributions.*.role_id' => 'required|exists:roles,id', // contributions.* = For EACH item in the contributions array. Look at the role_id field. It must be present. The value must exist in the roles table's id column
        ]);

        $song  = null;

        try {
            DB::transaction(function() use ($validated, &$song) {
                $song = Song::create([
                    'title' => $validated['title'],
                    'album_id' => $validated['album_id'] ?? null,
                    'genre' => $validated['genre'] ?? null
                ]);

                // create the contribution
                $song->contributions()->createMany($validated['contributions']);
            });

            return response()->json($song->load(['contributions.artist', 'contributions.role']), 201);

        } catch (\Throwable $th) {
            Log::error("Failed to create song and associated relationships.", [
                'input' => $request->all(), // full input for context
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString() // trace for debugging
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $id) {
        $song = Song::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'album_id' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))],
            'contributions' => 'sometimes|array|min:1',
            'contributions.*.artist_id' => 'required|exists:artists,id',
            'contributions.*.role_id' => 'required|exists:roles,id'
        ]);

        try {
            DB::transaction(function() use ($validated, &$song, $request) {
                // update fields provided
                $song->update($request->validated());

                if (isset($validated['contributions'])) {
                    $song->contributions()->delete(); // remove existing
                    $song->contributions()->createMany($validated['contributions']);
                }
            });
        return response()->json($song->load(['contributions.artist', 'contributions.role']), 200);
        } catch (\Throwable $th) {
            Log::error("Failed to update song and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        $song = Song::findOrFail($id);
        $song->delete();

        return response()->json(['message' => 'Song deleted successfully'], 204);
    }
}
