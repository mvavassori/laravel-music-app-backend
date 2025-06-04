<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller {
    // index for get all
    public function index() {
        // list artist with its albums
        $artists = Artist::with('albums')->get();
        return response()->json($artists);
    }

    // show for get by id
    public function show($id) {
        return Artist::findOrFail($id);
    }

    public function showWithSongs(Artist $artist) {
        $artist->load('songs');

        return response()->json($artist, 200);
    }

    // laravel automatically queries the database for that model instance based on the ID provided in the URL
    public function showWithAlbums(Artist $artist) {
        $artist->load('albums');
        return response()->json($artist);
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
