<?php

namespace JustBetter\StatamicBase\Tests\Services;

use Illuminate\Support\Facades\Config;
use JustBetter\StatamicBase\Services\IconFetcher;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IconFetcherTest extends TestCase
{
    private IconFetcher $fetcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetcher = new IconFetcher;
    }

    #[Test]
    public function it_embeds_the_configured_icon_url_in_an_svg_image(): void
    {
        Config::set('justbetter.statamic-base.icon_url', 'https://example.com/logo.svg');

        $markup = $this->fetcher->fetch();

        $this->assertStringStartsWith('<svg', $markup);
        $this->assertStringContainsString('<image', $markup);
        $this->assertStringContainsString('href="https://example.com/logo.svg"', $markup);
        $this->assertStringContainsString('xlink:href="https://example.com/logo.svg"', $markup);
    }

    #[Test]
    public function it_escapes_xml_special_characters_in_the_icon_url(): void
    {
        Config::set('justbetter.statamic-base.icon_url', 'https://example.com/logo.svg?foo=1&bar=2');

        $markup = $this->fetcher->fetch();

        $this->assertStringContainsString('foo=1&amp;bar=2', $markup);
        $this->assertStringNotContainsString('&bar=2"', $markup);
    }

    #[Test]
    public function it_falls_back_when_icon_url_is_empty(): void
    {
        Config::set('justbetter.statamic-base.icon_url', '');

        $this->assertSame(IconFetcher::FALLBACK_ICON, $this->fetcher->fetch());
    }

    #[Test]
    public function it_falls_back_when_icon_url_is_invalid(): void
    {
        Config::set('justbetter.statamic-base.icon_url', 'not-a-url');

        $this->assertSame(IconFetcher::FALLBACK_ICON, $this->fetcher->fetch());
    }

    #[Test]
    public function it_outputs_light_and_dark_images_when_icon_dark_url_is_set(): void
    {
        Config::set('justbetter.statamic-base.icon_url', 'https://example.com/light.svg');
        Config::set('justbetter.statamic-base.icon_dark_url', 'https://example.com/dark.svg');

        $markup = $this->fetcher->fetch();

        $this->assertStringContainsString('class="dark:hidden"', $markup);
        $this->assertStringContainsString('class="hidden dark:block"', $markup);
        $this->assertStringContainsString('href="https://example.com/light.svg"', $markup);
        $this->assertStringContainsString('href="https://example.com/dark.svg"', $markup);
    }

    #[Test]
    public function it_ignores_invalid_icon_dark_url_and_uses_light_only(): void
    {
        Config::set('justbetter.statamic-base.icon_url', 'https://example.com/light.svg');
        Config::set('justbetter.statamic-base.icon_dark_url', 'not-a-url');

        $markup = $this->fetcher->fetch();

        $this->assertStringNotContainsString('dark:hidden', $markup);
        $this->assertStringContainsString('href="https://example.com/light.svg"', $markup);
    }
}
