<?php

namespace App\Repositories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Collection;

interface SongRepositoryInterface {
    public function all(): Collection;

    public function find(int $id): Collection|null;
    
    public function store(array $data): Song|null;

    public function update(Song $song, array $data): Song|null;

    public function delete(int $id): void;
}