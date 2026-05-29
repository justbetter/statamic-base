<?php

namespace JustBetter\StatamicBase\Tests\Services;

use JustBetter\StatamicBase\Services\InstalledPackageDiscovery;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

class InstalledPackageDiscoveryTest extends TestCase
{
    private InstalledPackageDiscovery $discovery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discovery = new InstalledPackageDiscovery(
            lockFilePath: $this->fixturePath('composer.lock'),
            vendorPath: $this->fixturePath('vendor'),
        );
    }

    #[Test]
    public function it_discovers_production_and_dev_packages(): void
    {
        $packages = $this->discovery->discover();

        $this->assertCount(3, $packages);

        $base = $packages->firstWhere('name', 'justbetter/statamic-base');
        $this->assertNotNull($base);
        $this->assertSame('1.0.0', $base->version);
        $this->assertSame('statamic-addon', $base->type);
        $this->assertSame('Statamic Base', $base->addonName);
        $this->assertFalse($base->isDev);

        $dev = $packages->firstWhere('name', 'just-better/statamic-dev-tools');
        $this->assertNotNull($dev);
        $this->assertTrue($dev->isDev);
        $this->assertSame('Dev Tools', $dev->addonName);
    }

    #[Test]
    public function it_throws_when_lock_file_is_missing(): void
    {
        $discovery = new InstalledPackageDiscovery(
            lockFilePath: $this->fixturePath('missing.lock'),
            vendorPath: $this->fixturePath('vendor'),
        );

        $this->expectException(RuntimeException::class);

        $discovery->discover();
    }

    #[Test]
    public function it_throws_when_a_package_entry_is_invalid(): void
    {
        $path = $this->fixturePath('invalid-package.lock');
        file_put_contents($path, json_encode([
            'packages' => [
                ['name' => 123, 'version' => '1.0.0'],
            ],
        ]));

        try {
            $discovery = new InstalledPackageDiscovery(
                lockFilePath: $path,
                vendorPath: $this->fixturePath('vendor'),
            );

            $this->expectException(RuntimeException::class);

            $discovery->discover();
        } finally {
            unlink($path);
        }
    }

    #[Test]
    public function it_throws_when_a_package_version_is_invalid(): void
    {
        $path = $this->fixturePath('invalid-version.lock');
        file_put_contents($path, json_encode([
            'packages' => [
                ['name' => 'justbetter/statamic-base', 'version' => 123],
            ],
        ]));

        try {
            $discovery = new InstalledPackageDiscovery(
                lockFilePath: $path,
                vendorPath: $this->fixturePath('vendor'),
            );

            $this->expectException(RuntimeException::class);

            $discovery->discover();
        } finally {
            unlink($path);
        }
    }

    #[Test]
    public function it_handles_packages_without_vendor_composer_files(): void
    {
        $path = $this->fixturePath('missing-vendor.lock');
        file_put_contents($path, json_encode([
            'packages' => [
                [
                    'name' => 'justbetter/statamic-missing',
                    'version' => 'v3.0.0',
                    'type' => 'library',
                ],
            ],
        ]));

        try {
            $discovery = new InstalledPackageDiscovery(
                lockFilePath: $path,
                vendorPath: $this->fixturePath('vendor'),
            );

            $package = $discovery->discover()->firstWhere('name', 'justbetter/statamic-missing');

            $this->assertNotNull($package);
            $this->assertSame('library', $package->type);
            $this->assertNull($package->addonName);
        } finally {
            unlink($path);
        }
    }

    #[Test]
    public function it_throws_when_lock_file_is_invalid(): void
    {
        $path = $this->fixturePath('invalid.lock');
        file_put_contents($path, 'not-json');

        try {
            $discovery = new InstalledPackageDiscovery(
                lockFilePath: $path,
                vendorPath: $this->fixturePath('vendor'),
            );

            $this->expectException(RuntimeException::class);

            $discovery->discover();
        } finally {
            unlink($path);
        }
    }
}
