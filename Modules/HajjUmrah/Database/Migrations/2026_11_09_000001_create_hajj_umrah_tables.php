<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Standard Module CMS Tables (hajj_*)
        if (!Schema::hasTable('hajj_pages')) {
            Schema::create('hajj_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('slug')->index();
                $table->longText('content')->nullable();
                $table->longText('content_bn')->nullable();
                $table->longText('content_ar')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_menus')) {
            Schema::create('hajj_menus', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('url');
                $table->string('location')->default('header'); // header, footer, offers
                $table->string('parent_key')->nullable(); // null, hajj, umrah
                $table->string('target')->default('_self');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_sliders')) {
            Schema::create('hajj_sliders', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('subtitle_bn')->nullable();
                $table->string('subtitle_ar')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_text_bn')->nullable();
                $table->string('button_text_ar')->nullable();
                $table->string('button_url')->nullable();
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_media')) {
            Schema::create('hajj_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('category')->nullable()->default('makkah');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('180 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_videos')) {
            Schema::create('hajj_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->text('description')->nullable();
                $table->text('description_bn')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('category')->default('Umrah Guide');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('12:40');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_themes')) {
            Schema::create('hajj_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#15803D');
                $table->string('secondary_color')->default('#0A1410');
                $table->string('font_family')->default('Plus Jakarta Sans & Cairo & Hind Siliguri');
                $table->string('layout_style')->default('Al Hajenra Kaaba Royal Gold & Emerald');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_users')) {
            Schema::create('hajj_users', function (Blueprint $table) {
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

        if (!Schema::hasTable('hajj_settings')) {
            Schema::create('hajj_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }

        // 2. Dedicated Hajj & Umrah Industry CRUD Tables (With EN / BN / AR Multilingual Columns)
        if (!Schema::hasTable('hajj_packages')) {
            Schema::create('hajj_packages', function (Blueprint $table) {
                $table->id();
                $table->string('package_type')->default('hajj'); // 'hajj' or 'umrah'
                $table->string('category_label')->default('Category');
                $table->string('category_label_bn')->nullable();
                $table->string('category_label_ar')->nullable();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('slug')->unique();
                $table->string('price')->default('$15,000');
                $table->string('price_bn')->nullable();
                $table->string('price_ar')->nullable();
                $table->string('price_unit')->default('/Person');
                $table->string('price_unit_bn')->nullable();
                $table->string('price_unit_ar')->nullable();
                $table->string('duration')->default('(15-25 Days)');
                $table->string('duration_bn')->nullable();
                $table->string('duration_ar')->nullable();
                $table->string('thumbnail')->nullable();
                $table->json('gallery_images')->nullable();
                $table->text('short_description')->nullable();
                $table->text('short_description_bn')->nullable();
                $table->text('short_description_ar')->nullable();
                $table->longText('description')->nullable();
                $table->longText('description_bn')->nullable();
                $table->longText('description_ar')->nullable();
                $table->string('airline')->nullable();
                $table->string('makkah_hotel')->nullable();
                $table->string('madinah_hotel')->nullable();
                $table->json('features')->nullable();
                $table->json('features_bn')->nullable();
                $table->json('features_ar')->nullable();
                $table->boolean('is_featured')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_pricing_plans')) {
            Schema::create('hajj_pricing_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Economy Package, Standard Package, Classic Package
                $table->string('name_bn')->nullable();
                $table->string('name_ar')->nullable();
                $table->string('slug')->unique();
                $table->text('subtitle')->nullable();
                $table->text('subtitle_bn')->nullable();
                $table->text('subtitle_ar')->nullable();
                $table->string('currency')->default('BDT');
                $table->string('currency_bn')->nullable();
                $table->string('currency_ar')->nullable();
                $table->string('price')->default('6,35,000');
                $table->string('price_bn')->nullable();
                $table->string('price_ar')->nullable();
                $table->string('price_suffix')->default('STARTS FROM');
                $table->string('price_suffix_bn')->nullable();
                $table->string('price_suffix_ar')->nullable();
                $table->string('icon_class')->default('fa-solid fa-hand-holding-dollar');
                $table->string('duration')->default('40/42 Days');
                $table->string('duration_bn')->nullable();
                $table->string('duration_ar')->nullable();
                $table->string('direct_flight')->default('SA/Biman/Flynas');
                $table->string('direct_flight_bn')->nullable();
                $table->string('direct_flight_ar')->nullable();
                $table->string('hotel_aziziyah')->default('Near to Jamarah');
                $table->string('hotel_aziziyah_bn')->nullable();
                $table->string('hotel_aziziyah_ar')->nullable();
                $table->string('hotel_makkah')->default('600/700M');
                $table->string('hotel_makkah_bn')->nullable();
                $table->string('hotel_makkah_ar')->nullable();
                $table->string('hotel_madinah')->default('500/600M');
                $table->string('hotel_madinah_bn')->nullable();
                $table->string('hotel_madinah_ar')->nullable();
                $table->string('food')->default('Breakfast, Lunch & Dinner');
                $table->string('food_bn')->nullable();
                $table->string('food_ar')->nullable();
                $table->string('special_services')->default('Ziyarah Tour, Transportation & Guide');
                $table->string('special_services_bn')->nullable();
                $table->string('special_services_ar')->nullable();
                $table->string('button_text')->default('SELECT PACKAGE');
                $table->string('button_text_bn')->nullable();
                $table->string('button_text_ar')->nullable();
                $table->string('terms_text')->default('*Terms & Conditions Applicable');
                $table->string('terms_text_bn')->nullable();
                $table->string('terms_text_ar')->nullable();
                $table->boolean('is_featured')->default(false); // true = dark emerald green card (Standard Package)
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_services')) {
            Schema::create('hajj_services', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable();
                $table->string('subtitle_bn')->nullable();
                $table->string('subtitle_ar')->nullable();
                $table->text('description')->nullable();
                $table->text('description_bn')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('icon_class')->default('fa-solid fa-plane-up');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_partners')) {
            Schema::create('hajj_partners', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('airline'); // airline, hotel, review_badge
                $table->string('logo')->nullable();
                $table->text('svg_markup')->nullable();
                $table->string('rating')->nullable(); // For top trust badges (e.g. 4.5/5)
                $table->string('reviews_text')->nullable(); // e.g. 1597 reviews
                $table->string('reviews_text_bn')->nullable();
                $table->string('reviews_text_ar')->nullable();
                $table->string('website_url')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_blogs')) {
            Schema::create('hajj_blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('title_ar')->nullable();
                $table->string('slug')->unique();
                $table->string('category')->default('Hajj Guide');
                $table->string('category_bn')->nullable();
                $table->string('category_ar')->nullable();
                $table->string('published_date')->nullable();
                $table->text('short_description')->nullable();
                $table->text('short_description_bn')->nullable();
                $table->text('short_description_ar')->nullable();
                $table->longText('content')->nullable();
                $table->longText('content_bn')->nullable();
                $table->longText('content_ar')->nullable();
                $table->string('image')->nullable();
                $table->unsignedInteger('views')->default(150);
                $table->boolean('is_featured')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_testimonials')) {
            Schema::create('hajj_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('client_name');
                $table->string('client_name_bn')->nullable();
                $table->string('client_name_ar')->nullable();
                $table->string('designation')->nullable();
                $table->string('designation_bn')->nullable();
                $table->string('designation_ar')->nullable();
                $table->text('review_text');
                $table->text('review_text_bn')->nullable();
                $table->text('review_text_ar')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('avatar')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_inquiries')) {
            Schema::create('hajj_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('package_name')->nullable();
                $table->string('travelers_count')->nullable()->default('1 Person');
                $table->string('preferred_date')->nullable();
                $table->text('message')->nullable();
                $table->string('status')->default('Unread'); // Unread, Read, Confirmed
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hajj_subscribers')) {
            Schema::create('hajj_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status')->default('Subscribed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hajj_subscribers');
        Schema::dropIfExists('hajj_inquiries');
        Schema::dropIfExists('hajj_testimonials');
        Schema::dropIfExists('hajj_blogs');
        Schema::dropIfExists('hajj_partners');
        Schema::dropIfExists('hajj_services');
        Schema::dropIfExists('hajj_pricing_plans');
        Schema::dropIfExists('hajj_packages');
        Schema::dropIfExists('hajj_settings');
        Schema::dropIfExists('hajj_users');
        Schema::dropIfExists('hajj_themes');
        Schema::dropIfExists('hajj_videos');
        Schema::dropIfExists('hajj_media');
        Schema::dropIfExists('hajj_sliders');
        Schema::dropIfExists('hajj_menus');
        Schema::dropIfExists('hajj_pages');
    }
};
