<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model {
    use HasFactory;

    protected $fillable = ['artist_id', 'role_id', 'contribution_type', 'contribution_id'];


    public function role() {
        return $this->belongsTo(Role::class); // searches for role_id in its own table
    }

    public function artist() {
        return $this->belongsTo(Artist::class); // searches for artist_id in its own table
    }

    public function contributable() {
        return $this->morphTo('contributable', 'contributable_type', 'contributable_id');
    }
}
