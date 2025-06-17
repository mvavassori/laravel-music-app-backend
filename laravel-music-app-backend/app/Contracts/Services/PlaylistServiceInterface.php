<?php

namespace App\Contracts\Services;

interface PlaylistServiceInterface {
    public function getTodaysDailyMix($userId);
    public function generateDailyMix($userId);
    public function getDailyMixAsPlaylist($userId);
    public function createCustomPlaylist($userId, array $data);
    public function getUserPlaylists($userId);
    public function getPlaylist($id);
    public function updatePlaylist($id, array $data);
    public function deletePlaylist($id);

    // 2. As a second step i define what my service accepts and what it will return
    public function shufflePlaylist($id);
    // define next song method
    public function getNextSongInPlaylist(int $currentSongId, array $songIds, int $userId, bool $shouldGenerate);
}