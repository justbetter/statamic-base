<?php

namespace JustBetter\StatamicBase\Tests\Support;

use JustBetter\StatamicBase\Support\Vendors;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class VendorsTest extends TestCase
{
    #[Test]
    public function it_matches_justbetter_packages(): void
    {
        $this->assertTrue(Vendors::matches('justbetter/statamic-base'));
    }

    #[Test]
    public function it_matches_just_better_packages(): void
    {
        $this->assertTrue(Vendors::matches('just-better/statamic-dev-tools'));
    }

    #[Test]
    public function it_does_not_match_other_packages(): void
    {
        $this->assertFalse(Vendors::matches('acme/example'));
    }
}
