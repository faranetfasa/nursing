<?php

use App\Modules\Core\Http\Middleware\EnsureApplicationIsInstalled;
use App\Modules\Core\Http\Middleware\EnsurePermission;
use App\Modules\Core\Http\Middleware\SetLocaleAndDirection;
use App\Support\ErrorReporter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            EnsureApplicationIsInstalled::class,
            SetLocaleAndDirection::class,
        ]);

        $middleware->alias([
            'installed' => EnsureApplicationIsInstalled::class,
            'permission' => EnsurePermission::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn (): string => route('core.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Every unexpected failure gets a short error id which is logged and shown
        // to the user, so a report can be traced back to the log entry (item 131).
        $exceptions->report(fn (Throwable $exception): bool => app(ErrorReporter::class)->report($exception));

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (ErrorReporter::isExpected($exception) || app()->hasDebugModeEnabled()) {
                return null;
            }

            $errorId = app(ErrorReporter::class)->errorId($exception);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'خطای غیرمنتظره‌ای رخ داد. کد خطا: '.$errorId,
                    'error_id' => $errorId,
                ], 500);
            }

            return response()->view('core::errors.exception', ['errorId' => $errorId], 500);
        });
    })->create();
