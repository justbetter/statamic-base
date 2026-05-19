<?php

namespace JustBetter\StatamicBase\Data;

use JustBetter\StatamicBase\Enums\UpdateStatus;

readonly class PackageOverview
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $installedVersion,
        public ?string $latestVersion,
        public UpdateStatus $updateStatus,
        public ?string $addonName,
        public string $type,
    ) {}

    public static function fromDiscovered(
        DiscoveredPackage $package,
        ?string $latestVersion,
        UpdateStatus $updateStatus,
    ): self {
        return new self(
            name: $package->name,
            description: $package->description,
            installedVersion: $package->version,
            latestVersion: $latestVersion,
            updateStatus: $updateStatus,
            addonName: $package->addonName,
            type: $package->type,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'installedVersion' => $this->installedVersion,
            'latestVersion' => $this->latestVersion,
            'updateStatus' => $this->updateStatus->value,
            'addonName' => $this->addonName,
            'type' => $this->type,
        ];
    }
}
