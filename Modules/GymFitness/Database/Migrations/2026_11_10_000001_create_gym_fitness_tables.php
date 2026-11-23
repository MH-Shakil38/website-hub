<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Standard Module CMS Tables (gym_*)
        // =====================================================================
        if (!Schema::hasTable('gym_pages')) {
            Schema::create('gym_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('slug')->index();
                $table->longText('content')->nullable();
                $table->longText('content_bn')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_menus')) {
            Schema::create('gym_menus', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('url');
                $table->string('location')->default('header'); // header, footer
                $table->string('target')->default('_self');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_sliders')) {
            Schema::create('gym_sliders', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('subtitle_bn')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_text_bn')->nullable();
                $table->string('button_url')->nullable();
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_media')) {
            Schema::create('gym_media', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('category')->nullable()->default('gym');
                $table->string('file_path');
                $table->string('file_type')->default('image/jpeg');
                $table->string('file_size')->default('185 KB');
                $table->string('alt_text')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_videos')) {
            Schema::create('gym_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->text('description')->nullable();
                $table->text('description_bn')->nullable();
                $table->string('category')->default('Workout');
                $table->string('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('duration')->default('04:30');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_themes')) {
            Schema::create('gym_themes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('primary_color')->default('#A3E635');
                $table->string('secondary_color')->default('#0A0E0A');
                $table->string('font_family')->default('Plus Jakarta Sans & Teko & Hind Siliguri');
                $table->string('layout_style')->default('FitPulse Dark Neon Lime Pro');
                $table->string('thumbnail')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_users')) {
            Schema::create('gym_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role_name')->default('Gym Manager');
                $table->string('status')->default('Active');
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gym_settings')) {
            Schema::create('gym_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });
        }

        // =====================================================================
        // 2. Gym & Fitness Industry CRUD Tables (EN & BN Multilingual Support)
        // =====================================================================

        // 2.1 Membership Packages (Basic Plan, Premium Plan, Elite Plan)
        if (!Schema::hasTable('gym_packages')) {
            Schema::create('gym_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Basic Plan, Premium Plan, Elite Plan
                $table->string('name_bn')->nullable();
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable(); // Perfect for beginners
                $table->string('subtitle_bn')->nullable();
                $table->text('description')->nullable();
                $table->text('description_bn')->nullable();
                $table->string('currency')->default('$');
                $table->decimal('monthly_price', 10, 2)->default(19.00);
                $table->decimal('quarterly_price', 10, 2)->default(49.00);
                $table->decimal('six_month_price', 10, 2)->default(89.00);
                $table->decimal('yearly_price', 10, 2)->default(159.00);
                $table->string('custom_duration_label')->nullable();
                $table->decimal('custom_duration_price', 10, 2)->nullable();
                $table->decimal('registration_fee', 10, 2)->default(0.00);
                $table->json('features')->nullable();
                $table->json('features_bn')->nullable();
                $table->boolean('has_personal_trainer')->default(false);
                $table->boolean('has_group_classes')->default(true);
                $table->boolean('has_locker')->default(true);
                $table->boolean('has_nutrition')->default(false);
                $table->string('badge_text')->nullable(); // Most Popular
                $table->string('badge_text_bn')->nullable();
                $table->string('button_text')->default('Get Started');
                $table->string('button_text_bn')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.2 Expert Trainers
        if (!Schema::hasTable('gym_trainers')) {
            Schema::create('gym_trainers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_bn')->nullable();
                $table->string('slug')->unique();
                $table->string('specialization'); // Strength Trainer, CrossFit Coach
                $table->string('specialization_bn')->nullable();
                $table->string('experience')->default('5+ Years');
                $table->string('experience_bn')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('photo')->nullable();
                $table->text('bio')->nullable();
                $table->text('bio_bn')->nullable();
                $table->string('availability')->default('Mon - Sat (6:00 AM - 9:00 PM)');
                $table->string('schedule_summary')->nullable();
                $table->decimal('salary', 10, 2)->default(0);
                $table->decimal('commission_rate', 5, 2)->default(10.00);
                $table->string('facebook_url')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('youtube_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.3 Workout & Diet Plans
        if (!Schema::hasTable('gym_fitness_plans')) {
            Schema::create('gym_fitness_plans', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('plan_type')->default('Workout'); // Workout, Diet
                $table->string('goal')->default('Muscle Gain'); // Muscle Gain, Fat Loss, Strength, General Fitness
                $table->string('level')->default('Intermediate'); // Beginner, Intermediate, Advanced
                $table->integer('duration_weeks')->default(8);
                $table->string('calories_target')->nullable(); // e.g. 2400 kcal/day
                $table->longText('schedule_details')->nullable();
                $table->longText('schedule_details_bn')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2.4 Gym Members (Registered Members & Member Portal Users)
        if (!Schema::hasTable('gym_members')) {
            Schema::create('gym_members', function (Blueprint $table) {
                $table->id();
                $table->string('member_code')->unique(); // e.g. FP-2026-0001
                $table->string('qr_token')->unique();
                $table->string('name');
                $table->string('email')->index();
                $table->string('phone');
                $table->string('password')->nullable();
                $table->string('photo')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('gender')->default('Male'); // Male, Female, Other
                $table->string('blood_group')->nullable();
                $table->string('address')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->unsignedBigInteger('package_id')->nullable()->index();
                $table->unsignedBigInteger('trainer_id')->nullable()->index();
                $table->unsignedBigInteger('workout_plan_id')->nullable();
                $table->unsignedBigInteger('diet_plan_id')->nullable();
                $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, six_month, yearly, custom
                $table->date('join_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->string('membership_status')->default('Active'); // Active, Expired, Suspended, Frozen, Pending
                $table->string('payment_status')->default('Paid'); // Paid, Partial, Due, Pending
                $table->decimal('due_amount', 10, 2)->default(0.00);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2.5 Membership Subscriptions & History
        if (!Schema::hasTable('gym_subscriptions')) {
            Schema::create('gym_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->string('plan_name');
                $table->string('billing_cycle')->default('monthly');
                $table->string('action_type')->default('Registration'); // Registration, Renewal, Upgrade, Downgrade, Freeze, Suspend
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('registration_fee', 10, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        // 2.6 Classes & Weekly Schedule
        if (!Schema::hasTable('gym_classes')) {
            Schema::create('gym_classes', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('category')->default('Strength'); // Yoga, Cardio, CrossFit, Weight Training, Strength, Personal Training
                $table->string('category_bn')->nullable();
                $table->unsignedBigInteger('trainer_id')->nullable()->index();
                $table->string('day_of_week')->default('Monday'); // Monday..Sunday
                $table->string('time_slot')->default('6:00 AM - 7:00 AM');
                $table->integer('duration_minutes')->default(60);
                $table->string('room_zone')->default('Main Studio');
                $table->integer('max_participants')->default(25);
                $table->integer('booked_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.7 Class Bookings
        if (!Schema::hasTable('gym_class_bookings')) {
            Schema::create('gym_class_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('member_id')->index();
                $table->date('booking_date');
                $table->string('status')->default('Booked'); // Booked, Attended, Cancelled
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2.8 Attendance Management (Check-in / Check-out / QR)
        if (!Schema::hasTable('gym_attendances')) {
            Schema::create('gym_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->date('attendance_date')->index();
                $table->string('check_in_time');
                $table->string('check_out_time')->nullable();
                $table->string('check_in_method')->default('QR Code'); // QR Code, Member ID, Admin Desk
                $table->string('status')->default('Present'); // Present, Completed
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2.9 Payments & Revenue
        if (!Schema::hasTable('gym_payments')) {
            Schema::create('gym_payments', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_no')->unique();
                $table->unsignedBigInteger('member_id')->index();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->string('payment_type')->default('Membership Fee'); // Membership Fee, Registration Fee, Renewal Payment, Personal Training, Additional Service
                $table->decimal('amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->decimal('due_amount', 10, 2)->default(0);
                $table->string('payment_method')->default('bKash / Nagad'); // bKash / Nagad, Card / PGW, Cash at Desk, Bank Transfer
                $table->string('transaction_reference')->nullable();
                $table->string('payment_status')->default('Paid'); // Paid, Pending, Due, Refunded
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2.10 Training Programs (Homepage & Public Programs Grid)
        if (!Schema::hasTable('gym_programs')) {
            Schema::create('gym_programs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_bn')->nullable();
                $table->string('slug')->unique();
                $table->string('subtitle')->nullable();
                $table->string('subtitle_bn')->nullable();
                $table->text('description')->nullable();
                $table->text('description_bn')->nullable();
                $table->string('image')->nullable();
                $table->string('icon_class')->default('fa-solid fa-dumbbell');
                $table->string('duration_label')->default('45-60 Mins');
                $table->string('calories_burn')->default('500-700 kcal');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.11 Gym Equipment & Maintenance
        if (!Schema::hasTable('gym_equipments')) {
            Schema::create('gym_equipments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_bn')->nullable();
                $table->string('category')->default('Strength'); // Cardio, Strength, Free Weights, Functional
                $table->integer('quantity')->default(4);
                $table->string('brand')->default('Hammer Strength / Technogym');
                $table->string('image')->nullable();
                $table->string('condition_status')->default('Operational'); // Operational, Under Maintenance, Repair Needed
                $table->date('last_maintenance_date')->nullable();
                $table->date('next_maintenance_date')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.12 Member Progress & Body Measurement Logs
        if (!Schema::hasTable('gym_progress_logs')) {
            Schema::create('gym_progress_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->date('recorded_date');
                $table->decimal('weight_kg', 5, 2);
                $table->decimal('height_cm', 5, 2)->nullable();
                $table->decimal('bmi', 5, 2)->nullable();
                $table->decimal('body_fat_pct', 5, 2)->nullable();
                $table->decimal('chest_cm', 5, 2)->nullable();
                $table->decimal('waist_cm', 5, 2)->nullable();
                $table->decimal('arms_cm', 5, 2)->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2.13 Member Testimonials
        if (!Schema::hasTable('gym_testimonials')) {
            Schema::create('gym_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('member_name');
                $table->string('member_name_bn')->nullable();
                $table->string('transformation_tag')->nullable(); // Lost 15kg in 5 Months
                $table->string('transformation_tag_bn')->nullable();
                $table->text('review_text');
                $table->text('review_text_bn')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('avatar')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.14 Frequently Asked Questions (EN & BN)
        if (!Schema::hasTable('gym_faqs')) {
            Schema::create('gym_faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->string('question_bn')->nullable();
                $table->text('answer');
                $table->text('answer_bn')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2.15 Newsletter Subscribers
        if (!Schema::hasTable('gym_subscribers')) {
            Schema::create('gym_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status')->default('Subscribed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_subscribers');
        Schema::dropIfExists('gym_faqs');
        Schema::dropIfExists('gym_testimonials');
        Schema::dropIfExists('gym_progress_logs');
        Schema::dropIfExists('gym_equipments');
        Schema::dropIfExists('gym_programs');
        Schema::dropIfExists('gym_payments');
        Schema::dropIfExists('gym_attendances');
        Schema::dropIfExists('gym_class_bookings');
        Schema::dropIfExists('gym_classes');
        Schema::dropIfExists('gym_subscriptions');
        Schema::dropIfExists('gym_members');
        Schema::dropIfExists('gym_fitness_plans');
        Schema::dropIfExists('gym_trainers');
        Schema::dropIfExists('gym_packages');
        Schema::dropIfExists('gym_settings');
        Schema::dropIfExists('gym_users');
        Schema::dropIfExists('gym_themes');
        Schema::dropIfExists('gym_videos');
        Schema::dropIfExists('gym_media');
        Schema::dropIfExists('gym_sliders');
        Schema::dropIfExists('gym_menus');
        Schema::dropIfExists('gym_pages');
    }
};
