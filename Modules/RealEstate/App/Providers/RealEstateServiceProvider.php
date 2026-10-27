<?php

namespace Modules\RealEstate\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class RealEstateServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'RealEstate';

    protected string $nameLower = 'realestate';

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
