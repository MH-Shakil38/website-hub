<?php

namespace Modules\WonderLearn\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class WonderLearnServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'WonderLearn';

    protected string $nameLower = 'wonderlearn';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
