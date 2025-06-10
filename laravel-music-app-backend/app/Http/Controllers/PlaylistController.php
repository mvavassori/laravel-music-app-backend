<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Http\Requests\PlaylistStoreRequest;
use App\Http\Requests\PlaylistUpdateRequest;
use App\Services\PlaylistService;
use Illuminate\Support\Facades\Log;

class PlaylistController extends Controller {
    private PlaylistService $playlistService;
    public function __construct(PlaylistService $playlistService) {
        $this->playlistService = $playlistService;
    }

    public function show($userId) {
        $playlist = $this->playlistService->getPlaylist($userId);
        return response()->json($playlist, 200);
    }

    public function showUserPlaylists($userId) {
        $playlists = $this->playlistService->getUserPlaylists($userId);
        return response()->json($playlists, 200);
    }

    public function store(PlaylistStoreRequest $request) {
        $validated = $request->validated();
        try {
            $playlist = $this->playlistService->createCustomPlaylist($validated['user_id'], $request->validated());
            return response()->json($playlist, 201);
        } catch (\Throwable $th) {
            Log::error("Failed to create playlist.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function update(PlaylistUpdateRequest $request, $userId) {
        $playlist = Playlist::findOrFail($userId);
        try {
            $updatedPlaylist = $this->playlistService->updatePlaylist($playlist, $request->validated());
            return response()->json($updatedPlaylist, 200);
        } catch (\Throwable $th) {
            Log::error("Failed to update playlist.", [
                'input' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
    }

    public function destroy($id) {
        $playlist = Playlist::findOrFail($id);
        $this->playlistService->deletePlaylist($playlist);

        return response()->noContent();
    }
}
