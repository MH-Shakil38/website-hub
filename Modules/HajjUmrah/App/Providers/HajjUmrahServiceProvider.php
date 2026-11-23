<?php

namespace Modules\HajjUmrah\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class HajjUmrahServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'HajjUmrah';

    protected string $nameLower = 'hajjumrah';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
