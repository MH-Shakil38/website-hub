<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('int_pages')) {
            Schema::create('int_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('content')->nullable();
                $table->string('status')->default('Published'); // Published, Draft, Private, Scheduled
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('int_menus')) {
            Schema::create('int_menus', function (Blueprint $table) {
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

        if (!Schema::hasTable('int_sliders')) {
            Schema::create('int_sliders', function (Blueprint $table) {
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

        if (!Schema::hasTable('int_media')) {
            Schema::create('int_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('120 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('int_themes')) {
            Schema::create('int_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#EC134A');
                $table->string('secondary_color')->default('#0F172A');
                $table->string('font_family')->default('Inter');
                $table->string('layout_style')->default('Modern Luxury');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('int_users')) {
            Schema::create('int_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Editor'); // Administrator, Manager, Editor, Support
                $table->string('status')->default('Active'); // Active, Inactive
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('int_settings')) {
            Schema::create('int_settings', function (Blueprint $table) {
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
        Schema::dropIfExists('int_settings');
        Schema::dropIfExists('int_users');
        Schema::dropIfExists('int_themes');
        Schema::dropIfExists('int_media');
        Schema::dropIfExists('int_sliders');
        Schema::dropIfExists('int_menus');
        Schema::dropIfExists('int_pages');
    }
};
