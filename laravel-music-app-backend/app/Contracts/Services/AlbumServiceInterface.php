<?php

namespace App\Contracts\Services;

interface AlbumServiceInterface {
    public function getAlbum($id);
    public function getAlbumWithSongs($id);
    public function getAlbumWithContributions($id);
    public function getAlbumComplete($id);
    public function createAlbum(array $data);
    public function updateAlbum($id, array $data);
    public function deleteAlbum($id);
}