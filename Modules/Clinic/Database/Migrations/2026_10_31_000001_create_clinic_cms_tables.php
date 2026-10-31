<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clinic_pages')) {
            Schema::create('clinic_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_menus')) {
            Schema::create('clinic_menus', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('url');
                $table->string('location')->default('header');
                $table->string('target')->default('_self');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_sliders')) {
            Schema::create('clinic_sliders', function (Blueprint $table) {
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

        if (!Schema::hasTable('clinic_media')) {
            Schema::create('clinic_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('120 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_videos')) {
            Schema::create('clinic_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->default('Clinic');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('2:30');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_themes')) {
            Schema::create('clinic_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#14B89A');
                $table->string('secondary_color')->default('#0C3B4E');
                $table->string('font_family')->default('Plus Jakarta Sans');
                $table->string('layout_style')->default('Fertility Care');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_users')) {
            Schema::create('clinic_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Editor');
                $table->string('status')->default('Active');
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('clinic_settings')) {
            Schema::create('clinic_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
        Schema::dropIfExists('clinic_users');
        Schema::dropIfExists('clinic_themes');
        Schema::dropIfExists('clinic_videos');
        Schema::dropIfExists('clinic_media');
        Schema::dropIfExists('clinic_sliders');
        Schema::dropIfExists('clinic_menus');
        Schema::dropIfExists('clinic_pages');
    }
};
