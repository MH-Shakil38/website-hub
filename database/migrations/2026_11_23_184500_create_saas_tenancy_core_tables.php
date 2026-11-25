<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Themes Registry (core_themes)
        if (!Schema::hasTable('core_themes')) {
            Schema::create('core_themes', function (Blueprint $table) {
                $table->id();
                $table->string('module_name')->unique(); // Interior, RealEstate, Pharmacy, etc.
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('category')->default('business');
                $table->string('badge_color')->default('purple');
                $table->string('thumbnail')->nullable();
                $table->string('demo_url')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(true);
                $table->boolean('is_active')->default(true);
                $table->json('allowed_plans')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Tenants Registry (core_tenants = each client website instance with its own DB)
        if (!Schema::hasTable('core_tenants')) {
            Schema::create('core_tenants', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('reseller_id')->nullable()->index();
                $table->unsignedBigInteger('plan_id')->nullable()->index();
                $table->string('theme_module'); // e.g., Interior
                $table->string('name');
                $table->string('subdomain')->unique(); // e.g., interior-demo or mysite
                $table->string('status')->default('pending_verification')->index();
                // Statuses: pending_verification, provisioning, trial, active, suspended, expired, failed
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->unsignedInteger('storage_used_mb')->default(0);
                $table->unsignedInteger('storage_limit_mb')->default(500);

                // Dedicated Tenant Database Credentials
                $table->string('db_name')->unique()->nullable();
                $table->string('db_host')->default('127.0.0.1');
                $table->string('db_port')->default('3306');
                $table->string('db_username')->nullable();
                $table->text('db_password')->nullable(); // Encrypted cast in model

                $table->boolean('is_demo')->default(false)->index();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Tenant Domains (core_domains)
        if (!Schema::hasTable('core_domains')) {
            Schema::create('core_domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
                $table->string('domain')->unique(); // e.g., mysite.yourhub.com or clientdomain.com
                $table->string('type')->default('subdomain'); // subdomain, custom
                $table->boolean('is_primary')->default(true);
                $table->string('verification_token')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->string('ssl_status')->default('pending'); // pending, active, failed
                $table->timestamps();
            });
        }

        // 4. Tenant Migrations Log (core_tenant_migrations_log)
        if (!Schema::hasTable('core_tenant_migrations_log')) {
            Schema::create('core_tenant_migrations_log', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
                $table->string('module');
                $table->string('migration')->nullable();
                $table->string('status')->default('success'); // success, failed
                $table->text('error')->nullable();
                $table->timestamp('ran_at')->useCurrent();
                $table->timestamps();
            });
        }

        // 5. Upgrade users table with SaaS role type & reseller referral tracking
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'type')) {
                $table->string('type')->default('client')->after('role_id')->index(); // super_admin, reseller, client, staff
            }
            if (!Schema::hasColumn('users', 'reseller_id')) {
                $table->unsignedBigInteger('reseller_id')->nullable()->after('type')->index();
            }
            if (!Schema::hasColumn('users', 'referred_via')) {
                $table->string('referred_via')->nullable()->after('reseller_id'); // manual, referral_link
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = array_filter(['type', 'reseller_id', 'referred_via'], fn ($c) => Schema::hasColumn('users', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
        Schema::dropIfExists('core_tenant_migrations_log');
        Schema::dropIfExists('core_domains');
        Schema::dropIfExists('core_tenants');
        Schema::dropIfExists('core_themes');
    }
};
