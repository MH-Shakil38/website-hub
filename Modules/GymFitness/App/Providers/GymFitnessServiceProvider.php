<?php

namespace Modules\GymFitness\App\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class GymFitnessServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'GymFitness';

    protected string $nameLower = 'gymfitness';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    protected function registerConfig(): void
    {
        parent::registerConfig();
        $this->mergeConfigFrom(module_path($this->name, 'Config/config.php'), $this->nameLower);
    }
}
