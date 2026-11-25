<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Refuse to boot the test app against a real database.
     *
     * Some tests use RefreshDatabase (migrate:fresh) and others create/drop tenant
     * databases, so the suite must only ever touch a dedicated *_testing database
     * and a test-only tenant DB prefix (phpunit.xml sets both). This check runs
     * before any trait such as RefreshDatabase can touch the database.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $config = $app['config'];
        $database = (string) $config->get('database.connections.'.$config->get('database.default').'.database');
        $prefix = (string) $config->get('app.tenant_db_prefix');

        if (! str_ends_with($database, '_testing') || $prefix === '' || $prefix === 'hub_t_') {
            throw new RuntimeException(
                "Refusing to run tests against database [{$database}] with tenant prefix [{$prefix}]. ".
                'Tests need a *_testing database and a test-only TENANT_DB_PREFIX (see phpunit.xml). '.
                'If config is cached, run: php artisan config:clear'
            );
        }

        return $app;
    }
}
