<?php

namespace JustBetter\StatamicBase\Services;

use Composer\Semver\VersionParser;
use JustBetter\StatamicBase\Enums\UpdateStatus;

class VersionComparator
{
    public function __construct(
        private VersionParser $versionParser = new VersionParser,
    ) {}

    public function compare(string $installed, ?string $latest): UpdateStatus
    {
        if ($latest === null) {
            return UpdateStatus::Unknown;
        }

        $installed = ltrim($installed, 'v');
        $latest = ltrim($latest, 'v');

        if (version_compare($installed, $latest, '>=')) {
            return UpdateStatus::UpToDate;
        }

        $installedParts = $this->majorMinorPatch($installed);
        $latestParts = $this->majorMinorPatch($latest);

        if ($installedParts === null || $latestParts === null) {
            return UpdateStatus::Unknown;
        }

        if ($latestParts[0] > $installedParts[0]) {
            return UpdateStatus::Major;
        }

        if ($latestParts[1] > $installedParts[1]) {
            return UpdateStatus::Minor;
        }

        return UpdateStatus::Patch;
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    protected function majorMinorPatch(string $version): ?array
    {
        try {
            $normalized = $this->versionParser->normalize($version);
        } catch (\UnexpectedValueException) {
            return null;
        }

        $parts = explode('.', $normalized);

        if (count($parts) < 3 || ! is_numeric($parts[0]) || ! is_numeric($parts[1]) || ! is_numeric($parts[2])) {
            return null;
        }

        return [
            (int) $parts[0],
            (int) $parts[1],
            (int) $parts[2],
        ];
    }
}
