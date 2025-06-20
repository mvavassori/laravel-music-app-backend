<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'genre',
        'album_id'
    ];

    const GENRES = [
        'rock' => 'Rock',
        'pop' => 'Pop',
        'jazz' => 'Jazz',
        'classical' => 'Classical',
        'hip-hop' => 'Hip-Hop',
        'country' => 'Country',
        'electronic' => 'Electronic',
        'r&b' => 'R&B',
        'metal' => 'Metal',
        'folk' => 'Folk'
    ];

    // // now we make songs have more than one artist; i.e. many to many
    // public function artists() {
    //     return $this->belongsToMany(Artist::class, 'song_artist');
    // }

    public function album() {
        return $this->belongsTo(Album::class); // searches for album_id in its own table (songs)
    }

    public function contributions() {
        return $this->morphMany(Contribution::class, 'contributable'); // searches for contributable_id in contributions table when the contributable_type is App\Models\Song
    }

    public function plays() {
        return $this->hasMany(Play::class);
    }

    // many to many relationship. // songa can have (be part of) many playlists and playlists can have many songs.
    public function playlists() {
        return $this->belongsToMany(Playlist::class, 'playlist_songs')
            ->withTimestamps();
    }
}
