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
Route::post('/artists', [ArtistController::class, 'store']);
Route::get('/artists', [ArtistController::class, 'index']);
Route::get('/artists/{id}', [ArtistController::class, 'show']);
Route::get('/artists/{id}/songs', [ArtistController::class, 'showWithSongs']);
Route::get('/artists/{artist}/albums', [ArtistController::class, 'showWithAlbums']); // made it to test laravel route model binding
Route::put('/artists/{id}', [ArtistController::class, 'update']);
Route::delete('/artists/{id}', [ArtistController::class, 'destroy']);


// Song routes
Route::post('/songs', [SongController::class, 'store']);
Route::get('/songs/{id}', [SongController::class, 'show']);
Route::get('/songs/{id}/artists', [SongController::class, 'showWithArtists']);
Route::put('/songs/{id}', [SongController::class, 'update']);
Route::delete('/songs/{id}', [SongController::class, 'destroy']);


// Album routes
Route::post('/albums', [AlbumController::class, 'store']);
Route::get('/albums/{id}', [AlbumController::class, 'show']);
Route::get('/albums/{id}/songs', [AlbumController::class, 'showWithSongs']);
Route::get('/albums/{id}/artists', [AlbumController::class, 'showWithArtists']);
Route::put('/albums/{id}', [AlbumController::class, 'update']);
Route::put('/albums/{id}', [AlbumController::class, 'destroy']);

