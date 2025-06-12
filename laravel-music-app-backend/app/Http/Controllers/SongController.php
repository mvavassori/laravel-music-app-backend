<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SongServiceInterface;
use App\Http\Requests\SongStoreRequest;
use App\Http\Requests\SongUpdateRequest;
use App\Models\Song;
use Illuminate\Support\Facades\Log;

class SongController extends Controller {

    private SongServiceInterface $songService;

    public function __construct(SongServiceInterface $songService) {
        $this->songService = $songService;
    }

    public function show($id) {
        $song = $this->songService->getSong($id);
        return response()->json($song, 200);
    }

    public function showWithContributions($id) {
        $songWithContributions = $this->songService->getSongWithContributions($id);
        return response()->json($songWithContributions, 200);
    }


    public function showWithArtists($id) {
        $songWithArtists = $this->songService->getSongWithArtists($id);
        return response()->json($songWithArtists, 200);
    }

    public function showWithAlbum($id) {
        $songWithAlbum = $this->songService->getSongWithAlbum($id);
        return response()->json($songWithAlbum, 200);
    }

    public function showComplete($id) {
        $songComplete = $this->songService->getSongComplete($id);
        return response()->json($songComplete, 200);
    }

    public function store(SongStoreRequest $request) {
        try {
            $song = $this->songService->createSong($request->validated());
            return response()->json($song, 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create song and associated relationships.", [
                'input' => $request->all(), // full input for context
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString() // trace for debugging
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(SongUpdateRequest $request, $id) {
        $song = Song::findOrFail($id);

        try {
            $updatedsong = $this->songService->updateSong($song, $request->validated());
            return response()->json($updatedsong, 200);
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
        $this->songService->deleteSong($id);
        return response()->noContent(204);
    }
}
