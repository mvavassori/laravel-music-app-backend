<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtistStoreRequest;
use App\Http\Requests\ArtistUpdateRequest;
use App\Models\Artist;
use App\Services\ArtistService;
use Illuminate\Support\Facades\Log;

class ArtistController extends Controller {
    private ArtistService $artistService;

    public function __construct(ArtistService $artistService) {
        $this->artistService = $artistService;
    }

    // index for get all
    public function index() {
        $artists = $this->artistService->getAllArtists();
        return response()->json($artists, 200);
    }

    public function show($id) {
        $artist = $this->artistService->getArtist($id);
        return response()->json($artist, 200);
    }

    // show everything
    public function showWithContributions($id) {
        $artistWithContributions = $this->artistService->getArtistWithContributions($id);
        return response()->json($artistWithContributions, 200);
    }

    public function showWithSongs($id) {
        $artistWithSongs = $this->artistService->getArtistWithSongs($id);
        return response()->json($artistWithSongs, 200);
    }

    public function showWithAlbums($id) {
        $artistWithAlbums = $this->artistService->getArtistWithAlbums($id);
        return response()->json($artistWithAlbums, 200);
    }

    // store for create
    public function store(ArtistStoreRequest $request) {
        $artist = null; // not necessary, just for clarity; it's being passed by reference
        try {
            $artist = $this->artistService->createArtist($request->validated());
            //? added logging for learning purposes
            Log::info("Artist created: '{$artist->name}' with id {$artist->id}", ['id' => $artist->id]);

            // return the artist with relationships loaded
            return response()->json($artist,201);
        } catch (\Throwable $th) {
            Log::error("Failed to create artist and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(ArtistUpdateRequest $request, $id) {
        $artist = Artist::findOrFail($id);
        try {
            $artist = $this->artistService->updateArtist($artist, $request->validated());

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
        $this->artistService->deleteArtist($artist);

        Log::info("Artist deleted: '$artistName' with id {$artist->id}", ['id' => $artist->id]);
        return response()->noContent(204);
    }
}
