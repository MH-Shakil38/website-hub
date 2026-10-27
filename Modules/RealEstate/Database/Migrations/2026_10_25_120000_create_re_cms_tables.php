<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('re_pages')) {
            Schema::create('re_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('re_menus')) {
            Schema::create('re_menus', function (Blueprint $table) {
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

        if (!Schema::hasTable('re_sliders')) {
            Schema::create('re_sliders', function (Blueprint $table) {
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

        if (!Schema::hasTable('re_media')) {
            Schema::create('re_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('120 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('re_themes')) {
            Schema::create('re_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#2563EB');
                $table->string('secondary_color')->default('#0F172A');
                $table->string('font_family')->default('Inter');
                $table->string('layout_style')->default('Estate Grid');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('re_users')) {
            Schema::create('re_users', function (Blueprint $table) {
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

        if (!Schema::hasTable('re_settings')) {
            Schema::create('re_settings', function (Blueprint $table) {
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
        Schema::dropIfExists('re_settings');
        Schema::dropIfExists('re_users');
        Schema::dropIfExists('re_themes');
        Schema::dropIfExists('re_media');
        Schema::dropIfExists('re_sliders');
        Schema::dropIfExists('re_menus');
        Schema::dropIfExists('re_pages');
    }
};
