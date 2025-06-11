<?php
namespace App\Repositories;

use App\Contracts\Repositories\ArtistRepositoryInterface;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Support\Facades\DB;

class MySQLArtistRepository implements ArtistRepositoryInterface {
    public function all() {
        return Artist::all();
    }

    public function find($id) {
        return Artist::findOrFail($id);
    }

    public function create(array $data) {
        DB::transaction(function () use ($data) {
            return Artist::create([
                'name' => $data['name'],
                'bio' => $data['bio'] ?? null,
                'image_url' => $data['image_url'] ?? null,
            ]);
        });
    }

    public function update($id, array $data) {
        DB::transaction(function () use ($data, $id) {
            $artist = $this->find($id);
            $artist->update($data);
            return $artist;
        });
    }

    public function delete($id) {
        return Artist::destroy($id);
    }

    public function findWithContributions($id) {
        return Artist::with(['contributions' => function ($query) {
            $query->whereIn('contributable_type', [Song::class, Album::class])->with(['role', 'contributable']);
        }])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FROM artists WHERE id = 1 LIMIT 1;
        //2 SELECT * FROM contributions WHERE artist_id = 1 AND contributable_type = ('App\\Models\\Song', 'App\\Models\\Album');
        //3 SELECT * FROM roles WHERE id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT * FROM songs WHERE id IN (1, 2, 3); // i numeri dipendono dai contributable_id (i.e. id in songs) trovati nelle contributions
        //5 SELECT * FROM albums WHERE id IN (1, 2); // i numeri dipendono dai contributable_id (i.e. id in albums) trovati nelle contribuzioni
    }

    public function findWithSongs($id) {
        return Artist::with(['contributions' => function ($query) {
            $query->where('contributable_type', Song::class)->with(['role:id,name', 'contributable:id,title,genre']); // SELECT just id and name of the role // just id, title and genre of the song
        }
        ])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FORM roles;
        //2 SELECT * FROM artists WHERE artists.id = 1 LIMIT 1;
        //3 SELECT * FROM contributions WHERE contributions.artist_id IN (1) AND contributable_type = 'App\\Models\\Song';
        //3 SELECT id, name FROM roles WHERE roles.id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT id, title, genre FROM songs WHERE songs.id IN (1, 2, 3); // i numeri dipendono dai contributable_id (i.e. id in songs) trovati nelle contributions
    }

    public function findWithAlbums($id) {
        return Artist::with(['contributions' => function ($query) {
            $query->where('contributable_type', Album::class)->with(['role:id,name', 'contributable:id,title,genre']); // SELECT just id and name of the role // just id, title and genre of the album
        }
        ])->findOrFail($id);
        // above queries explained:
        //1 SELECT * FROM artists WHERE artists.id = 1 LIMIT 1;
        //2 SELECT * FROM contributions WHERE contributions.artist_id IN (1) AND contributable_type = 'App\\Models\\Song';
        //3 SELECT id, name FROM roles WHERE id IN (1, 2, 3, 4); // i numeri dipendono dai role_id trovati nelle contributions
        //4 SELECT id, title, genre FROM albums WHERE id IN (1, 2); // i numeri dipendono dai contributable_id (i.e. id in albums) trovati nelle contribuzioni
    }
}