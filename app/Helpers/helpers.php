<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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



function transactional(\Closure $callback){
    DB::beginTransaction();
    try{
        $result = $callback();
        DB::commit();
        return $result;
    }catch(\Exception $e){
        DB::rollBack();
        Log::error($e->getMessage());
        return jsonResponse(data:[],status:Response::HTTP_INTERNAL_SERVER_ERROR,message:'Ocurrió un error al procesar la solicitud');
    }
}
