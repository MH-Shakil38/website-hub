<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MODIFY is MySQL/MariaDB syntax; SQLite text columns are already unbounded.
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $tables = ['settings', 'int_settings', 're_settings', 'pharma_settings'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `value` LONGTEXT NULL");
            }
        }
    }

    public function down(): void
    {
        // Keep LONGTEXT for safety
    }
};
