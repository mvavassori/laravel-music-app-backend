<?php

namespace App\Services;

use App\Models\Playlist;
use App\Services\PlaylistState\PlaylistContext;
use App\Contracts\Services\SongServiceInterface;
use App\Services\NextSong\NextSongStrategyFactory;
use App\Services\Shuffle\ShuffleStrategyInterface;
// use App\Contracts\Repositories\PlayRepositoryInterface;
use App\Contracts\Services\PlaylistServiceInterface;
use App\Services\PlaylistState\PlaylistStateFactory;
use App\Services\GenerateMix\GenerateMixStrategyInterface;
use App\Contracts\Repositories\PlaylistRepositoryInterface;

class PlaylistService implements PlaylistServiceInterface {
    private PlaylistRepositoryInterface $playlistRepository;
    // private PlayRepositoryInterface $playService;
    private SongServiceInterface $songService;
    private GenerateMixStrategyInterface $generateMixStrategy;
    private ShuffleStrategyInterface $shuffleStrategy;
    private PlaylistStateFactory $playlistStateFactory;
    private NextSongStrategyFactory $strategyFactory;
    public function __construct(PlaylistRepositoryInterface $playlistRepository, ShuffleStrategyInterface $shuffleStrategy, GenerateMixStrategyInterface $generateMixStrategy, SongServiceInterface $songService, PlaylistStateFactory $playlistStateFactory, NextSongStrategyFactory $strategyFactory) {
        $this->playlistRepository = $playlistRepository;
        $this->songService = $songService;
        $this->shuffleStrategy = $shuffleStrategy;
        $this->generateMixStrategy = $generateMixStrategy;
        $this->playlistStateFactory = $playlistStateFactory;
        $this->strategyFactory = $strategyFactory;
        // $this->playService = $playService;
    }

    public function getTodaysDailyMix($userId) {
        return $this->playlistRepository->findByUserAndType($userId, 'daily_mix', today());
    }

    // not necessary it's here just to show how it was before
    public function generateDailyMix($userId) {
        
        $dailyMixSongs = $this->generateMixStrategy->generate($userId);
        //! old
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
            $this->playlistRepository->attachSongs($playlist->id, $songIds);
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
        return $shuffledSongs->pluck('id'); // for simplicity in this case; otherwise i would have returned the collection above
    }

    public function getNextSongInPlaylist(int $currentSongId, array $songIds, int $userId, bool $shouldGenerate = false) {

        // find the index of the current song
        $currentIndex = array_search($currentSongId, $songIds);

        //? state design pattern implementation
        // // let factory decide which state we're in i.e. either EndOfPlaylistState|MiddleOfPlaylistState|SongNotFoundState
        // $state = $this->playlistStateFactory->createState($currentIndex, $songIds);

        // // all the context each of the possible playlist state mighe need 
        // $context = new PlaylistContext(
        //     currentIndex: $currentIndex === false ? -1 : $currentIndex, // if there's a valid currentIndex send it, otherwise send -1 (currentIndex is expected to be an int)
        //     songIds: $songIds,
        //     userId: $userId,
        //     shouldGenerate: $shouldGenerate,
        //     songService: $this->songService,
        //     generateMixStrategy: $this->generateMixStrategy
        // );

        // return $state->getNextSong($context);

        //? strategy design pattern implementation

        
        // let factory decide which state we're in i.e. either EndOfPlaylistStrategy|MiddleOfPlaylistStrategy|SongNotFoundStrategy
        $strategy = $this->strategyFactory->create($currentIndex, $songIds);
        
        $nextSongInPlaylist = $strategy->execute([
            'currentIndex' => $currentIndex,
            'song_ids' => $songIds,
            'user_id' => $userId,
            'shouldGenerate' => $shouldGenerate
        ]);

        return $nextSongInPlaylist;
        
        //! old, if statements approach
        // // if song id not found in the song_ids array
        // if ($currentIndex === false) {
        //     return [
        //         'song' => null,
        //         'song_ids' => $songIds
        //     ];
        // }

        // // if it's no the last song return the next song (standard)
        // if ($currentIndex < count($songIds) - 1) {
        //     $nextSongId = $songIds[$currentIndex + 1];
        //     return [
        //         'song' => $this->songService->getSong($nextSongId),
        //         'song_ids' => $songIds
        //     ];
        // }

        // // if it's the last element and shouldGenerate is set to true
        // if ($shouldGenerate) {
        //     return $this->extendPlaylistAndGetNext($songIds, $userId);
        // }

        // // if it's the last element and shouldGenerate is set to false just return null song and the normal song_ids
        // return [
        //     'song' => null,
        //     'song_ids' => $songIds
        // ];
    }

    private function extendPlaylistAndGetNext(array $songIds, $userId) {
        // generate a new mix of songs tailored to the user
        $newSongs = $this->generateMixStrategy->generate($userId);
        $newSongIds = collect($newSongs)->pluck('id')->toArray();

        $uniqueNewSongIds = array_values(array_diff($newSongIds, $songIds));

        // append new songs to the existing ones
        $updatedSongIds = array_merge($songIds, $uniqueNewSongIds);

        // if there are no new songs to add
        if (!isset($uniqueNewSongIds[0])) {
            return [
                'song' => null,
                'song_ids' => $songIds
            ];
        }

        return [
            'song' => $this->songService->getSong($uniqueNewSongIds[0]),
            'song_ids' => $updatedSongIds
        ];
    }
}