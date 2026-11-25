<?php

namespace Modules\EventManagement\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class EventManagementServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'EventManagement';

    protected string $nameLower = 'eventmanagement';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
