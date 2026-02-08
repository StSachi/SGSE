<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->route('home')
                ->with('error', 'A sessão expirou. Por favor, tente novamente.');
        });
    }

    public function render($request, Throwable $e)
    {
        if (! $request->expectsJson() && $e instanceof NotFoundHttpException) {
            return redirect()->route('home')
                ->with('error', 'A página que tentou aceder não existe.');
        }

        if (
            ! $request->expectsJson() &&
            $e instanceof MethodNotAllowedHttpException &&
            $request->path() === 'logout'
        ) {
            return redirect()->route('home')
                ->with('error', 'Ação inválida. Para terminar a sessão, use o botão "Sair".');
        }

        return parent::render($request, $e);
    }
}
