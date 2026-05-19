<?php

namespace JustBetter\StatamicBase;

use Illuminate\Routing\Router;
use JustBetter\StatamicBase\Http\Middleware\AuthorizePackages;
use JustBetter\StatamicBase\Navigation\JustBetterNav;
use JustBetter\StatamicBase\Services\IconFetcher;
use JustBetter\StatamicBase\Services\InstalledPackageDiscovery;
use JustBetter\StatamicBase\Services\PackageOverviewBuilder;
use JustBetter\StatamicBase\Services\PackagistClient;
use JustBetter\StatamicBase\Services\VersionComparator;
use Statamic\Auth\Permission;
use Statamic\CP\Navigation\Nav as Navigation;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission as PermissionFacade;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    /** @phpstan-ignore-next-line */
    protected $vite = [
        'input' => [
            'resources/js/justbetter-statamic-base.js',
            'resources/css/justbetter-statamic-base.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/statamic-base.php', 'justbetter.statamic-base');

        $this->app->singleton(InstalledPackageDiscovery::class);
        $this->app->singleton(PackagistClient::class);
        $this->app->singleton(VersionComparator::class);
        $this->app->singleton(IconFetcher::class);
        $this->app->singleton(PackageOverviewBuilder::class);

        $this->app->booted(function () {
            $router = app(Router::class);
            $router->aliasMiddleware('justbetter.packages', AuthorizePackages::class);
        });
    }

    public function bootAddon(): void
    {
        $this->bootConfig()
            ->bootPermissions()
            ->bootNavigation();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/statamic-base.php' => config_path('justbetter/statamic-base.php'),
        ], 'justbetter-statamic-base');

        return $this;
    }

    protected function bootPermissions(): static
    {
        PermissionFacade::extend(function () {
            PermissionFacade::group('justbetter', 'JustBetter', function () {
                $permission = config()->string('justbetter.statamic-base.permissions.view');

                PermissionFacade::register($permission, function (Permission $permission) {
                    $permission
                        ->label('View JustBetter packages')
                        ->description('Gives the user access to the JustBetter packages overview.');
                });
            });
        });

        return $this;
    }

    protected function bootNavigation(): static
    {
        Nav::extend(function ($nav) {
            if (! $nav instanceof Navigation) {
                return;
            }

            (new JustBetterNav)->register(
                $nav,
                JustBetterNav::icon(),
                config()->string('justbetter.statamic-base.permissions.view'),
            );
        });

        return $this;
    }
}
