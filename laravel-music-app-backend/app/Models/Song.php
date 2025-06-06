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
        return $this->belongsTo(Album::class);
    }

    public function contributions() {
        return $this->morphMany(Contribution::class, 'contributable');
    }
}
