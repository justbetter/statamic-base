<?php

namespace JustBetter\StatamicBase\Tests\Services;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JustBetter\StatamicBase\Client\PackagistClient;
use JustBetter\StatamicBase\Enums\UpdateStatus;
use JustBetter\StatamicBase\Services\InstalledPackageDiscovery;
use JustBetter\StatamicBase\Services\PackageOverviewBuilder;
use JustBetter\StatamicBase\Services\VersionComparator;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PackageOverviewBuilderTest extends TestCase
{
    #[Test]
    public function it_builds_production_and_dev_overviews(): void
    {
        Http::preventStrayRequests();

        Http::fake(function (Request $request) {
            $versions = match (true) {
                str_contains($request->url(), 'statamic-base.json') => ['1.0.0' => [], '1.1.0' => []],
                str_contains($request->url(), 'statamic-glide.json') => ['2.1.0' => [], '2.2.0' => []],
                str_contains($request->url(), 'statamic-dev-tools.json') => ['0.5.0' => [], '0.6.0' => []],
                default => ['1.0.0' => []],
            };

            return Http::response(['package' => ['versions' => $versions]]);
        });

        $data = $this->builder()->build();

        $this->assertTrue($data->packagistAvailable);
        $this->assertCount(2, $data->productionPackages);
        $this->assertCount(1, $data->devPackages);

        $base = collect($data->productionPackages)->firstWhere('name', 'justbetter/statamic-base');
        $this->assertNotNull($base);
        $this->assertSame(UpdateStatus::Minor->value, $base->updateStatus);

        $dev = $data->devPackages[0];
        $this->assertSame('just-better/statamic-dev-tools', $dev->name);
        $this->assertSame(UpdateStatus::Minor->value, $dev->updateStatus);
    }

    #[Test]
    public function it_marks_packagist_as_unavailable_when_unreachable(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $data = $this->builder()->build();

        $this->assertFalse($data->packagistAvailable);
        $this->assertSame(UpdateStatus::Unknown->value, $data->productionPackages[0]->updateStatus);
    }

    protected function builder(): PackageOverviewBuilder
    {
        $discovery = new InstalledPackageDiscovery(
            lockFilePath: $this->fixturePath('composer.lock'),
            vendorPath: $this->fixturePath('vendor'),
        );

        return new PackageOverviewBuilder(
            $discovery,
            new PackagistClient,
            new VersionComparator,
        );
    }
}
