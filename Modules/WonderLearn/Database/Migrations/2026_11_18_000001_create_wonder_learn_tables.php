<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Standard Module CMS Tables (wl_*)
        // =====================================================================
        if (!Schema::hasTable('wl_pages')) {
            Schema::create('wl_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->longText('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_menus')) {
            Schema::create('wl_menus', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('url');
                $table->string('location')->default('header'); // header, footer
                $table->string('target')->default('_self');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_sliders')) {
            Schema::create('wl_sliders', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_url')->nullable();
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_media')) {
            Schema::create('wl_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->nullable()->default('wonderlearn');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('185 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_videos')) {
            Schema::create('wl_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->default('Learning');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('03:20');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_themes')) {
            Schema::create('wl_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#1D58B8');
                $table->string('secondary_color')->default('#169E53');
                $table->string('font_family')->default('Fredoka & Nunito');
                $table->string('layout_style')->default('WonderLearn Magical Cloud Theme');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_users')) {
            Schema::create('wl_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Lead Educator');
                $table->string('status')->default('Active');
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wl_settings')) {
            Schema::create('wl_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }

        // =====================================================================
        // 2. WonderLearn Domain CRUD Tables (wl_*)
        // =====================================================================

        // 2.1 Explore by Topic (Math Magic, Science Lab, Reading Land, Art Studio, Geography, Life Skills)
        if (!Schema::hasTable('wl_topics')) {
            Schema::create('wl_topics', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('card_bg')->default('#FFF5D6');
                $table->string('border_color')->default('#FDE68A');
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->string('age_group')->default('Age 4 to 12');
                $table->integer('activities_count')->default(24);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.2 Magical Stories (The Starry Balloon, Luna and the Little Clock, The Brave Little Sailor, The Secret Garden)
        if (!Schema::hasTable('wl_stories')) {
            Schema::create('wl_stories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category_tag')->default('Friendly'); // Friendly, Friendship, Adventure, Nature
                $table->string('tag_bg')->default('#E0F2FE');
                $table->string('tag_color')->default('#0284C7');
                $table->string('image')->nullable();
                $table->text('summary')->nullable();
                $table->longText('story_content')->nullable();
                $table->string('reading_time')->default('5 min read');
                $table->string('age_range')->default('Ages 4-10');
                $table->boolean('is_featured')->default(true);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.3 Play & Learn Interactive Games
        if (!Schema::hasTable('wl_games')) {
            Schema::create('wl_games', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category')->default('Math Magic');
                $table->string('game_type')->default('quiz'); // quiz, math, balloon, puzzle, memory
                $table->string('question_prompt')->nullable();
                $table->string('correct_answer')->nullable();
                $table->json('options_json')->nullable();
                $table->string('image')->nullable();
                $table->string('card_bg')->default('#7DD3FC');
                $table->unsignedInteger('play_count')->default(1200);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.4 Featured Adventures Carousel (Island of Imagination)
        if (!Schema::hasTable('wl_adventures')) {
            Schema::create('wl_adventures', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('button_text')->default('Start Adventure');
                $table->string('button_url')->default('#explore');
                $table->string('banner_image')->nullable();
                $table->string('badge_text')->nullable()->default('Featured Quest');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.5 Loved by Parents Testimonials
        if (!Schema::hasTable('wl_testimonials')) {
            Schema::create('wl_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('parent_name'); // Sarah M., Daniel K., Priya S.
                $table->string('child_age_label')->nullable()->default('Parent of 7-yr old');
                $table->text('quote');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('avatar')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.6 Kid & Parent Join Now Enrollments
        if (!Schema::hasTable('wl_enrollments')) {
            Schema::create('wl_enrollments', function (Blueprint $table) {
                $table->id();
                $table->string('parent_name');
                $table->string('child_name');
                $table->unsignedTinyInteger('child_age')->default(6);
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('favorite_topic')->nullable()->default('Math Magic');
                $table->string('plan_type')->default('Free Explorer');
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        // 2.7 Stay Connected Newsletter Subscribers
        if (!Schema::hasTable('wl_subscribers')) {
            Schema::create('wl_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status')->default('Subscribed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wl_subscribers');
        Schema::dropIfExists('wl_enrollments');
        Schema::dropIfExists('wl_testimonials');
        Schema::dropIfExists('wl_adventures');
        Schema::dropIfExists('wl_games');
        Schema::dropIfExists('wl_stories');
        Schema::dropIfExists('wl_topics');
        Schema::dropIfExists('wl_settings');
        Schema::dropIfExists('wl_users');
        Schema::dropIfExists('wl_themes');
        Schema::dropIfExists('wl_videos');
        Schema::dropIfExists('wl_media');
        Schema::dropIfExists('wl_sliders');
        Schema::dropIfExists('wl_menus');
        Schema::dropIfExists('wl_pages');
    }
};
