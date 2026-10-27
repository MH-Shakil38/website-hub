<?php

namespace Modules\Pharmacy\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class PharmacyServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Pharmacy';

    protected string $nameLower = 'pharmacy';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
