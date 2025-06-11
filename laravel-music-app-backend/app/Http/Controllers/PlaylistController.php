<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PlaylistServiceInterface;
use App\Models\Playlist;
use App\Http\Requests\PlaylistStoreRequest;
use App\Http\Requests\PlaylistUpdateRequest;
use App\Services\PlaylistService;
use Illuminate\Support\Facades\Log;

class PlaylistController extends Controller {
    private PlaylistServiceInterface $playlistService;
    public function __construct(PlaylistService $playlistService) {
        $this->playlistService = $playlistService;
    }

    public function show($id) {
        $playlist = $this->playlistService->getPlaylist($id);
        return response()->json($playlist, 200);
    }

    public function showUserPlaylists($userId) {
        $playlists = $this->playlistService->getUserPlaylists($userId);
        return response()->json($playlists, 200);
    }

    public function showDailyMixPlaylist($userId) {
        try {
            $playlistDailyMix = $this->playlistService->getDailyMixAsPlaylist($userId);
            return response()->json($playlistDailyMix, 200);
        } catch (\Throwable $th) {
            Log::error("Failed to create daily mix playlist.", [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json(['message' => 'An internal server error occurred. Please try again later.'], 500);
        }
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

    public function update(PlaylistUpdateRequest $request, $id) {
        try {
            $updatedPlaylist = $this->playlistService->updatePlaylist($id, $request->validated());
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
