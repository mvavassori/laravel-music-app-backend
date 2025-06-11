<?php

namespace App\Repositories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Collection;

interface AlbumRepositoryInterface {
    public function all(): Collection;

    public function find(int $id): Collection|null;
    
    public function store(array $data): Album|null;

    public function update(Album $album, array $data): Album|null;

    public function delete(int $id): void;
}