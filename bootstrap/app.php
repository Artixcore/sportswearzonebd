<?php

use App\Support\DuplicateDatabaseEntry;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (UniqueConstraintViolationException $e, Request $request): ?Response {
            if ($request->expectsJson()) {
                report($e);

                return response()->json([
                    'message' => 'A duplicate value was submitted. Change any duplicate fields and try again.',
                ], 422);
            }

            if (! $request->is('admin') && ! $request->is('admin/*')) {
                return null;
            }

            report($e);

            [$fieldErrors, $summary] = DuplicateDatabaseEntry::toValidationAndMessage($e);

            return back(fallback: route('admin.dashboard'))
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors($fieldErrors)
                ->with('error', $summary);
        });
    })->create();
