<?php

namespace App\Services;

use App\Models\Playlist;

class PlaylistService {
    private PlayService $playService;
    private SongService $songService;
    public function __construct(PlayService $playService, SongService $songService) { // constructor dependency injection
        $this->playService = $playService;
        $this->songService = $songService;
    }

    public function getTodaysDailyMix(int $userId): ?Playlist {
        return Playlist::with('songs')
            ->where('user_id', $userId)
            ->where('type', 'daily_mix')
            ->whereDate('created_at', today())
            ->first();
    }

    public function generateDailyMix($userId) {
        $topGenre = $this->playService->getTopGenreForUser($userId); // most listened genre by the user

        if (!$topGenre) {
            return collect(); // no top genre; return empty collection 
        }
        $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre, 10);  // 10 songs from that genre
        $mostListenedSongs = $this->playService->getMostListenedSongs($userId, 10); // most listened 10 songs by the user
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
            ->merge($mostListenedSongs) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all(); // returns back an array. NOT a collection.

        return $dailyMix;
    }

    public function getDailyMixAsPlaylist($userId) {
        // check if there's already a playlist for the current user for today, if so return it.
        $existingDailyMix = $this->getExistingDailyMix($userId);
        if ($existingDailyMix) {
            return $existingDailyMix;
        }

        $name = "Daily Mix " . date("Y-m-d");
        $dailyMixSongs = $this->generateDailyMix($userId);

        // dd($dailyMixSongs);

        $playlist = Playlist::create([
            'name' => $name,
            'description' => 'Default description given by your daily mix',
            'type' => 'daily_mix',
            'user_id' => $userId
        ]);

        $songIds = array_column($dailyMixSongs, 'id'); // get the ids from the dailyMixSongs array


        $this->addSongsByIds($playlist,$songIds);
        return $playlist->load('songs');
    }

    public function getExistingDailyMix($userId) {
        $existingDailyMix = Playlist::with('songs')->where('type', 'daily_mix')->where('user_id', $userId)->whereDate('created_at', '=', today())->first();
        return $existingDailyMix;
    }

    public function createCustomPlaylist($userId, $data) {
        $playlist = Playlist::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => 'custom',
            'user_id' => $userId
        ]);
        if (isset($data['song_ids']) && !empty($data['song_ids'])) {
            $songIds = $data['song_ids'];
            // $playlist->songs()->attach($songIds);
            $this->addSongsByIds($playlist,$songIds);

        }
        return $playlist->load('songs');
    }

    public function addSongsByIds(Playlist $playlist, array $songIds) {
        $playlist->songs()->attach($songIds);
    }

    public function getUserPlaylists(int $userId) {
        return Playlist::with('songs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPlaylist($id) {
        return Playlist::with(['songs', 'user'])->findOrFail($id);
    }

    public function updatePlaylist(Playlist $playlist, $data) {
        $playlist->update($data);

        if (isset($data['song_ids'])) {
            $this->addSongsByIds($playlist, $data['song_ids']);
        }

        return $playlist->load('songs');
    }

    public function deletePlaylist(Playlist $playlist) {
        $playlist->delete();
    }
}