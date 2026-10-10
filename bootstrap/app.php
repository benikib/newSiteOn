<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Http\Middleware\EtablissementMiddleware;
use App\Http\Middleware\FrenchErrorLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(FrenchErrorLocale::class);

            $middleware->alias([
        'admins' => AdminMiddleware::class,
        'etablissements' => EtablissementMiddleware::class,
    ]);
    })




    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            $previousLocale = app()->getLocale();
            app()->setLocale('fr');

            try {
                if ($exception->response) {
                    return $exception->response;
                }

                $errors = $exception->errors();
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => collect($errors)->flatten()->first() ?? 'Certaines informations sont invalides.',
                        'errors' => $errors,
                    ], $exception->status);
                }

                return redirect($exception->redirectTo ?? url()->previous())
                    ->withInput($request->except(['current_password', 'password', 'password_confirmation']))
                    ->withErrors($errors, $request->input('_error_bag', $exception->errorBag));
            } finally {
                app()->setLocale($previousLocale);
            }
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($exception instanceof ValidationException || $exception instanceof AuthenticationException) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $message = match ($status) {
                403 => 'Vous n’avez pas l’autorisation d’accéder à cette page.',
                404 => 'La page demandée est introuvable.',
                419 => 'Votre session a expiré. Rechargez la page puis réessayez.',
                429 => 'Trop de demandes ont été envoyées. Veuillez patienter puis réessayer.',
                default => 'Une erreur est survenue. Veuillez réessayer plus tard.',
            };

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], $status);
            }

            return response()->view('errors.generic', compact('message', 'status'), $status);
        });
    })->create();
