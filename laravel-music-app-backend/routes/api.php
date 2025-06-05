<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SongController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$someVar = "hello";
$myFunc = fn() => $someVar;

// Route::get("/users/{id}", $myFunc);



// Artist routes
Route::prefix('v1/artists')->name('api.v1.')->group(function () {
    Route::post('/', [ArtistController::class, 'store'])->name('artists.store')->middleware('throttle:60,1'); // 60 requests per minute max
    Route::get('/', [ArtistController::class, 'index'])->name('artists.index');
    Route::get('/{id}', [ArtistController::class, 'show'])->name('artists.show');
    Route::get('/{id}/songs', [ArtistController::class, 'showWithSongs'])->name('artists.show.songs');
    Route::get('/{artist}/albums', [ArtistController::class, 'showWithAlbums'])->name('artists.show.albums'); // made it to test laravel route model binding
    Route::put('/{id}', [ArtistController::class, 'update'])->name('artists.update');
    Route::delete('/{id}', [ArtistController::class, 'destroy'])->name('artists.destroy');
});

// Song routes
Route::prefix('v1/songs')->name('api.v1.')->group(function () {
    Route::post('/', [SongController::class, 'store'])->name('songs.store');
    Route::get('/{id}', [SongController::class, 'show'])->name('songs.show');
    Route::get('/{id}/artists', [SongController::class, 'showWithArtists'])->name('songs.show.artists');
    Route::get('/{id}/album', [SongController::class, 'showWithAlbum'])->name('songs.show.album');
    Route::put('/{id}', [SongController::class, 'update'])->name('songs.update');
    Route::delete('/{id}', [SongController::class, 'destroy'])->name('songs.destroy');
});

// Album routes
Route::prefix('v1/albums')->name('api.v1.')->group(function () {
    Route::post('/', [AlbumController::class, 'store'])->name('albums.store');
    Route::get('/{id}', [AlbumController::class, 'show'])->name('albums.show');
    Route::get('/{id}/songs', [AlbumController::class, 'showWithSongs'])->name('albums.show.songs');
    Route::get('/{id}/artists', [AlbumController::class, 'showWithArtists'])->name('albums.show.artists');
    Route::put('/{id}', [AlbumController::class, 'update'])->name('albums.update');
    Route::put('/{id}', [AlbumController::class, 'destroy'])->name('albums.destroy');
});
