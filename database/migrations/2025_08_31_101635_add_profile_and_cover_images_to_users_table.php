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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('profile_image_id')->nullable()->after('password');
            $table->foreignId('cover_image_id')->nullable()->after('profile_image_id');

            $table->foreign('profile_image_id')->references('id')->on('images')->onDelete('set null');
            $table->foreign('cover_image_id')->references('id')->on('images')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_image_id']);
            $table->dropForeign(['cover_image_id']);
            $table->dropColumn('profile_image_id');
            $table->dropColumn('cover_image_id');
        });
    }
};
