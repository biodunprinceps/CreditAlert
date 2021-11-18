<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json(['status' => 'error',
                'message' => $exception->getMessage(),
            ], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            if ($request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 401);
            }
        }
        if ($exception instanceof RouteNotFoundException) {
            if ($request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 404);
            }
        }
        if ($exception instanceof ModelNotFoundException) {
            if ($request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 404);
            }
        }
        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => 'Kindly login to continue'], 401);
            }
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*')) {
                return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 400);
            }
        }
        $trace_array = 'dd';
        $error_line = $exception->getLine();
        $error_file = $exception->getFile();
        $error_message = $exception->getMessage();
        $trace_string = $exception->__toString();
        return parent::render($request, $exception);
    }
}
