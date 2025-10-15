<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 🧱 Costruzione dell'app Laravel 12
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        // api: __DIR__.'/../routes/api.php', // se hai anche api
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Qui puoi aggiungere o rimuovere middleware

        // 👇 Escludiamo il webhook Stripe dal CSRF
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Qui puoi personalizzare la gestione eccezioni (opzionale)
    })
    ->create();
