<?php

namespace JustBetter\StatamicBase\Data;

readonly class PackagesIndexData
{
    /**
     * @param  list<PackageOverview>  $productionPackages
     * @param  list<PackageOverview>  $devPackages
     */
    public function __construct(
        public array $productionPackages,
        public array $devPackages,
        public bool $packagistAvailable,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'productionPackages' => array_map(
                fn (PackageOverview $package): array => $package->toArray(),
                $this->productionPackages,
            ),
            'devPackages' => array_map(
                fn (PackageOverview $package): array => $package->toArray(),
                $this->devPackages,
            ),
            'packagistAvailable' => $this->packagistAvailable,
        ];
    }
}
