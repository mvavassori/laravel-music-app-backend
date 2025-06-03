<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SongController extends Controller {
    public function show($id) {
        return Song::findOrFail($id);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist_id' => 'required|exists:artists,id',
            'album' => 'nullable|string|max:255',
            'genre' => ['nullable', Rule::in(array_keys(Song::GENRES))]
        ]);

        return Song::create($validated);
    }
}
