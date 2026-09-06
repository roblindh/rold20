<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'utilities/character-generator/*',
            'utilities/chargen/*',
            'utilities/npc-generator/*',
            'utilities/npcgen/*',
            'utilities/item-generator/*',
            'utilities/itemgen/*',
            'utilities/treasure-generator/*',
            'utilities/treasuregen/*',
            'character-generator/*',
            'chargen/*',
            'npc-generator/*',
            'npcgen/*',
            'item-generator/*',
            'itemgen/*',
            'treasure-generator/*',
            'treasuregen/*',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetCacheHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
