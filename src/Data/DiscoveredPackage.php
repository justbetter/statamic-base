<?php

namespace JustBetter\StatamicBase\Data;

readonly class DiscoveredPackage
{
    public function __construct(
        public string $name,
        public string $version,
        public ?string $description,
        public string $type,
        public ?string $addonName,
        public bool $isDev,
    ) {}
}
