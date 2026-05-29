<?php

namespace JustBetter\StatamicBase\Tests\Http\Middleware;

use Illuminate\Http\Request;
use JustBetter\StatamicBase\Http\Middleware\AuthorizePackages;
use JustBetter\StatamicBase\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Role;
use Statamic\Facades\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizePackagesTest extends TestCase
{
    #[Test]
    public function it_allows_super_users(): void
    {
        /** @var \Statamic\Auth\File\User $user */
        $user = User::make();
        $user->id('super')->email('super@example.com')->makeSuper();

        $this->actingAs($user);

        $response = (new AuthorizePackages)->handle(Request::create('/cp/justbetter/packages'), fn () => response('ok'));

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_allows_users_with_permission(): void
    {
        $role = Role::make('packages-access')->permissions(['access cp', 'view justbetter packages']);
        $role->save();

        /** @var \Statamic\Auth\File\User $user */
        $user = User::make();
        $user
            ->id('editor')
            ->email('editor@example.com')
            ->assignRole($role)
            ->save();

        $this->actingAs($user);

        $response = (new AuthorizePackages)->handle(Request::create('/cp/justbetter/packages'), fn () => response('ok'));

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_denies_users_without_permission(): void
    {
        $role = Role::make('cp-only')->permissions(['access cp']);
        $role->save();

        /** @var \Statamic\Auth\File\User $user */
        $user = User::make();
        $user
            ->id('guest')
            ->email('guest@example.com')
            ->assignRole($role)
            ->save();

        $this->actingAs($user);

        $this->expectException(HttpException::class);

        (new AuthorizePackages)->handle(Request::create('/cp/justbetter/packages'), fn () => response('ok'));
    }
}
