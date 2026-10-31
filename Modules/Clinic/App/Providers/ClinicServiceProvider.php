<?php

namespace Modules\Clinic\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ClinicServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Clinic';

    protected string $nameLower = 'clinic';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
