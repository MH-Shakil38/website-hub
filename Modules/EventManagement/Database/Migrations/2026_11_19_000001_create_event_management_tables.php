<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Standard Module CMS Tables (em_*)
        // =====================================================================
        if (!Schema::hasTable('em_pages')) {
            Schema::create('em_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->longText('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('em_menus')) {
            Schema::create('em_menus', function (Blueprint $table) {
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

        if (!Schema::hasTable('em_sliders')) {
            Schema::create('em_sliders', function (Blueprint $table) {
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

        if (!Schema::hasTable('em_media')) {
            Schema::create('em_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->nullable()->default('eventify');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('185 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('em_videos')) {
            Schema::create('em_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->default('Keynote');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('04:15');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('em_themes')) {
            Schema::create('em_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#B9FF38');
                $table->string('secondary_color')->default('#0A2229');
                $table->string('font_family')->default('Plus Jakarta Sans & Inter');
                $table->string('layout_style')->default('Eventify Dark Teal & Neon Lime Conference Theme');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('em_users')) {
            Schema::create('em_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Conference Director');
                $table->string('status')->default('Active');
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('em_settings')) {
            Schema::create('em_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }

        // =====================================================================
        // 2. Eventify Domain CRUD Tables (em_*)
        // =====================================================================

        // 2.1 Our Event Speakers (Alex Robertson, Alexy Sammo, Andrew Symonds, Ben Stokes)
        if (!Schema::hasTable('em_speakers')) {
            Schema::create('em_speakers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('designation')->default('Finance Consultant');
                $table->string('company')->nullable();
                $table->string('avatar')->nullable();
                $table->text('bio')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->string('twitter_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.2 Our Events Schedule Plan (Day One, Day Two, Day Three sessions 01, 02, 03)
        if (!Schema::hasTable('em_schedules')) {
            Schema::create('em_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('day_key')->default('day1'); // day1, day2, day3
                $table->string('day_label')->default('Day One');
                $table->string('day_date')->default('May 25, 2024');
                $table->string('session_number')->default('01'); // 01, 02, 03
                $table->string('title');
                $table->string('time_range')->default('10:00 AM - 12:00 PM');
                $table->string('venue_location')->default('26/C Asana, New York');
                $table->string('image')->nullable();
                $table->string('speaker1_name')->default('Alex Robertson');
                $table->string('speaker1_role')->default('UI/UX Designer');
                $table->string('speaker1_avatar')->nullable();
                $table->string('speaker2_name')->default('Alexys Archer');
                $table->string('speaker2_role')->default('WP Developer');
                $table->string('speaker2_avatar')->nullable();
                $table->string('button_text')->default('PURCHASE TICKET NOW');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.3 Get Your Entry Ticket Pricing Plans (Regular $49, Premium $69, Platinum $99)
        if (!Schema::hasTable('em_pricing_plans')) {
            Schema::create('em_pricing_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // REGULAR, PREMIUM, PLATINUM
                $table->string('slug')->unique();
                $table->decimal('price', 10, 2)->default(49.00);
                $table->string('currency')->default('$');
                $table->string('subtitle')->default('ATTENDEE');
                $table->text('features_text')->nullable();
                $table->string('button_text')->default('BUY TICKET');
                $table->boolean('is_highlighted')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.4 Sponsors & Companies (Nietzsche, Epicurious, Boltshift, Lightbox)
        if (!Schema::hasTable('em_sponsors')) {
            Schema::create('em_sponsors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon_class')->nullable()->default('bi-brightness-high-fill');
                $table->string('logo')->nullable();
                $table->string('website_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.5 Recent Memories 2024 & Footer Gallery
        if (!Schema::hasTable('em_memories')) {
            Schema::create('em_memories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('year_label')->default('2024');
                $table->string('image');
                $table->boolean('is_footer_gallery')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.6 Ticket Bookings / Attendee Seat Reservations
        if (!Schema::hasTable('em_ticket_bookings')) {
            Schema::create('em_ticket_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_code')->unique();
                $table->string('attendee_name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('company')->nullable();
                $table->string('plan_name')->default('PREMIUM');
                $table->decimal('unit_price', 10, 2)->default(69.00);
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('total_amount', 10, 2)->default(69.00);
                $table->string('session_choice')->nullable();
                $table->string('status')->default('Confirmed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('em_ticket_bookings');
        Schema::dropIfExists('em_memories');
        Schema::dropIfExists('em_sponsors');
        Schema::dropIfExists('em_pricing_plans');
        Schema::dropIfExists('em_schedules');
        Schema::dropIfExists('em_speakers');
        Schema::dropIfExists('em_settings');
        Schema::dropIfExists('em_users');
        Schema::dropIfExists('em_themes');
        Schema::dropIfExists('em_videos');
        Schema::dropIfExists('em_media');
        Schema::dropIfExists('em_sliders');
        Schema::dropIfExists('em_menus');
        Schema::dropIfExists('em_pages');
    }
};
