<?php

namespace JustBetter\StatamicBase\Tests\ServiceProvider;

use JustBetter\StatamicBase\ServiceProvider;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use ReflectionProperty;
use Statamic\CP\Navigation\Nav as Navigation;
use Statamic\Facades\Addon;
use Statamic\Facades\CP\Nav;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_addon(): void
    {
        $this->assertSame('justbetter/statamic-base', Addon::get('justbetter/statamic-base')->id());
    }

    #[Test]
    public function it_registers_navigation_through_the_nav_extension(): void
    {
        $navigation = new Navigation;
        Nav::swap($navigation);

        $app = $this->app;
        $this->assertNotNull($app);

        $bootNavigation = new ReflectionMethod(ServiceProvider::class, 'bootNavigation');
        $bootNavigation->invoke($app->getProvider(ServiceProvider::class));

        $extensions = new ReflectionProperty(Navigation::class, 'extensions');
        $extensions->setAccessible(true);

        /** @var list<callable(Navigation): void> $callbacks */
        $callbacks = $extensions->getValue($navigation);

        foreach ($callbacks as $callback) {
            $callback($navigation);
        }

        $this->assertNotNull($navigation->find('Tools', 'JustBetter'));
    }

    #[Test]
    public function it_skips_navigation_registration_for_invalid_nav_instances(): void
    {
        $navigation = new Navigation;
        Nav::swap($navigation);

        $app = $this->app;
        $this->assertNotNull($app);

        $bootNavigation = new ReflectionMethod(ServiceProvider::class, 'bootNavigation');
        $bootNavigation->invoke($app->getProvider(ServiceProvider::class));

        $extensions = new ReflectionProperty(Navigation::class, 'extensions');
        $extensions->setAccessible(true);

        /** @var list<callable(mixed): void> $callbacks */
        $callbacks = $extensions->getValue($navigation);

        foreach ($callbacks as $callback) {
            $callback(new \stdClass);
        }

        $this->assertNull($navigation->find('Tools', 'JustBetter'));
    }
}
