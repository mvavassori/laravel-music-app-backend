<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        // repository bindings
        // now when i inject the interface the service container will know that the implementation i want to inject is the second argument of the bind function
        $this->app->bind(\App\Contracts\Repositories\ArtistRepositoryInterface::class, \App\Repositories\MySQLArtistRepository::class);
        $this->app->bind(\App\Contracts\Repositories\PlaylistRepositoryInterface::class, \App\Repositories\MySQLPlaylistRepository::class);

        // service bindings
        $this->app->bind(\App\Contracts\Services\ArtistServiceInterface::class, \App\Services\ArtistService::class);
        $this->app->bind(\App\Contracts\Services\PlaylistServiceInterface::class, \App\Services\PlaylistService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() {
        // log sql queries
        DB::listen(function ($query) {
            Log::info(
                $query->sql,
                [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]
            );
        });
    }
}
