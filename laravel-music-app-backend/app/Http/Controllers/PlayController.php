<?php

namespace App\Http\Controllers;

use App\Services\PlaylistService;
use App\Services\PlayService;
use App\Http\Requests\PlayStoreRequest;

class PlayController extends Controller {

    private PlayService $playService;
    private PlaylistService $playlistService;

    public function __construct(PlayService $playService, PlaylistService $playlistService) { // constructor dependency injection // Service container will instantiate the objects for me behind the scenes
        $this->playService = $playService;
        $this->playlistService = $playlistService;
    }
    public function store(PlayStoreRequest $request) {
        $play = $this->playService->createPlay($request->validated());
        return response()->json($play, 201);
    }

    public function generate($userId) {
        $dailyMix = $this->playlistService->generateDailyMix($userId);
        return response()->json($dailyMix, 200);
    }
}
