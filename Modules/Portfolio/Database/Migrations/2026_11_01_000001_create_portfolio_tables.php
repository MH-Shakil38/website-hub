<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('port_pages')) {
            Schema::create('port_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_menus')) {
            Schema::create('port_menus', function (Blueprint $table) {
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

        if (!Schema::hasTable('port_sliders')) {
            Schema::create('port_sliders', function (Blueprint $table) {
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

        if (!Schema::hasTable('port_media')) {
            Schema::create('port_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->nullable()->default('portfolio');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('140 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_videos')) {
            Schema::create('port_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->default('Showcase');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('03:15');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_themes')) {
            Schema::create('port_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#078BFF');
                $table->string('secondary_color')->default('#020817');
                $table->string('font_family')->default('Playfair Display & Plus Jakarta Sans');
                $table->string('layout_style')->default('Dark & Light Wave Signature');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_users')) {
            Schema::create('port_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Administrator');
                $table->string('status')->default('Active');
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_settings')) {
            Schema::create('port_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }

        // Dedicated Portfolio CRUD Tables
        if (!Schema::hasTable('port_projects')) {
            Schema::create('port_projects', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category')->default('Corporate Website');
                $table->string('subtitle')->nullable();
                $table->string('thumbnail')->nullable();
                $table->json('gallery_images')->nullable();
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->json('technologies')->nullable();
                $table->string('client_name')->nullable();
                $table->string('live_url')->nullable();
                $table->string('github_url')->nullable();
                $table->string('completion_date')->nullable();
                $table->boolean('is_featured')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_skills')) {
            Schema::create('port_skills', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->string('category')->default('Backend'); // Backend, Frontend, Database, Tools, Design
                $table->string('icon_class')->nullable();
                $table->string('icon_color')->default('#078BFF');
                $table->string('badge_text')->nullable();
                $table->unsignedTinyInteger('level_percent')->default(95);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_services')) {
            Schema::create('port_services', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable();
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->string('icon_class')->default('fa-solid fa-code');
                $table->json('features')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_experiences')) {
            Schema::create('port_experiences', function (Blueprint $table) {
                $table->id();
                $table->string('period');
                $table->string('job_title');
                $table->string('company_name')->nullable();
                $table->string('employment_type')->default('Full-Time');
                $table->text('description')->nullable();
                $table->string('company_logo')->nullable();
                $table->string('icon_class')->default('fa-solid fa-briefcase');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_educations')) {
            Schema::create('port_educations', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('Education'); // Education | Certification
                $table->string('title');
                $table->string('institution');
                $table->string('completion_date')->nullable();
                $table->text('description')->nullable();
                $table->string('certificate_url')->nullable();
                $table->string('certificate_image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_testimonials')) {
            Schema::create('port_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('client_name');
                $table->string('designation')->nullable();
                $table->string('company')->nullable();
                $table->text('review_text');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('avatar')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_blogs')) {
            Schema::create('port_blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category')->default('Laravel');
                $table->string('published_date')->nullable();
                $table->text('short_description')->nullable();
                $table->longText('content')->nullable();
                $table->string('image')->nullable();
                $table->unsignedInteger('views')->default(120);
                $table->boolean('is_featured')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_faqs')) {
            Schema::create('port_faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('General');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_messages')) {
            Schema::create('port_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('status')->default('Unread'); // Unread, Read, Replied
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('port_subscribers')) {
            Schema::create('port_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status')->default('Subscribed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('port_subscribers');
        Schema::dropIfExists('port_messages');
        Schema::dropIfExists('port_faqs');
        Schema::dropIfExists('port_blogs');
        Schema::dropIfExists('port_testimonials');
        Schema::dropIfExists('port_educations');
        Schema::dropIfExists('port_experiences');
        Schema::dropIfExists('port_services');
        Schema::dropIfExists('port_skills');
        Schema::dropIfExists('port_projects');
        Schema::dropIfExists('port_settings');
        Schema::dropIfExists('port_users');
        Schema::dropIfExists('port_themes');
        Schema::dropIfExists('port_videos');
        Schema::dropIfExists('port_media');
        Schema::dropIfExists('port_sliders');
        Schema::dropIfExists('port_menus');
        Schema::dropIfExists('port_pages');
    }
};
