<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SaaS Landing Page Sections CMS (core_saas_sections)
        if (!Schema::hasTable('core_saas_sections')) {
            Schema::create('core_saas_sections', function (Blueprint $table) {
                $table->id();
                $table->string('section_key')->unique(); // hero, themes, features, pricing, faq, cta_footer, seo
                $table->string('title')->nullable();
                $table->json('content')->nullable();
                $table->boolean('is_visible')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Ensure core_tenants has verification_token for Step-by-Step Theme Installation Email Verification
        Schema::table('core_tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('core_tenants', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->after('status')->index();
            }
            if (!Schema::hasColumn('core_tenants', 'billing_cycle')) {
                $table->string('billing_cycle', 20)->default('monthly')->after('plan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('core_tenants', function (Blueprint $table) {
            $cols = array_filter(['verification_token', 'billing_cycle'], fn ($c) => Schema::hasColumn('core_tenants', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
        Schema::dropIfExists('core_saas_sections');
    }
};
