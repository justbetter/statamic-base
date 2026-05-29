<?php

namespace JustBetter\StatamicBase\Tests\Services;

use JustBetter\StatamicBase\Enums\UpdateStatus;
use JustBetter\StatamicBase\Services\VersionComparator;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class VersionComparatorTest extends TestCase
{
    private VersionComparator $comparator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comparator = new VersionComparator;
    }

    #[Test]
    public function it_returns_unknown_when_latest_is_missing(): void
    {
        $this->assertSame(UpdateStatus::Unknown, $this->comparator->compare('1.0.0', null));
    }

    #[Test]
    public function it_returns_up_to_date_when_installed_is_current(): void
    {
        $this->assertSame(UpdateStatus::UpToDate, $this->comparator->compare('1.2.3', '1.2.3'));
        $this->assertSame(UpdateStatus::UpToDate, $this->comparator->compare('v1.2.3', '1.2.3'));
        $this->assertSame(UpdateStatus::UpToDate, $this->comparator->compare('2.0.0', '1.9.9'));
    }

    #[Test]
    public function it_detects_patch_minor_and_major_updates(): void
    {
        $this->assertSame(UpdateStatus::Patch, $this->comparator->compare('1.2.3', '1.2.4'));
        $this->assertSame(UpdateStatus::Minor, $this->comparator->compare('1.2.3', '1.3.0'));
        $this->assertSame(UpdateStatus::Major, $this->comparator->compare('1.2.3', '2.0.0'));
    }
}
