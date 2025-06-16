<?php

namespace App\Services\Shuffle;

use Illuminate\Database\Eloquent\Collection;

// 6. define the strategy; write the shuffle method for this strategy
class RandomShuffleStrategy implements ShuffleStrategyInterface {
    public function shuffle($songs): Collection {
        return $songs->shuffle();
    }
}