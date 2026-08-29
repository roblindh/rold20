<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\RolLink;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.tailwind');

        // Register custom Blade directives for rules auto-linking & tooltips
        Blade::directive('ruleLink', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::rule($expression); ?>";
        });

        Blade::directive('refLink', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::reference($expression); ?>";
        });
    }
}
