<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->morphs('contributable'); // contributable_id and contributable_type
            $table->timestamps();
        });

        Schema::dropIfExists('song_artist');
        Schema::dropIfExists('album_artist');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');

        Schema::create('song_artist', function (Blueprint $table) {
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->foreignId('artist_id')->constrained()->onDelete('cascade');
            $table->primary(['song_id', 'artist_id']);
        });

        Schema::create('album_artist', function (Blueprint $table) {
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->foreignId('artist_id')->constrained()->onDelete('cascade');
            $table->primary(['album_id', 'artist_id']);
        });
    }
};
