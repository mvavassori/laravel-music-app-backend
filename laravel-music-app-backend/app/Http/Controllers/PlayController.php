<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\PlayRepositoryInterface;
use App\Http\Requests\PlayStoreRequest;

class PlayController extends Controller {

    private PlayRepositoryInterface $playRepository;

    public function __construct(PlayRepositoryInterface $playRepository) { // constructor dependency injection // Service container will instantiate the objects for me behind the scenes
        $this->playRepository = $playRepository;
    }
    public function store(PlayStoreRequest $request) {
        $play = $this->playRepository->create($request->validated());
        return response()->json($play, 201);
    }

    // public function generate($userId) {
    //     $dailyMix = $this->playlistService->generateDailyMix($userId);
    //     return response()->json($dailyMix, 200);
    // }
}
