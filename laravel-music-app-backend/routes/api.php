<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\PlaylistController;

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
Route::prefix('v1/artists')->name('api.v1.artists.')->group(function () {
    Route::post('/', [ArtistController::class, 'store'])->name('store')->middleware('throttle:60,1');
    Route::get('/', [ArtistController::class, 'index'])->name('index');
    Route::get('/{id}', [ArtistController::class, 'show'])->name('show');
    Route::get('/{id}/contributions', [ArtistController::class, 'showWithContributions'])->name('show.contributions');
    Route::get('/{id}/songs', [ArtistController::class, 'showWithSongs'])->name('show.songs');
    Route::get('/{id}/albums', [ArtistController::class, 'showWithAlbums'])->name('show.albums');
    Route::put('/{id}', [ArtistController::class, 'update'])->name('update');
    Route::delete('/{id}', [ArtistController::class, 'destroy'])->name('destroy');
});

// Song routes
Route::prefix('v1/songs')->name('api.v1.songs.')->group(function () {
    Route::post('/', [SongController::class, 'store'])->name('store')->middleware('throttle:60,1');
    Route::get('/{id}', [SongController::class, 'show'])->name('show');
    Route::get('/{id}/contributions', [SongController::class, 'showWithContributions'])->name('show.contributions');
    Route::get('/{id}/artists', [SongController::class, 'showWithArtists'])->name('show.artists');
    Route::get('/{id}/album', [SongController::class, 'showWithAlbum'])->name('show.album');
    Route::get('/{id}/complete', [SongController::class, 'showComplete'])->name('show.complete');
    Route::put('/{id}', [SongController::class, 'update'])->name('update');
    Route::delete('/{id}', [SongController::class, 'destroy'])->name('destroy');
});

// Album routes
Route::prefix('v1/albums')->name('api.v1.albums.')->group(function () {
    Route::post('/', [AlbumController::class, 'store'])->name('store')->middleware('throttle:60,1');
    Route::get('/{id}', [AlbumController::class, 'show'])->name('show');
    Route::get('/{id}/songs', [AlbumController::class, 'showWithSongs'])->name('show.songs');
    Route::get('/{id}/contributions', [AlbumController::class, 'showWithContributions'])->name('show.contributions');
    Route::get('/{id}/complete', [AlbumController::class, 'showComplete'])->name('show.complete');
    Route::put('/{id}', [AlbumController::class, 'update'])->name('update');
    Route::delete('/{id}', [AlbumController::class, 'destroy'])->name('destroy');
});

// Role routes
Route::prefix('v1/roles')->name('api.v1.roles.')->group(function () {
    Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('throttle:60,1');
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/{id}', [RoleController::class, 'show'])->name('show');
    Route::get('/name/{name}', [RoleController::class, 'showByName'])->name('show.byName');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
});


// User routes
Route::prefix('v1/users')->name(value: 'api.v1.users.')->group(function () {
    Route::post('/', [UserController::class, 'store'])->name('store')->middleware('throttle:60,1');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::get('/', [UserController::class, 'index'])->name('index');
});

// Plays routes
Route::prefix('v1/plays')->name(value: 'api.v1.plays.')->group(function () {
    Route::post('/', [PlayController::class, 'store'])->name('store');
});

// Playlist routes
Route::prefix('v1/playlists')->name(value: 'api.v1.playlists.')->group(function () {
    Route::post('/', [PlaylistController::class, 'store'])->name('store');
    Route::get('/{id}', [PlaylistController::class, 'show'])->name('show');
    Route::get('/user/{userId}', [PlaylistController::class, 'showUserPlaylists'])->name('show.user.playlists');
    Route::get('/daily-mix/{userId}', [PlaylistController::class, 'showDailyMixplaylist'])->name('show.dailyMix');
    Route::put('/{id}', [PlaylistController::class, 'update'])->name('update');
    Route::delete('/{id}', [PlaylistController::class, 'destroy'])->name('destroy');
    Route::get('/shuffle/{id}', [PlaylistController::class, 'shuffle'])->name('shuffle');
    Route::post('/next-song', [PlaylistController::class, 'next'])->name('next');
});