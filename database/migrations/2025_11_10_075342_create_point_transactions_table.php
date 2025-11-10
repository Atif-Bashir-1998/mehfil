<?php

use App\Enums\PointTransactionType;
use App\Enums\PointType;
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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', PointTransactionType::values());
            $table->integer('points');
            $table->enum('action_type', PointType::values()); // post_created, boost_used, ad_created, etc.
            $table->text('description');
            $table->morphs('transactionable'); // Links to post, comment, ad, etc.
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
