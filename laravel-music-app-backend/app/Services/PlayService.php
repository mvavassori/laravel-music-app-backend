<?php

namespace App\Services;

use App\Models\Play;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class PlayService {
    public function createPlay($data) {
        return Play::create($data);
    }

    public function getMostListenedSongs($userId, $limit = 10) {
        // get all the rows that have the same user_id in the plays table 
        $mostListened = Song::whereHas('plays', function ($query) use ($userId) { // whereHas finds songs that the user has listened to at least once
            $query->where('user_id', $userId);
        })
            ->withCount(['plays' => function ($query) use ($userId) { // counts how many times the user has played each song and adds that number to the new column plays_count
                $query->where('user_id', $userId);
            }])
            ->orderByDesc('plays_count')
            ->limit($limit)
            ->get()
            ->makeHidden('plays_count'); // remove plays_count from result

        // alternative way    
        // $mostListened = Song::withCount(['plays' => fn($query) => $query->where('user_id', $userId)])
        //     ->having('plays_count', '>', 0) // we use this because WHERE can't be used with aggregate functions i.e. COUNT(*)
        //     ->orderByDesc('plays_count')
        //     ->limit($limit)
        //     ->get()
        //     ->makeHidden('plays_count');

        return $mostListened;
    }

    public function getTopGenreForUser($userId) {
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
}