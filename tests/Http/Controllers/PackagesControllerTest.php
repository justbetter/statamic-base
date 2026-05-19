<?php

namespace JustBetter\StatamicBase\Tests\Http\Controllers;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;
use JustBetter\StatamicBase\Services\InstalledPackageDiscovery;
use JustBetter\StatamicBase\Services\PackageOverviewBuilder;
use JustBetter\StatamicBase\Services\PackagistClient;
use JustBetter\StatamicBase\Services\VersionComparator;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Role;
use Statamic\Facades\User;

class PackagesControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $discovery = new InstalledPackageDiscovery(
            lockFilePath: $this->fixturePath('composer.lock'),
            vendorPath: $this->fixturePath('vendor'),
        );

        $app = $this->app;
        $this->assertNotNull($app);

        $app->instance(InstalledPackageDiscovery::class, $discovery);
        $app->instance(PackageOverviewBuilder::class, new PackageOverviewBuilder(
            $discovery,
            new PackagistClient,
            new VersionComparator,
        ));

        Http::preventStrayRequests();

        Http::fake(function (Request $request) {
            return Http::response([
                'package' => [
                    'versions' => [
                        '1.0.0' => [],
                        '1.1.0' => [],
                        '2.2.0' => [],
                        '0.6.0' => [],
                    ],
                ],
            ]);
        });
    }

    #[Test]
    public function it_renders_the_packages_overview(): void
    {
        /** @var \Statamic\Auth\File\User $user */
        $user = User::make();
        $user->id('super')->email('super@example.com')->makeSuper();

        $this->actingAs($user);

        $response = $this->get(cp_route('justbetter.packages.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('statamic-base::Packages/Index')
            ->has('productionPackages', 2)
            ->has('devPackages', 1)
            ->where('packagistAvailable', true)
            ->has('icon'));
    }

    #[Test]
    public function it_requires_permission(): void
    {
        $role = Role::make('cp-only')->permissions(['access cp']);
        $role->save();

        /** @var \Statamic\Auth\File\User $user */
        $user = User::make();
        $user
            ->id('guest')
            ->email('guest@example.com')
            ->assignRole($role)
            ->save();

        $this->actingAs($user);

        $this->get(cp_route('justbetter.packages.index'))->assertForbidden();
    }
}
