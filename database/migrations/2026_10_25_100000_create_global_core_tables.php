<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 2. Permissions Table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // 3. Role-Permission Pivot Table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        // 4. Add Role, Profile & 2FA fields to Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('status')->default('Active')->after('avatar');
            $table->boolean('two_factor_enabled')->default(false)->after('password');
            $table->string('two_factor_code', 10)->nullable()->after('two_factor_enabled');
        });

        // 5. Global Websites Table (core_websites)
        Schema::create('core_websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain');
            $table->string('thumbnail')->nullable();
            $table->string('module'); // Real Estate, Interior Design, Booking, Software
            $table->string('module_badge')->default('pink'); // pink, purple, blue, green
            $table->string('status')->default('Published'); // Published, Draft, Private
            $table->timestamps();
        });

        // 6. Global Pages Table (core_pages)
        Schema::create('core_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->nullable()->constrained('core_websites')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('status')->default('Published'); // Published, Draft, Private, Scheduled
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        // 7. Global Activity Logs Table (core_activity_logs)
        Schema::create('core_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('subtitle');
            $table->string('icon')->default('bi-plus-circle-fill');
            $table->string('bg_color')->default('#DCFCE7');
            $table->string('text_color')->default('#10B981');
            $table->string('time_label')->nullable();
            $table->timestamps();
        });

        // 8. Global Settings Table (core_settings)
        Schema::create('core_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // 9. Global Transactions Table (core_transactions)
        Schema::create('core_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('transacted_at');
            $table->string('wallet_number');
            $table->decimal('amount', 12, 2);
            $table->decimal('coupon_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            $table->decimal('cashback_amount', 12, 2)->default(0);
            $table->string('type')->default('Payment'); // Payment, Settlement
            $table->string('channel')->default('PGW');
            $table->string('transaction_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_transactions');
        Schema::dropIfExists('core_settings');
        Schema::dropIfExists('core_activity_logs');
        Schema::dropIfExists('core_pages');
        Schema::dropIfExists('core_websites');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['phone', 'avatar', 'status', 'two_factor_enabled', 'two_factor_code']);
        });
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
