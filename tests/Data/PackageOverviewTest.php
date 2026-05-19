<?php

namespace JustBetter\StatamicBase\Tests\Data;

use JustBetter\StatamicBase\Data\DiscoveredPackage;
use JustBetter\StatamicBase\Data\PackageOverview;
use JustBetter\StatamicBase\Data\PackagesIndexData;
use JustBetter\StatamicBase\Enums\UpdateStatus;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PackageOverviewTest extends TestCase
{
    #[Test]
    public function it_serializes_package_and_index_data(): void
    {
        $overview = PackageOverview::fromDiscovered(
            new DiscoveredPackage([
                'name' => 'justbetter/statamic-base',
                'version' => '1.0.0',
                'description' => 'Foundation addon',
                'type' => 'statamic-addon',
                'addonName' => 'Statamic Base',
                'isDev' => false,
            ]),
            latestVersion: '1.1.0',
            updateStatus: UpdateStatus::Minor,
        );

        $this->assertSame([
            'name' => 'justbetter/statamic-base',
            'description' => 'Foundation addon',
            'installedVersion' => '1.0.0',
            'latestVersion' => '1.1.0',
            'updateStatus' => 'minor',
            'addonName' => 'Statamic Base',
            'type' => 'statamic-addon',
        ], $overview->toArray());

        $index = new PackagesIndexData([
            'productionPackages' => [$overview],
            'devPackages' => [],
            'packagistAvailable' => true,
        ]);

        $this->assertSame([
            'productionPackages' => [$overview->toArray()],
            'devPackages' => [],
            'packagistAvailable' => true,
        ], $index->toArray());
    }
}
