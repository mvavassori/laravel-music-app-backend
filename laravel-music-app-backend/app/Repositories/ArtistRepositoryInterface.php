<?php

namespace App\Repositories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Collection;

interface ArtistRepositoryInterface {
    public function all(): Collection;

    public function find(int $id): Collection|null;
    
    public function store(array $data): Artist|null;

    public function update(Artist $artist, array $data): Artist|null;

    public function delete(Artist $artist): void;
}