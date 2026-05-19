<?php

namespace JustBetter\StatamicBase\Tests\Navigation;

use Illuminate\Support\Collection;
use JustBetter\StatamicBase\Navigation\JustBetterNav;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\CP\Navigation\Nav as Navigation;
use Statamic\CP\Navigation\NavItem;

class JustBetterNavTest extends TestCase
{
    #[Test]
    public function it_registers_justbetter_under_tools_with_a_packages_child(): void
    {
        $nav = new Navigation;
        (new JustBetterNav)->register($nav, JustBetterNav::icon(), 'view justbetter packages');

        $section = $nav->find('Tools', 'JustBetter');

        $this->assertNotNull($section);
        $children = $section->children();
        $this->assertInstanceOf(Collection::class, $children);
        $first = $children->first();
        $this->assertInstanceOf(NavItem::class, $first);
        $url = $first->url();
        $this->assertIsString($url);
        $this->assertStringContainsString('justbetter/packages', $url);
    }
}
