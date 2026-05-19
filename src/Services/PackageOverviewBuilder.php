<?php

namespace JustBetter\StatamicBase\Services;

use JustBetter\StatamicBase\Data\DiscoveredPackage;
use JustBetter\StatamicBase\Data\PackageOverview;
use JustBetter\StatamicBase\Data\PackagesIndexData;

class PackageOverviewBuilder
{
    public function __construct(
        private InstalledPackageDiscovery $discovery,
        private PackagistClient $packagist,
        private VersionComparator $versionComparator,
    ) {}

    public function build(): PackagesIndexData
    {
        $packages = $this->discovery->discover();
        $packagistAvailable = $this->packagist->isAvailable();

        $production = [];
        $dev = [];

        foreach ($packages as $package) {
            $overview = $this->buildOverview($package);

            if ($package->isDev) {
                $dev[] = $overview;
            } else {
                $production[] = $overview;
            }
        }

        return new PackagesIndexData(
            productionPackages: $production,
            devPackages: $dev,
            packagistAvailable: $packagistAvailable,
        );
    }

    protected function buildOverview(DiscoveredPackage $package): PackageOverview
    {
        $latest = $this->packagist->latestStableVersion($package->name);
        $status = $this->versionComparator->compare($package->version, $latest);

        return PackageOverview::fromDiscovered($package, $latest, $status);
    }
}
