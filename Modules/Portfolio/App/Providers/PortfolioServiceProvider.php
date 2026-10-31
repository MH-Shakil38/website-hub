<?php

namespace Modules\Portfolio\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class PortfolioServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Portfolio';

    protected string $nameLower = 'portfolio';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
