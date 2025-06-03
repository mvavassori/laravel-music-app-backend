<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::table('songs', function (Blueprint $table) {
            // drop the old genre column
            $table->dropColumn('genre');
        });

        Schema::table('songs', function (Blueprint $table) {
            // add the new genre column as enum
            $table->enum('genre', [
                'rock',
                'pop',
                'jazz',
                'classical',
                'hip-hop',
                'country',
                'electronic',
                'r&b',
                'metal',
                'folk'
            ])->nullable()->after('album');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('genre');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->string('genre', 100)->nullable()->after('album');
        });
    }
};
