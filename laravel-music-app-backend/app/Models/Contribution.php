<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model {
    use HasFactory;

    protected $fillable = ['artist_id', 'role_id'];


    // every contrbution has a role (role_id)
    public function role() {
        return $this->belongsTo(Role::class);
    }

    // every contrbution has an artist (artist_id)
    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function contributable() {
        return $this->morphTo('contributable', 'contributable_type', 'contributable_id');
    }
}
