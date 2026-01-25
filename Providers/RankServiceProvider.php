<?php

namespace Modules\Rank\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;


class RankServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Rank';
    protected string $moduleNameLower = 'rank';
    public function boot()
    {
        $this->mapWebRoutes();
        // Register Rank Assets
        $this->publishes([
            module_path('Rank', 'Views/assets')
            => base_path('assets/rank'), # TODO: There is high possibility for this to fail on production, let wait and see
        ], 'rank-assets');
        // Load Views
        $this->loadViewsFrom(module_path($this->moduleName, 'Views'), $this->moduleNameLower);
    }
    /**
     * Define the "web" routes for the module.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware(['web'])
            ->group(module_path($this->moduleName, '/Http/Routes/web.php'));
    }
}
