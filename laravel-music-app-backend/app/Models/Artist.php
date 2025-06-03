<?php

namespace App\Models;

use Database\Factories\ArtistFactory;
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

    // this methiod tells laravel that an Artist can have multiple Song records associated with it
    public function songs() {
        return $this->hasMany(Song::class); // Song::class tell laravel which other model is in on the "many" side of the realtionship
    }
}
