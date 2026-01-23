<?php

namespace Modules\Leaderboard\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;


class LeaderboardServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Leaderboard';
    protected string $moduleNameLower = 'leaderboard';
    public function boot()
    {
        $this->mapWebRoutes();
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
