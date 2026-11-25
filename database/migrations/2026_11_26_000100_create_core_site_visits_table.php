<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        // One row per public page view; feeds the dashboard "Website Traffic" chart.
        if (! Schema::connection('central')->hasTable('core_site_visits')) {
            Schema::connection('central')->create('core_site_visits', function (Blueprint $table) {
                $table->id();
                $table->string('host', 191)->nullable();
                $table->string('path', 255);
                $table->char('visitor_hash', 40); // sha1 of ip + user agent + day: no raw IP is stored
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->timestamp('visited_at')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('core_site_visits');
    }
};
