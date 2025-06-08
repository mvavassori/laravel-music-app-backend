<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'image_url',
        'genre',
        'description'
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

    // public function artists() {
    //     return $this->belongsToMany(Artist::class, 'album_artist');
    // }

    public function songs() {
        return $this->hasMany(Song::class); // searches for album_id in the songs table
    }

    public function contributions() {
        return $this->morphMany(Contribution::class, 'contributable'); // searches for contributable_id in contributions table when the contributable_type is App\Models\Album
    }
}
