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

        // if there's already a daily mix return it
        $existingDailyMix = $this->generateDailyMix($userId);

        if ($existingDailyMix) {
            return $existingDailyMix;
        }

        $topGenre = $this->playService->getTopGenreForUser($userId); // most listened genre by the user

        if (!$topGenre) {
            return collect(); // no top genre; return empty collection 
        }

        $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre[0], 10);  // 10 songs from that genre
        $mostListenedSongs = $this->playService->getMostListenedSongs($userId, 10); // most listened 10 songs by the user
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
            ->merge($mostListenedSongs) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all();

        return $dailyMix;
    }

    public function createDailyMixAsPlaylist($userId) {
        $name = "Daily Mix " . date("Y-m-d");
        $dailyMixSongs = $this->generateDailyMix($userId);

        $playlist = Playlist::create([
            'name' => $name,
            'description' => 'Default description given by your daily mix',
            'type' => 'daily_mix',
            'user_id' => $userId
        ]);

        $songIds = $dailyMixSongs->pluck('id')->toArray();

        $playlist->songs()->attach($songIds);
        return $playlist->load('songs');
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
            $playlist->songs()->attach($songIds);
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