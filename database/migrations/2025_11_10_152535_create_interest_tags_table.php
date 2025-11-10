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
        Schema::create('interest_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        Schema::create('user_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('interest_tag_id')->constrained()->onDelete('cascade');
            $table->float('score')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'interest_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
        Schema::dropIfExists('interest_tags');
    }
};
