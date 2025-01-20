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

        $exceptions->render(function(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return jsonResponse(data:[],status: Response::HTTP_NOT_FOUND,message: "El recurso solicitado no existe");
        });

        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return jsonResponse(data:[],status: Response::HTTP_NOT_FOUND,message: "El recurso solicitado no existe");
        });



        $exceptions->render(function(\Symfony\Component\HttpFoundation\Exception\BadRequestException $e) {
            return jsonResponse(data:[],status: Response::HTTP_BAD_REQUEST,message: $e->getMessage());
        });

        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            return jsonResponse(data:[],status: Response::HTTP_METHOD_NOT_ALLOWED,message: $e->getMessage());
        });


        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e) {
            return jsonResponse(data:[],status: Response::HTTP_UNAUTHORIZED,message: $e->getMessage());
        });



        $exceptions->render(function(\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {

            return jsonResponse(data:[],status: Response::HTTP_FORBIDDEN,message: "No estas autorizado para visualizar este recurso");
        });

        $exceptions->render(function(\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return jsonResponse(data:[],status: Response::HTTP_UNAUTHORIZED,message: "El token no es valido");
        });

        $exceptions->render(function(Throwable $e) {
            logger()->error($e);
            return jsonResponse(data:[],status: Response::HTTP_INTERNAL_SERVER_ERROR,message:"Ocurrió un error interno en el servidor.");
        });
    })->create();
