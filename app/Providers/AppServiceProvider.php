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
        // Dynamically bind root URL to incoming request host/scheme to avoid resolving to localhost on remote/mobile devices
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
                ? 'https://' : 'http://';
            \Illuminate\Support\Facades\URL::forceRootUrl($scheme . $_SERVER['HTTP_HOST']);
        }

        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.tailwind');

        // Register custom Blade directives for rules auto-linking & tooltips
        Blade::directive('ruleLink', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::rule($expression); ?>";
        });

        Blade::directive('refLink', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::reference($expression); ?>";
        });

        Blade::directive('traits', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::parseTraits($expression); ?>";
        });

        Blade::directive('natAttacks', function ($expression) {
            return "<?php echo \App\Helpers\RolLink::parseNaturalAttacks($expression); ?>";
        });
    }
}
