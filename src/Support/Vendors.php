<?php

namespace JustBetter\StatamicBase\Support;

final class Vendors
{
    /** @var list<string> */
    public const NAMES = [
        'justbetter',
        'just-better',
    ];

    public static function matches(string $packageName): bool
    {
        foreach (self::NAMES as $vendor) {
            if (str_starts_with($packageName, $vendor.'/')) {
                return true;
            }
        }

        return false;
    }
}
