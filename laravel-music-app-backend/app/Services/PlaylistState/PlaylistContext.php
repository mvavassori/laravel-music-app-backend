<?php

namespace App\Services\PlaylistState;

use App\Services\GenerateMix\GenerateMixStrategyInterface;
use App\Services\SongService;

class PlaylistContext {
    public readonly int $currentIndex;
    public readonly array $songIds;
    public readonly int $userId;
    public readonly bool $shouldGenerate;
    public readonly SongService $songService;
    public readonly GenerateMixStrategyInterface $generateMixStrategy;

    public function __construct(
        int $currentIndex,
        array $songIds,
        int $userId,
        bool $shouldGenerate,
        SongService $songService,
        GenerateMixStrategyInterface $generateMixStrategy
    ) {
        $this->currentIndex = $currentIndex;
        $this->songIds = $songIds;
        $this->userId = $userId;
        $this->shouldGenerate = $shouldGenerate;
        $this->songService = $songService;
        $this->generateMixStrategy = $generateMixStrategy;
    }
}