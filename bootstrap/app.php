<?php

use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: '/admin',
        );
        $middleware->alias([
            'tenant' => ResolveTenant::class,
            'permission' => EnsurePermission::class,
            'platform.admin' => \App\Http\Middleware\EnsurePlatformAdmin::class,
        ]);
        $middleware->prependToPriorityList(before: SubstituteBindings::class, prepend: ResolveTenant::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->trustProxies(at: '*');
        $hosts = array_values(array_filter(array_map('trim', explode(',', (string) env('TRUSTED_HOSTS', '')))));
        if ($hosts !== []) {
            $middleware->trustHosts(at: fn () => $hosts, subdomains: false);
        }
    })
    ->withCommands([App\Console\Commands\CreatePlatformAdmin::class, App\Console\Commands\ExpireGuestSessions::class, App\Console\Commands\EscalateSlaRequests::class])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
    })->create();
