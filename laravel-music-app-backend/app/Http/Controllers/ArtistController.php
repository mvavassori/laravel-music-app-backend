<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArtistController extends Controller {
    // index for get all
    public function index() {
        $artists = Artist::all();
        return response()->json($artists, 200);
    }

    public function show($id) {
        $artist = Artist::findOrFail($id);
        return response()->json($artist, 200);
    }

    // show everything
    public function showWithContributions($id) {
        $artistWithContributions = Artist::with(['contributions' => function ($query) {
            $query->whereIn('contributable_type', [Song::class, Album::class])->with(['role', 'contributable']);
        }
        ])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FROM artists WHERE id = 1 LIMIT 1;
        //2 SELECT * FROM contributions WHERE artist_id = 1 AND contributable_type = ('App\\Models\\Song', 'App\\Models\\Album');
        //3 SELECT * FROM roles WHERE id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT * FROM songs WHERE id IN (1, 2, 3); // i numeri dipendono dai contributable_id (i.e. id in songs) trovati nelle contributions
        //5 SELECT * FROM albums WHERE id IN (1, 2); // i numeri dipendono dai contributable_id (i.e. id in albums) trovati nelle contribuzioni
        return response()->json($artistWithContributions, 200);
    }

    public function showWithSongs($id) {
        $artistWithSongs = Artist::with(['contributions' => function ($query) {
            $query->where('contributable_type', Song::class)->with(['role:id,name', 'contributable:id,title,genre']); // SELECT just id and name of the role // just id, title and genre of the song
        }
        ])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FORM roles;
        //2 SELECT * FROM artists WHERE artists.id = 1 LIMIT 1;
        //3 SELECT * FROM contributions WHERE contributions.artist_id IN (1) AND contributable_type = 'App\\Models\\Song';
        //3 SELECT id, name FROM roles WHERE roles.id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT id, title, genre FROM songs WHERE songs.id IN (1, 2, 3); // i numeri dipendono dai contributable_id (i.e. id in songs) trovati nelle contributions
        return response()->json($artistWithSongs, 200);
    }

    public function showWithAlbums($id) {
        $artistWithAlbums = Artist::with(['contributions' => function ($query) {
            $query->where('contributable_type', Album::class)->with(['role:id,name', 'contributable:id,title,genre']); // SELECT just id and name of the role // just id, title and genre of the album
        }
        ])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FROM artists WHERE artists.id = 1 LIMIT 1;
        //2 SELECT * FROM contributions WHERE contributions.artist_id IN (1) AND contributable_type = 'App\\Models\\Song';
        //3 SELECT id, name FROM roles WHERE id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT id, title, genre FROM albums WHERE id IN (1, 2); // i numeri dipendono dai contributable_id (i.e. id in albums) trovati nelle contribuzioni
        return response()->json($artistWithAlbums, 200);
    }

    // store for create
    public function store(Request $request) {

        $validated = $request->validate([   // laravel automatically returns validation error and messages for any "problematic field". No need to manually create responses for validation errors.
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|string|max:255|url'
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
            });

            //? added logging for learning purposes
            Log::info("Artist created: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);

            // return the artist with relationships loaded
            return response()->json(
                $artist,
                201
            );
        } catch (\Throwable $th) {
            Log::error("Failed to create artist and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
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
        ]);

        try {
            DB::transaction(function () use ($validated, &$artist, $request) {
                //? wrong way of updating: it updates fields with the existing ones (not necessary)
                $artist->update([
                    'name' => $validated['name'] ?? $artist->name,
                    'bio' => $validated['bio'] ?? $artist->bio,
                    'image_url' => $validated['image_url'] ?? $artist->image_url,
                ]);

                //! doesn't work
                // only updates the fields that are actually provided in the request
                // $artist->update($request->validated());
            });

            Log::info("Artist updated: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);

            return response()->json($artist, 200);

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
        $artistName = $artist->name; // save name before deleting
        $artist->delete();

        Log::info("Artist deleted: '$artistName' with id {$artist->id}", ['id' => $artist->id]);
        return response()->noContent(204);
    }
}
