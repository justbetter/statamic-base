<?php

namespace JustBetter\StatamicBase\Tests;

use JustBetter\StatamicBase\ServiceProvider;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

abstract class TestCase extends AddonTestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:7tG0yY7g3QkFrQ+Vk4EBSbcT8D9C4/5Dph1dNRjh6WU=');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('justbetter.statamic-base.packagist_cache_ttl', 3600);
        $app['config']->set('justbetter.statamic-base.icon_url', 'https://opensource.justbetter.nl/statamic/justbetter-logo-small-black.svg');
        $app['config']->set('justbetter.statamic-base.icon_dark_url', null);
        $app['config']->set('justbetter.statamic-base.permissions.view', 'view justbetter packages');
    }

    protected function fixturePath(string $path = ''): string
    {
        return __DIR__.'/__fixtures__'.($path !== '' ? '/'.$path : '');
    }
}
