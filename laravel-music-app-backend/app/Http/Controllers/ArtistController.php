<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//! todo completely change this controller. It's currently working with the old database design

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

    // ALTERNATIVE WAY: laravel automatically queries the database for that model instance based on the id provided in the url
    public function showWithAlbums(Artist $artist) {
        $artist->load('albums');
        return response()->json($artist, 200);
    }

    // store for create
    public function store(Request $request) {

        $validated = $request->validate([   // laravel automatically returns validation error and messages for any "problematic field". No need to manually create responses for validation errors.
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|string|max:255|url',
            'album_ids' => 'nullable|array',
            'album_ids.*' => 'exists:albums,id',
            'song_ids' => 'nullable|array',
            'song_ids.*' => 'exists:songs,id'
        ]);

        $artist = null; // not necessary, just for clarity; it's being passed by reference
        try {
            DB::transaction(function () use ($validated, &$artist) {
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
            });

            //? added logging for learning purposes
            Log::info("Artist created: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);

            // return the artist with relationships loaded
            return response()->json(
                $artist->load(['albums', 'songs']),
                201
            );
        } catch (\Throwable $th) {
            Log::error("Failed to create artist and associated relationships.", [
                'input' => $request->all(), // full input for context
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString() // trace for debugging
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
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

        try {
            DB::transaction(function () use ($validated, &$artist, $request) {
                //! wrong way of updating: it updates fields with the existing ones (not necessary)
                // $artist->update([
                //     'name' => $validated['name'] ?? $artist->name,
                //     'bio' => $validated['bio'] ?? $artist->bio,
                //     'image_url' => $validated['image_url'] ?? $artist->image_url,
                // ]);

                // only updates the fields that are actually provided in the request
                $artist->update($request->validated());

                // sync relationships if provided (not attach)
                if (isset($validated['album_ids'])) {
                    $artist->albums()->sync($validated['album_ids']);
                }

                if (isset($validated['song_ids'])) {
                    $artist->songs()->sync($validated['song_ids']);
                }
            });

            Log::info("Artist updated: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);

            return response()->json($artist->load(['albums', 'songs']), 200);

        } catch (\Throwable $th) {
            Log::error("Failed to update artist and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        $artist = Artist::findOrFail($id);
        $artist->delete();

        Log::info("Artist deleted: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);
        return response()->json(['message' => 'Artist deleted successfully'], 204);
    }
}
