<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(\Illuminate\Validation\ValidationException $e) {
            return jsonResponse(data:[],status: Response::HTTP_UNPROCESSABLE_ENTITY,message: $e->getMessage(),errors: $e->errors());

        });
        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return jsonResponse(data:[],status: Response::HTTP_NOT_FOUND,message: $e->getMessage());
        });

        $exceptions->render(function(\Symfony\Component\HttpFoundation\Exception\BadRequestException $e) {
            return jsonResponse(data:[],status: Response::HTTP_BAD_REQUEST,message: $e->getMessage());
        });

        $exceptions->render(function(Throwable $e) {
            return jsonResponse(data:[],status: Response::HTTP_INTERNAL_SERVER_ERROR,message: $e->getMessage());
        });
    })->create();
