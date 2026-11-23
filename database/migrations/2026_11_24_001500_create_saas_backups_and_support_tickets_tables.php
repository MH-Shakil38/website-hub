<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Per-Tenant & Central Database Backups Registry (core_tenant_backups)
        if (!Schema::hasTable('core_tenant_backups')) {
            Schema::create('core_tenant_backups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('core_tenants')->cascadeOnDelete();
                $table->string('db_name');
                $table->string('file_path');
                $table->unsignedBigInteger('size_bytes')->default(0);
                $table->string('status')->default('completed'); // completed, failed
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. SaaS Support Tickets (core_support_tickets)
        if (!Schema::hasTable('core_support_tickets')) {
            Schema::create('core_support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('tenant_id')->nullable()->constrained('core_tenants')->nullOnDelete();
                $table->string('subject');
                $table->string('category')->default('technical'); // technical, billing, domain, reseller
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->string('status')->default('open')->index(); // open, answered, closed
                $table->timestamps();
            });
        }

        // 3. Support Ticket Replies (core_support_replies)
        if (!Schema::hasTable('core_support_replies')) {
            Schema::create('core_support_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('core_support_tickets')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->boolean('is_staff_reply')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_support_replies');
        Schema::dropIfExists('core_support_tickets');
        Schema::dropIfExists('core_tenant_backups');
    }
};
