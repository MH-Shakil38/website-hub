<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Tenant;
use App\Models\TenantBackup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TenantBackupService
{
    public function __construct(
        protected TenantDatabaseManager $tenantDb
    ) {}

    /**
     * Create a full JSON/schema snapshot backup of a specific Tenant's isolated MySQL database.
     */
    public function backupTenant(Tenant $tenant, ?User $actor = null, ?string $notes = null): TenantBackup
    {
        $dbName = $tenant->db_name;
        if (!$dbName) {
            throw new \RuntimeException("Tenant #{$tenant->id} does not have a provisioned database.");
        }

        $this->tenantDb->connect($tenant);

        $tablesRows = DB::connection('tenant')->select("SHOW TABLES FROM `{$dbName}`");
        $snapshot = [
            'tenant_id' => $tenant->id,
            'tenant_uuid' => $tenant->uuid,
            'tenant_name' => $tenant->name,
            'theme_module' => $tenant->theme_module,
            'db_name' => $dbName,
            'created_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        foreach ($tablesRows as $tRow) {
            $tableName = (string) current((array) $tRow);
            if ($tableName === 'migrations') {
                continue;
            }

            $rows = DB::connection('tenant')
                ->table($tableName)
                ->get()
                ->map(fn ($r) => (array) $r)
                ->all();

            $snapshot['tables'][$tableName] = $rows;
        }

        $backupDir = storage_path('app/backups/tenants');
        File::ensureDirectoryExists($backupDir);

        $filename = $dbName . '_' . date('Ymd_His') . '_' . substr(uniqid(), -4) . '.json';
        $relativePath = 'backups/tenants/' . $filename;
        $fullPath = storage_path('app/' . $relativePath);

        $jsonPayload = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        File::put($fullPath, $jsonPayload);

        $backup = TenantBackup::create([
            'tenant_id' => $tenant->id,
            'db_name' => $dbName,
            'file_path' => $relativePath,
            'size_bytes' => strlen((string) $jsonPayload),
            'status' => 'completed',
            'created_by' => $actor?->id,
            'notes' => $notes ?: "Automated snapshot of {$dbName} (" . count($snapshot['tables']) . " tables)",
        ]);

        ActivityLog::record(
            'Tenant Database Backup Created',
            "{$tenant->name} ({$dbName}) • " . number_format($backup->size_bytes / 1024, 1) . ' KB',
            'bi-cloud-arrow-down-fill',
            '#DCFCE7',
            '#10B981'
        );

        return $backup;
    }

    /**
     * Restore a Tenant's isolated MySQL database from a saved TenantBackup snapshot in one click.
     */
    public function restoreBackup(TenantBackup $backup): int
    {
        $tenant = $backup->tenant;
        if (!$tenant) {
            throw new \RuntimeException('Target tenant for this backup no longer exists.');
        }

        $fullPath = storage_path('app/' . $backup->file_path);
        if (!File::exists($fullPath)) {
            throw new \RuntimeException("Backup file [{$backup->file_path}] not found on disk.");
        }

        $snapshot = json_decode(File::get($fullPath), true);
        if (!is_array($snapshot) || empty($snapshot['tables'])) {
            throw new \RuntimeException('Invalid or corrupted backup file payload.');
        }

        $this->tenantDb->connect($tenant);
        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0');

        $restoredTables = 0;
        try {
            foreach ($snapshot['tables'] as $tableName => $rows) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                    continue;
                }

                if (!\Illuminate\Support\Facades\Schema::connection('tenant')->hasTable($tableName)) {
                    continue;
                }

                DB::connection('tenant')->table($tableName)->truncate();

                if (!empty($rows) && is_array($rows)) {
                    foreach (array_chunk($rows, 200) as $chunk) {
                        DB::connection('tenant')->table($tableName)->insert($chunk);
                    }
                }
                $restoredTables++;
            }
        } finally {
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        ActivityLog::record(
            'Tenant Database Restored from Backup',
            "{$tenant->name} ({$tenant->db_name}) restored from Backup #{$backup->id} ({$restoredTables} tables)",
            'bi-arrow-counterclockwise',
            '#DBEAFE',
            '#2563EB'
        );

        return $restoredTables;
    }

    /**
     * Delete old backups beyond the retention limit per tenant (default keep latest 7 per tenant).
     */
    public function rotateBackups(int $keepPerTenant = 7): int
    {
        $deleted = 0;
        $tenantIds = TenantBackup::whereNotNull('tenant_id')->distinct()->pluck('tenant_id');

        foreach ($tenantIds as $tId) {
            $oldBackups = TenantBackup::where('tenant_id', $tId)
                ->orderByDesc('id')
                ->skip($keepPerTenant)
                ->take(100)
                ->get();

            foreach ($oldBackups as $old) {
                $fullPath = storage_path('app/' . $old->file_path);
                if (File::exists($fullPath)) {
                    @File::delete($fullPath);
                }
                $old->delete();
                $deleted++;
            }
        }

        return $deleted;
    }
}
