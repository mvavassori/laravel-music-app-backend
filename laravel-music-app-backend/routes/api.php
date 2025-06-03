<?php

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

// Song routes
Route::post('/songs', [SongController::class, 'store']);
Route::get('/songs/{id}', [SongController::class, 'show']);
