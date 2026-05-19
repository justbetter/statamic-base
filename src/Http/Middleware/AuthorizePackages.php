<?php

namespace JustBetter\StatamicBase\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\User;
use Symfony\Component\HttpFoundation\Response;

class AuthorizePackages
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::current();
        $permission = config()->string('justbetter.statamic-base.permissions.view');

        abort_unless(
            $user && ($user->isSuper() || $user->hasPermission($permission)),
            403
        );

        return $next($request);
    }
}
