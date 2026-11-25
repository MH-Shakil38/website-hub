<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ivf_consultations')) {
            Schema::create('ivf_consultations', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('service_interest')->nullable();
                $table->date('preferred_date')->nullable();
                $table->text('message')->nullable();
                $table->string('status')->default('Pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ivf_pages')) {
            Schema::create('ivf_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('content')->nullable();
                $table->string('status')->default('Published');
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ivf_menus')) {
            Schema::create('ivf_menus', function (Blueprint $table) {
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

        if (!Schema::hasTable('ivf_settings')) {
            Schema::create('ivf_settings', function (Blueprint $table) {
                $table->id();
                $table->string('group')->default('general');
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ivf_consultations');
        Schema::dropIfExists('ivf_pages');
        Schema::dropIfExists('ivf_menus');
        Schema::dropIfExists('ivf_settings');
    }
};
