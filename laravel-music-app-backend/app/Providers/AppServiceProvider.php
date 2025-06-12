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
        //? now when i inject the interface the service container will know that the implementation i want to inject is the second argument of the bind function
        // repository bindings
        $this->app->bind(\App\Contracts\Repositories\AlbumRepositoryInterface::class, \App\Repositories\MySQLAlbumRepository::class);
        $this->app->bind(\App\Contracts\Repositories\ArtistRepositoryInterface::class, \App\Repositories\MySQLArtistRepository::class);
        $this->app->bind(\App\Contracts\Repositories\PlayRepositoryInterface::class, \App\Repositories\MySQLPlayRepository::class);
        $this->app->bind(\App\Contracts\Repositories\PlaylistRepositoryInterface::class, \App\Repositories\MySQLPlaylistRepository::class);
        $this->app->bind(\App\Contracts\Repositories\RoleRepositoryInterface::class, \App\Repositories\MySQLRoleRepository::class);
        $this->app->bind(\App\Contracts\Repositories\SongRepositoryInterface::class, \App\Repositories\MySQLSongRepository::class);
        $this->app->bind(\App\Contracts\Repositories\UserRepositoryInterface::class, \App\Repositories\MySQLUserRepository::class);
        


        // service bindings
        $this->app->bind(\App\Contracts\Services\AlbumServiceInterface::class, \App\Services\AlbumService::class);
        $this->app->bind(\App\Contracts\Services\ArtistServiceInterface::class, \App\Services\ArtistService::class);
        $this->app->bind(\App\Contracts\Services\PlaylistServiceInterface::class, \App\Services\PlaylistService::class);
        $this->app->bind(\App\Contracts\Services\RoleServiceInterface::class, \App\Services\RoleService::class);
        $this->app->bind(\App\Contracts\Services\SongServiceInterface::class, \App\Services\SongService::class);
        $this->app->bind(\App\Contracts\Services\UserServiceInterface::class, \App\Services\UserService::class);
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
