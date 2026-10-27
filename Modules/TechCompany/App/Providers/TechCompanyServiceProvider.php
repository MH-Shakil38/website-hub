<?php

namespace Modules\TechCompany\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class TechCompanyServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'TechCompany';

    protected string $nameLower = 'techcompany';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
