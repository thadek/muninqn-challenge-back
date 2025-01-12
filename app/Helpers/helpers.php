<?php

/**
 * Esta funcion genera una respuesta estandarizada en JSON para ser utilizada en los returns de diferentes controladores
 * @param $data
 * @param $status
 * @param $message
 * @param $errors
 * @return \Illuminate\Http\JsonResponse
 */
function jsonResponse($data = [], $status = 200, $message = 'OK', $errors = []): \Illuminate\Http\JsonResponse
{

    $response = [
        'data' => $data,
        'status' => $status,
        'message' => $message,
    ];


    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    return response()->json($response, $status);
}
