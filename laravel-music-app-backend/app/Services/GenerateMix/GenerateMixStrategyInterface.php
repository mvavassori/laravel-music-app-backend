<?php

namespace App\Services\GenerateMix;

use Illuminate\Database\Eloquent\Collection;

interface GenerateMixStrategyInterface {
    public function generate($userId): array|Collection ;
}