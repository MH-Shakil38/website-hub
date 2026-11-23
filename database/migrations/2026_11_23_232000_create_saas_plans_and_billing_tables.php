<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SaaS Plans & Packages (core_plans)
        if (!Schema::hasTable('core_plans')) {
            Schema::create('core_plans', function (Blueprint $table) {
                $table->id();
                $table->string('audience')->default('client')->index(); // client, reseller
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price_monthly', 12, 2)->default(0);
                $table->decimal('price_yearly', 12, 2)->default(0);
                $table->string('currency', 10)->default('BDT');
                $table->unsignedInteger('trial_days')->default(14);
                $table->unsignedInteger('site_limit')->default(1); // how many websites/tenants allowed
                $table->unsignedInteger('storage_mb')->default(500);
                $table->boolean('custom_domain')->default(true);
                $table->decimal('commission_rate', 5, 2)->default(0); // for reseller Mode B plans
                $table->decimal('extra_site_price', 12, 2)->default(0); // wholesale extra site cost from wallet
                $table->json('allowed_themes')->nullable(); // null = all themes
                $table->json('features')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Subscriptions (core_subscriptions)
        if (!Schema::hasTable('core_subscriptions')) {
            Schema::create('core_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('tenant_id')->nullable()->constrained('core_tenants')->nullOnDelete();
                $table->foreignId('plan_id')->constrained('core_plans')->cascadeOnDelete();
                $table->string('billing_cycle')->default('monthly'); // monthly, yearly
                $table->string('status')->default('trial')->index(); // trial, active, past_due, suspended, cancelled, expired
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('grace_ends_at')->nullable();
                $table->boolean('auto_renew')->default(true);
                $table->timestamp('last_reminder_sent_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Invoices (core_invoices)
        if (!Schema::hasTable('core_invoices')) {
            Schema::create('core_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('tenant_id')->nullable()->constrained('core_tenants')->nullOnDelete();
                $table->foreignId('subscription_id')->nullable()->constrained('core_subscriptions')->nullOnDelete();
                $table->foreignId('plan_id')->nullable()->constrained('core_plans')->nullOnDelete();
                $table->string('billing_cycle')->default('monthly');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->string('currency', 10)->default('BDT');
                $table->string('status')->default('unpaid')->index(); // unpaid, pending_approval, paid, cancelled, refunded
                $table->timestamp('due_date')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Payments (core_payments)
        if (!Schema::hasTable('core_payments')) {
            Schema::create('core_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('core_invoices')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('gateway')->default('manual'); // sslcommerz, manual, stripe, wallet
                $table->string('method')->nullable(); // bKash, Nagad, Bank, Card
                $table->string('sender_number')->nullable();
                $table->string('gateway_txn_id')->nullable()->index();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('currency', 10)->default('BDT');
                $table->string('status')->default('pending')->index(); // pending, completed, rejected, failed
                $table->json('gateway_payload')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_payments');
        Schema::dropIfExists('core_invoices');
        Schema::dropIfExists('core_subscriptions');
        Schema::dropIfExists('core_plans');
    }
};
