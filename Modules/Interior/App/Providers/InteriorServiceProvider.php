<?php

namespace Modules\Interior\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class InteriorServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Interior';

    protected string $nameLower = 'interior';

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
