<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller {
    // index for get all
    public function index() {
        return Artist::all();
    }

    // show for get by id
    public function show($id) {
        return Artist::findOrFail($id);
    }

    public function showWithSongs($id) {
        // it makes 2 queries under the hood
        return Artist::with('songs')->findOrFail($id); // SELECT artists.*, songs.* FROM artists LEFT JOIN songs ON songs.artist_id = artists.id WHERE artists.id = 1;
    }

    // store for create
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|string|max:255|url'
        ]);

        return Artist::create($validated);
    }
}
