<?php

namespace JustBetter\StatamicBase\Navigation;

use JustBetter\StatamicBase\Services\IconFetcher;
use Statamic\CP\Navigation\Nav as Navigation;

class JustBetterNav
{
    public function register(Navigation $nav, string $icon, string $permission): void
    {
        $packages = $nav->item('Packages');
        $packages->route('justbetter.packages.index');
        $packages->can($permission);

        $justBetter = $nav->create('JustBetter');
        $justBetter->section('Tools');
        $justBetter->route('justbetter.packages.index');
        $justBetter->icon($icon);
        $justBetter->can($permission);
        $justBetter->children([$packages]);
    }

    public static function icon(): string
    {
        return app(IconFetcher::class)->fetch();
    }
}
