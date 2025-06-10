<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayController extends Controller {
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


        $topGenre = $this->topGenre($userId); // most listened genre by the user
        $genreTen = $this->genreTen($topGenre[0]);  // 10 songs from that genre
        $mostListenedTen = $this->mostListenedTen($userId); // most listened 10 songs by the user
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($genreTen) // adds the 10 songs from the top genre
            ->merge($mostListenedTen) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all();

        return response()->json($dailyMix, 200);
    }


    // ** helper methods **

    public function mostListenedTen($userId) {
        // get all the rows that have the same user_id in the plays table 
        $mostListenedTen = Song::whereHas('plays', function ($query) use ($userId) { // whereHas finds songs that the user has listened to at least once
            $query->where('user_id', $userId);
        })
            ->withCount(['plays' => function ($query) use ($userId) { // counts how many times the user has played each song and adds that number to the new column plays_count
                $query->where('user_id', $userId);
            }])
            ->orderByDesc('plays_count')
            ->limit(10)
            ->get()
            ->makeHidden('plays_count'); // remove plays_count from result

        // alternative way    
        // $mostListenedTen = Song::withCount(['plays' => fn($query) => $query->where('user_id', $userId)])
        //     ->having('plays_count', '>', 0) // we use this because WHERE can't be used with aggregate functions i.e. COUNT(*)
        //     ->orderByDesc('plays_count')
        //     ->limit(10)
        //     ->get()
        //     ->makeHidden('plays_count');


        return $mostListenedTen;
    }

    public function topGenre($userId) {
        $topGenre = Play::where('user_id', $userId) // grab all the plays of the specified user
            ->select('songs.genre', DB::raw('COUNT(*) as plays_count')) // SELECT songs.genre, COUNT(*) as plays_count FROM plays... // without DB:raw laravel would have assumed COUNT(*) was a column name.
            ->join('songs', 'plays.song_id', '=', 'songs.id')   // join with songs
            ->groupBy('songs.genre')
            ->orderByDesc('plays_count')
            ->limit(1) // top genre
            ->pluck('genre'); // don't include the counts i.e. play_count column
        // ->get();

        return $topGenre;
    }

    public function genreTen($genre) {
        $tenFromGenre = Song::where('genre', $genre)
            ->inRandomOrder() // picks songs with specified genre randomly
            ->limit(10)
            ->get();

        return $tenFromGenre;
    }
}
