<?php

namespace JustBetter\StatamicBase\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JustBetter\StatamicBase\Data\DiscoveredPackage;
use JustBetter\StatamicBase\Support\Vendors;
use RuntimeException;

class InstalledPackageDiscovery
{
    public function __construct(
        private ?string $lockFilePath = null,
        private ?string $vendorPath = null,
    ) {}

    /**
     * @return Collection<int, DiscoveredPackage>
     */
    public function discover(): Collection
    {
        $lock = $this->readLockFile();

        $production = collect($lock['packages'] ?? [])
            ->filter(fn (array $package): bool => $this->shouldInclude($package))
            ->map(fn (array $package): DiscoveredPackage => $this->mapPackage($package, isDev: false));

        $development = collect($lock['packages-dev'] ?? [])
            ->filter(fn (array $package): bool => $this->shouldInclude($package))
            ->map(fn (array $package): DiscoveredPackage => $this->mapPackage($package, isDev: true));

        return $production
            ->merge($development)
            ->sortBy(fn (DiscoveredPackage $package): string => $package->name)
            ->values();
    }

    /**
     * @return array{packages?: list<array<string, mixed>>, packages-dev?: list<array<string, mixed>>}
     */
    protected function readLockFile(): array
    {
        $path = $this->lockFilePath ?? base_path('composer.lock');

        if (! File::exists($path)) {
            throw new RuntimeException("Composer lock file not found at [{$path}].");
        }

        /** @var array{packages?: list<array<string, mixed>>, packages-dev?: list<array<string, mixed>>}|null $lock */
        $lock = json_decode(File::get($path), true);

        if (! is_array($lock)) {
            throw new RuntimeException("Composer lock file at [{$path}] is invalid.");
        }

        return $lock;
    }

    /**
     * @param  array<string, mixed>  $package
     */
    protected function shouldInclude(array $package): bool
    {
        if (! is_string($package['name'] ?? null)) {
            throw new RuntimeException('Invalid package entry in composer.lock.');
        }

        return Vendors::matches($package['name']);
    }

    /**
     * @param  array<string, mixed>  $package
     */
    protected function mapPackage(array $package, bool $isDev): DiscoveredPackage
    {
        if (! is_string($package['name'] ?? null) || ! is_string($package['version'] ?? null)) {
            throw new RuntimeException('Invalid package entry in composer.lock.');
        }

        $name = $package['name'];
        $composer = $this->readPackageComposer($name);
        $description = $package['description'] ?? $composer['description'] ?? null;

        return new DiscoveredPackage([
            'name' => $name,
            'version' => $this->normalizeVersion($package['version']),
            'description' => is_string($description) ? $description : null,
            'type' => $this->resolveType($composer),
            'addonName' => ($addonName = Arr::get($composer, 'extra.statamic.name')) && is_string($addonName) ? $addonName : null,
            'isDev' => $isDev,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function readPackageComposer(string $packageName): array
    {
        $path = $this->vendorDirectory().'/'.$packageName.'/composer.json';

        if (! File::exists($path)) {
            return [];
        }

        /** @var array<string, mixed>|null $composer */
        $composer = json_decode(File::get($path), true);

        return is_array($composer) ? $composer : [];
    }

    protected function vendorDirectory(): string
    {
        return $this->vendorPath ?? base_path('vendor');
    }

    protected function normalizeVersion(string $version): string
    {
        return ltrim($version, 'v');
    }

    /**
     * @param  array<string, mixed>  $composer
     */
    protected function resolveType(array $composer): string
    {
        if (($composer['type'] ?? null) === 'statamic-addon' || is_array($composer['extra'] ?? null) && isset($composer['extra']['statamic'])) {
            return 'statamic-addon';
        }

        $type = $composer['type'] ?? 'library';

        return is_string($type) ? $type : 'library';
    }
}
