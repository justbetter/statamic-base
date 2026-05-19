<?php

namespace JustBetter\StatamicBase\Http\Controllers\CP;

use Inertia\Inertia;
use Inertia\Response;
use JustBetter\StatamicBase\Services\IconFetcher;
use JustBetter\StatamicBase\Services\PackageOverviewBuilder;
use Statamic\Http\Controllers\CP\CpController;

class PackagesController extends CpController
{
    public function __construct(
        private PackageOverviewBuilder $overviewBuilder,
        private IconFetcher $iconFetcher,
    ) {}

    public function index(): Response
    {
        return Inertia::render('statamic-base::Packages/Index', [
            ...$this->overviewBuilder->build()->toArray(),
            'icon' => $this->iconFetcher->fetch(),
        ]);
    }
}
