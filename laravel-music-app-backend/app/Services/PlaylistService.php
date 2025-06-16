<?php

namespace App\Services;

use App\Models\Song;
use App\Models\Playlist;
// use App\Contracts\Services\SongServiceInterface;
use App\Services\Shuffle\ShuffleStrategyInterface;
use App\Contracts\Services\PlaylistServiceInterface;
use App\Services\GenerateMix\GenerateMixStrategyInterface;
// use App\Contracts\Repositories\PlayRepositoryInterface;
use App\Contracts\Repositories\PlaylistRepositoryInterface;

class PlaylistService implements PlaylistServiceInterface {
    private PlaylistRepositoryInterface $playlistRepository;
    // private PlayRepositoryInterface $playService;
    // private SongServiceInterface $songService;
    private GenerateMixStrategyInterface $generateMixStrategy;
    private ShuffleStrategyInterface $shuffleStrategy;
    public function __construct(PlaylistRepositoryInterface $playlistRepository, ShuffleStrategyInterface $shuffleStrategy, GenerateMixStrategyInterface $generateMixStrategy) { // constructor dependency injection // old: PlayRepositoryInterface $playService, SongServiceInterface $songService
        $this->playlistRepository = $playlistRepository;
        // $this->playService = $playService;
        // $this->songService = $songService;
        // inject the interface
        $this->shuffleStrategy = $shuffleStrategy;
        $this->generateMixStrategy = $generateMixStrategy;
    }

    public function getTodaysDailyMix($userId) {
        return $this->playlistRepository->findByUserAndType($userId, 'daily_mix', today());
    }

    public function generateDailyMix($userId) {
        // $topGenre = $this->playService->getTopGenreByUser($userId); // most listened genre by the user

        // if (!$topGenre) {
        //     return collect(); // no top genre; return empty collection 
        // }
        // $byGenreAtRandom = $this->songService->getSongsByGenreAtRandom($topGenre, 10);  // 10 songs from that genre
        // $mostListenedSongs = $this->playService->getMostPlayedSongsByUser($userId, 10); // most listened 10 songs by the user
        // $dailyMix = collect()   // collections are useful because they return a new collection; i.e. they don't modify the orignal collection
        //     ->merge($byGenreAtRandom) // adds the 10 songs from the top genre
        //     ->merge($mostListenedSongs) // adds the most listened 10
        //     ->unique('id')  // removes duplicates
        //     ->shuffle() // self expl
        //     ->all(); // returns back an array. NOT a collection.
        
        $dailyMixSongs = $this->generateMixStrategy->generate($userId);

        return $dailyMixSongs;
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
        $this->playlistRepository->attachSongs($playlist->id, $songIds);

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

    // 3. Third step actually implement the method defined in the interface
    public function shufflePlaylist($id) {
        // first find the playlist
        $playlist = $this->playlistRepository->findWithRelations($id, ['songs']);
        // 4. Algorithm part. Here we'll use the shuffle strategy interface to select which "shuffling algorithm" to use 
        $shuffledSongs = $this->shuffleStrategy->shuffle($playlist->songs);

        // return $playlist->setRelation('songs', $shuffledSongs);
        return $shuffledSongs->pluck('id');
    }

    public function getNextSongInPlaylist($currentSongId, array $songIds, $userId) {
        $currentIndex = array_search($currentSongId, $songIds); // find the index of the current song

        // if song not found
        if($currentIndex === false) {
            return [
                'song' => null,
                'song_ids' => $songIds
            ];
        }

        // if it's no the last song return the next song normally
        if($currentIndex < count($songIds) - 1) {
            $nextSongId = $songIds[$currentIndex + 1];
            return [
                'song' => Song::find($nextSongId),
                'song_ids' => $songIds
            ];
        }

        // if it's the last element
        
        // generate a new mix of songs tailored to the user
        $newSongs = $this->generateMixStrategy->generate($userId);
        $newSongIds = collect($newSongs)->pluck('id')->toArray();

        $newSongIds = array_diff($newSongIds, $songIds);

        // append new songs to the existing ones
        $updatedSongIds = array_merge($songIds, $newSongIds);

        $firstNewSong = $updatedSongIds[0];

        return [
            'song' => Song::find($firstNewSong),
            'song_ids' => $updatedSongIds
        ];
       
    }
}