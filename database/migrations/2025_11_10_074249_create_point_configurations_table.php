<?php

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
        Schema::create('point_configurations', function (Blueprint $table) {
            $table->id();
            $table->enum('action_type', PointType::values());
            $table->string('description');
            $table->integer('points');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['action_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_configurations');
    }
};
