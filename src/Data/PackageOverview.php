<?php

namespace JustBetter\StatamicBase\Data;

use Illuminate\Support\Fluent;
use JustBetter\StatamicBase\Enums\UpdateStatus;

/**
 * @extends Fluent<string, mixed>
 *
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string $installedVersion
 * @property-read string|null $latestVersion
 * @property-read string $updateStatus
 * @property-read string|null $addonName
 * @property-read string $type
 */
final class PackageOverview extends Fluent
{
    public static function fromDiscovered(
        DiscoveredPackage $package,
        ?string $latestVersion,
        UpdateStatus $updateStatus,
    ): self {
        return new self([
            'name' => $package->name,
            'description' => $package->description,
            'installedVersion' => $package->version,
            'latestVersion' => $latestVersion,
            'updateStatus' => $updateStatus->value,
            'addonName' => $package->addonName,
            'type' => $package->type,
        ]);
    }
}
