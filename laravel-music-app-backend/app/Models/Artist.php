<?php

namespace App\Models;

// use Database\Factories\ArtistFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artist extends Model {
    use HasFactory;

    // protected $table = 'my_artists'; // custom naming
    protected $fillable = [
        'name',
        'bio',
        'image_url',
    ];

    // protected static function newFactory(): ArtistFactory
    // {
    //     return ArtistFactory::new();
    // }

    // many to many relationship(s)
    // public function songs() {
    //     return $this->belongsToMany(Song::class, 'song_artist');
    // }

    // public function albums() {
    //     return $this->belongsToMany(Album::class, 'album_artist');
    // }

    public function contributions(){
        return $this->hasMany(Contribution::class);
    }
}
