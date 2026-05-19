<?php

namespace JustBetter\StatamicBase\Tests\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JustBetter\StatamicBase\Services\PackagistClient;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PackagistClientTest extends TestCase
{
    private PackagistClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::preventStrayRequests();

        $this->client = new PackagistClient;
    }

    #[Test]
    public function it_returns_the_latest_stable_version(): void
    {
        Http::fake(fn () => Http::response([
            'package' => [
                'versions' => [
                    'dev-main' => [],
                    '1.0.0' => [],
                    '1.2.0' => [],
                    'v1.1.0' => [],
                    '2.0.0-beta1' => [],
                ],
            ],
        ]));

        $this->assertSame('1.2.0', $this->client->latestStableVersion('justbetter/statamic-base'));
    }

    #[Test]
    public function it_returns_cached_versions(): void
    {
        Cache::put('statamic-base.packagist.justbetter.statamic-base', '9.9.9', 3600);

        Http::fake();

        $this->assertSame('9.9.9', $this->client->latestStableVersion('justbetter/statamic-base'));

        Http::assertNothingSent();
    }

    #[Test]
    public function it_returns_null_when_versions_are_missing(): void
    {
        Http::fake(fn () => Http::response(['package' => []]));

        $this->assertNull($this->client->latestStableVersion('justbetter/statamic-base'));
    }

    #[Test]
    public function it_returns_null_when_only_unstable_versions_exist(): void
    {
        Http::fake(fn () => Http::response([
            'package' => [
                'versions' => [
                    'dev-main' => [],
                    '2.0.0-beta1' => [],
                ],
            ],
        ]));

        $this->assertNull($this->client->latestStableVersion('justbetter/statamic-base'));
    }

    #[Test]
    public function it_returns_null_when_packagist_throws(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection failed'));

        $this->assertNull($this->client->latestStableVersion('justbetter/statamic-base'));
    }

    #[Test]
    public function it_caches_successful_responses(): void
    {
        Http::fake(fn () => Http::response([
            'package' => [
                'versions' => [
                    '1.0.0' => [],
                ],
            ],
        ]));

        $this->client->latestStableVersion('justbetter/statamic-base');
        $this->client->latestStableVersion('justbetter/statamic-base');

        Http::assertSentCount(1);
    }

    #[Test]
    public function it_returns_null_when_packagist_fails(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $this->assertNull($this->client->latestStableVersion('justbetter/statamic-base'));
    }

    #[Test]
    public function it_can_detect_packagist_availability(): void
    {
        Http::fake(fn () => Http::response(['package' => ['versions' => ['1.0.0' => []]]]));

        $this->assertTrue($this->client->isAvailable());
    }

    #[Test]
    public function it_detects_when_packagist_is_unavailable(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $this->assertFalse($this->client->isAvailable());
    }

    #[Test]
    public function it_detects_when_packagist_throws(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection failed'));

        $this->assertFalse($this->client->isAvailable());
    }
}
