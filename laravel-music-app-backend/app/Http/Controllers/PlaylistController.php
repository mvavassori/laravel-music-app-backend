<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PlaylistServiceInterface;
use App\Http\Requests\NextSongGetRequest;
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
            Log::error("\n\n\nFailed to create playlist.", [
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
        $this->playlistService->deletePlaylist($id);
        return response()->noContent();
    }

    // 1. STARTED FROM HERE route to shuffle my playlist: it needs a playlist id, then we use the correct playlistService method to actually do the thing. This just makes http calls.
    public function shuffle($id) {
        $shuffledPlaylist = $this->playlistService->shufflePlaylist($id);
        return response()->json($shuffledPlaylist, 200);
    }

    public function next(NextSongGetRequest $request) {
        $next = $this->playlistService->getNextSongInPlaylist(
            $request->current_song_id,
            $request->song_ids,
            $request->user_id,
            $request->should_generate
        );

        return response()->json($next, 200);
    }
}
