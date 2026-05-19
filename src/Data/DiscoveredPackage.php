<?php

namespace JustBetter\StatamicBase\Data;

use Illuminate\Support\Fluent;

/**
 * @extends Fluent<string, mixed>
 *
 * @property-read string $name
 * @property-read string $version
 * @property-read string|null $description
 * @property-read string $type
 * @property-read string|null $addonName
 * @property-read bool $isDev
 */
final class DiscoveredPackage extends Fluent {}
