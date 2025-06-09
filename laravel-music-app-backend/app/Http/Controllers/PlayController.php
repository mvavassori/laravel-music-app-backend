<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'song_id' => 'required|exists:songs,id'
        ]);

        $play = Play::create($validated);

        return response()->json($play, 201);
    }

    public function generate($userId) {
        // 10 most listened songs
        // 10 songs from the most listened genre

        $topGenre = $this->topGenre($userId);
        $genreTen = $this->genreTen($topGenre[0]);
        $mostListenedTen = $this->mostListenedTen($userId);
        $dailyMix = collect()
            ->merge($genreTen)
            ->merge($mostListenedTen)
            ->all();

        return response()->json($dailyMix, 200);
    }
    

    // ** helper methods **

    public function mostListenedTen($userId) {
        // get all the rows that have the same user_id in the plays table 
        $mostListenedTen = Song::whereHas('plays', function($query) use ($userId) { // whereHas finds songs that the user has listened to
            $query->where('user_id', $userId);
        })
        ->withCount(['plays' => function($query) use ($userId){ // counts how many times the user has played each song
            $query->where('user_id', $userId);
        }])
        ->orderByDesc('plays_count')
        ->limit(10)
        ->get()
        ->makeHidden('plays_count'); // remove plays_count from 

        return  $mostListenedTen;
    }

    public function topGenre($userId) {
        $topGenre = Play::where('user_id', $userId)
            ->join('songs', 'plays.song_id', '=' ,'songs.id')
            ->select('songs.genre', DB::raw('COUNT(*) as plays_count'))
            ->groupBy('songs.genre')
            ->limit(1) // top genres
            ->pluck('genre'); // don't include the counts i.e. play_count column
            // ->get();

        return $topGenre;
    }

    public function genreTen($genre) {
        $tenFromGenre = Song::where('genre', $genre)
            ->limit(10)
            ->get();

        return $tenFromGenre;
    }
}
