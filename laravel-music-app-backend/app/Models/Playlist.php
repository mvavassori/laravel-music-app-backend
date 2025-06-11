<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'user_id'
    ];

    const TYPES = [
        'custom' => 'Custom',
        'daily_mix' => 'Daily Mix',
    ];

    // a playlist belongs to one user only
    public function user() {
        return $this->belongsTo(User::class);
    }

    // many to many relationship. // songs can have (be part of) many playlists and playlists can have many songs.
    public function songs() {
        return $this->belongsToMany(Song::class, 'playlist_songs')
            ->orderBy('pivot_created_at') // Order by when song was added to playlist
            ->withTimestamps();
    }
}
