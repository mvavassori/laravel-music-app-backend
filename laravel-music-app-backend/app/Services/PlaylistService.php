<?php

namespace App\Services;

use App\Contracts\Repositories\PlaylistRepositoryInterface;
use App\Contracts\Repositories\PlayRepositoryInterface;
use App\Contracts\Services\PlaylistServiceInterface;
use App\Contracts\Services\SongServiceInterface;
use App\Models\Playlist;

class PlaylistService implements PlaylistServiceInterface {
    private PlaylistRepositoryInterface $playlistRepository;
    private PlayRepositoryInterface $playService;
    private SongServiceInterface $songService;
    public function __construct(PlaylistRepositoryInterface $playlistRepository, PlayRepositoryInterface $playService, SongServiceInterface $songService) { // constructor dependency injection
        $this->playlistRepository = $playlistRepository;
        $this->playService = $playService;
        $this->songService = $songService;
    }

    public function getTodaysDailyMix($userId) {
        return $this->playlistRepository->findByUserAndType($userId, 'daily_mix', today());
    }

    public function generateDailyMix($userId) {
        $topGenre = $this->playService->getTopGenreByUser($userId); // most listened genre by the user

        if (!$topGenre) {
            return collect(); // no top genre; return empty collection 
        }
        $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre, 10);  // 10 songs from that genre
        // dd($byGenreAtRandom);
        $mostListenedSongs = $this->playService->getMostPlayedSongsByUser($userId, 10); // most listened 10 songs by the user
        // dd($mostListenedSongs);
        $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
            ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
            ->merge($mostListenedSongs) // adds the most listened 10
            ->unique('id')  // removes duplicates
            ->shuffle() // self expl
            ->all(); // returns back an array. NOT a collection.
        
        // dd($dailyMix);

        return $dailyMix;
    }

    public function getDailyMixAsPlaylist($userId) {
        // check if there's already a playlist for the current user for today, if so return it.
        $existingDailyMix = $this->getTodaysDailyMix($userId);
        if ($existingDailyMix) {
            return $existingDailyMix;
        }

        $name = "Daily Mix " . date("Y-m-d");
        $dailyMixSongs = $this->generateDailyMix($userId);

        // dd($dailyMixSongs);

        $playlist = $this->playlistRepository->create([
            'name' => $name,
            'description' => 'Default description given by your daily mix',
            'type' => 'daily_mix',
            'user_id' => $userId
        ]);

        $songIds = array_column($dailyMixSongs, 'id'); // get the ids from the dailyMixSongs array
        // dd($playlist->id);
        $this->playlistRepository->attachSongs($playlist->id, $songIds);
        // dd($this->playlistRepository->findWithRelations($playlist->id, ['songs']));

        return $this->playlistRepository->findWithRelations($playlist->id, ['songs']);
    }

    public function createCustomPlaylist($userId, $data) {
        $playlist = $this->playlistRepository->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => 'custom',
            'user_id' => $userId
        ]);
        if (isset($data['song_ids']) && !empty($data['song_ids'])) {
            $songIds = $data['song_ids'];
            $this->playlistRepository->attachSongs($playlist, $songIds);
        }
        return $this->playlistRepository->findWithRelations($playlist->id, ['songs']);
    }

    public function getUserPlaylists($userId) {
        return Playlist::with('songs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPlaylist($id) {
        return $this->playlistRepository->findWithRelations($id, ['songs', 'user']);
    }

    public function updatePlaylist($id, $data) {
        $playlist = $this->playlistRepository->find($id);

        if (isset($data['song_ids'])) {
            $this->playlistRepository->attachSongs($playlist->id, $data['song_ids']);
        }

        return $this->playlistRepository->findWithRelations($id, ['songs']);
    }

    public function deletePlaylist($id) {
        return $this->playlistRepository->delete($id);
    }
}