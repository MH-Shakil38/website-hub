<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['int_', 're_', 'pharma_'] as $prefix) {
            if (!Schema::hasTable($prefix . 'videos')) {
                Schema::create($prefix . 'videos', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('category')->default('walkthrough'); // walkthrough, 3d_tour, client_story, reel
                    $table->string('video_url'); // YouTube/Vimeo embed or uploaded MP4 URL
                    $table->string('thumbnail')->nullable();
                    $table->string('duration')->default('02:30');
                    $table->integer('sort_order')->default(1);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }

            if (Schema::hasTable($prefix . 'media') && !Schema::hasColumn($prefix . 'media', 'category')) {
                Schema::table($prefix . 'media', function (Blueprint $table) {
                    $table->string('category')->default('living')->after('title');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['int_', 're_', 'pharma_'] as $prefix) {
            Schema::dropIfExists($prefix . 'videos');
        }
    }
};
