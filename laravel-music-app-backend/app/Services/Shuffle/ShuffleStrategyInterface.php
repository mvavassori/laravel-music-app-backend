<?php

namespace App\Services\Shuffle;

use Illuminate\Database\Eloquent\Collection;

// 5. Define the interface that will define the method that will implemented in the strateg(ies)
interface ShuffleStrategyInterface {
    public function shuffle(Collection $songs): Collection;
}