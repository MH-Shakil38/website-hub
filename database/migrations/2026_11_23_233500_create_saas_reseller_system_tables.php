<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Reseller Partner Profiles (core_resellers)
        if (!Schema::hasTable('core_resellers')) {
            Schema::create('core_resellers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->foreignId('plan_id')->nullable()->constrained('core_plans')->nullOnDelete();
                $table->string('company_name');
                $table->string('phone')->nullable();
                $table->string('kyc_document_type')->default('NID'); // NID, Trade License, Passport
                $table->string('kyc_document_number')->nullable();
                $table->string('kyc_status')->default('approved')->index(); // pending, approved, rejected
                $table->string('referral_code')->unique(); // e.g., RES8A9F
                $table->unsignedInteger('referral_clicks')->default(0);
                $table->string('billing_mode')->default('reseller_collects'); // reseller_collects (Mode A), commission (Mode B)
                $table->decimal('commission_rate', 5, 2)->default(15.00);
                $table->unsignedInteger('site_quota')->default(10);
                $table->unsignedInteger('sites_used')->default(0);
                $table->decimal('wallet_balance', 12, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 2. Reseller Wallet Transactions (core_wallet_transactions)
        if (!Schema::hasTable('core_wallet_transactions')) {
            Schema::create('core_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reseller_id')->constrained('core_resellers')->cascadeOnDelete();
                $table->string('type')->default('credit'); // credit, debit
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->decimal('balance_after', 12, 2)->default(0.00);
                $table->string('reference')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Reseller Commissions from Referred Clients (core_commissions - Mode B)
        if (!Schema::hasTable('core_commissions')) {
            Schema::create('core_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reseller_id')->constrained('core_resellers')->cascadeOnDelete();
                $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained('core_invoices')->cascadeOnDelete();
                $table->decimal('invoice_amount', 12, 2)->default(0.00);
                $table->decimal('rate', 5, 2)->default(15.00);
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->string('status')->default('pending')->index(); // pending, approved, cancelled
                $table->timestamp('available_at')->nullable(); // 7-14 days maturation
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Reseller Wallet Payout Requests (core_payout_requests)
        if (!Schema::hasTable('core_payout_requests')) {
            Schema::create('core_payout_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reseller_id')->constrained('core_resellers')->cascadeOnDelete();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->string('method')->default('bKash'); // bKash, Nagad, Rocket, Bank Transfer
                $table->string('account_info');
                $table->string('status')->default('pending')->index(); // pending, paid, rejected
                $table->string('transaction_reference')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('processed_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_payout_requests');
        Schema::dropIfExists('core_commissions');
        Schema::dropIfExists('core_wallet_transactions');
        Schema::dropIfExists('core_resellers');
    }
};
