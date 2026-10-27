<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_websites', function (Blueprint $table) {
            $table->string('module_name')->nullable()->after('id'); // e.g. Interior, RealEstate, Pharmacy
            $table->string('slug')->nullable()->unique()->after('domain'); // e.g. interior-design, real-estate, pharmacy
            $table->string('admin_route')->nullable()->after('module_badge');
            $table->text('description')->nullable()->after('admin_route');
            $table->boolean('is_active')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('core_websites', function (Blueprint $table) {
            $table->dropColumn(['module_name', 'slug', 'admin_route', 'description', 'is_active']);
        });
    }
};
