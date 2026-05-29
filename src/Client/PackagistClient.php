<?php

namespace JustBetter\StatamicBase\Client;

use Composer\Semver\Semver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PackagistClient
{
    public function latestStableVersion(string $packageName): ?string
    {
        $cacheKey = 'statamic-base.packagist.'.str_replace('/', '.', $packageName);
        $ttl = config()->integer('justbetter.statamic-base.packagist_cache_ttl', 3600);

        /** @var string|null $version */
        $version = Cache::get($cacheKey);

        if (is_string($version)) {
            return $version;
        }

        $version = $this->fetchLatestStableVersion($packageName);

        if ($version !== null) {
            Cache::put($cacheKey, $version, $ttl);
        }

        return $version;
    }

    public function isAvailable(): bool
    {
        return Http::timeout(5)
            ->get('https://packagist.org/packages/justbetter/statamic-base.json')
            ->successful();
    }

    protected function fetchLatestStableVersion(string $packageName): ?string
    {
        return rescue(function () use ($packageName): ?string {
            $response = Http::timeout(5)
                ->get("https://packagist.org/packages/{$packageName}.json")
                ->throw();

            /** @var array<string, mixed>|null $versions */
            $versions = $response->json('package.versions');

            if (! is_array($versions)) {
                return null;
            }

            $stableVersions = collect(array_keys($versions))
                ->filter(fn (string $version): bool => $this->isStable($version))
                ->map(fn (string $version): string => ltrim($version, 'v'))
                ->values()
                ->all();

            if ($stableVersions === []) {
                return null;
            }

            $sorted = Semver::rsort($stableVersions);

            return $sorted[0] ?? null;
        }, null);
    }

    protected function isStable(string $version): bool
    {
        $normalized = strtolower(ltrim($version, 'v'));

        return ! str_contains($normalized, 'dev')
            && ! str_contains($normalized, 'alpha')
            && ! str_contains($normalized, 'beta')
            && ! str_contains($normalized, 'rc');
    }
}
