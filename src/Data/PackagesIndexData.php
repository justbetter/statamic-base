<?php

namespace JustBetter\StatamicBase\Data;

use Illuminate\Support\Fluent;

/**
 * @extends Fluent<string, mixed>
 *
 * @property-read list<PackageOverview> $productionPackages
 * @property-read list<PackageOverview> $devPackages
 * @property-read bool $packagistAvailable
 */
final class PackagesIndexData extends Fluent
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $rawProduction = $this->get('productionPackages', []);
        $rawDev = $this->get('devPackages', []);

        $production = [];
        if (is_array($rawProduction)) {
            foreach ($rawProduction as $package) {
                if ($package instanceof PackageOverview) {
                    $production[] = $package;
                }
            }
        }

        $dev = [];
        if (is_array($rawDev)) {
            foreach ($rawDev as $package) {
                if ($package instanceof PackageOverview) {
                    $dev[] = $package;
                }
            }
        }

        return [
            'productionPackages' => array_map(
                static fn (PackageOverview $package): array => $package->toArray(),
                $production,
            ),
            'devPackages' => array_map(
                static fn (PackageOverview $package): array => $package->toArray(),
                $dev,
            ),
            'packagistAvailable' => (bool) $this->get('packagistAvailable'),
        ];
    }
}
