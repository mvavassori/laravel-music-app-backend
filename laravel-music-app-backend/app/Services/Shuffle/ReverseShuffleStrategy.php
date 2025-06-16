<?php

namespace App\Services\Shuffle;

use Illuminate\Database\Eloquent\Collection;

class ReverseShuffleStrategy implements ShuffleStrategyInterface {
    public function shuffle($songs): Collection {
        return $songs->reverse()->values(); // without values() it won't re-index the values. E.g. 20, 19, 18... instead of 1,2,3,4...
    }
}