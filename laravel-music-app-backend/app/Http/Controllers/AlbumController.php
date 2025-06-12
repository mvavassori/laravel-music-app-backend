<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AlbumServiceInterface;
use App\Http\Requests\AlbumStoreRequest;
use App\Http\Requests\AlbumUpdateRequest;
use Illuminate\Support\Facades\Log;

class AlbumController extends Controller {
    private AlbumServiceInterface $albumService;

    public function __construct(AlbumServiceInterface $albumService) {
        $this->albumService = $albumService;
    }
    public function show($id) {
        $album = $this->albumService->getAlbum($id);
        return response()->json($album, 200);
    }

    public function showWithSongs($id) {
        $albumWithSongs = $this->albumService->getAlbumWithSongs($id);
        return response()->json($albumWithSongs, 200);
    }

    public function showWithContributions($id) {
        $albumWithContributions = $this->albumService->getAlbumWithContributions($id);
        return response()->json($albumWithContributions, 200);
    }

    public function showComplete($id) {
        $albumComplete = $this->albumService->getAlbumComplete($id);
        return response()->json($albumComplete, 200);
    }

    public function store(AlbumStoreRequest $request) {
        try {
            $album = $this->albumService->createAlbum($request->validated());            
            return response()->json($album, 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create album and associated relationships.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(AlbumUpdateRequest $request, $id) {
        try {
            $updatedAlbum = $this->albumService->updateAlbum($id, $request->validated());
            return response()->json($updatedAlbum->load(['contributions.artist', 'contributions.role']), 200);
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
        $this->albumService->deleteAlbum($id);
        return response()->noContent(204);
    }
}
