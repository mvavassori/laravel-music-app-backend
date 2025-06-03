<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model {
    use HasFactory;

    protected $fillable = [
        'title', 'artist_id', 'album', 'genre'
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

    // defines the inverse of the hasMany relationship we defined in the Artist model
    public function artist() {
        return $this->belongsTo(Artist::class);
    }
}
