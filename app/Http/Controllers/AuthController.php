<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Devuelvo un JWT a partir de las credenciales recibidas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $request->validate(['email' => 'required|email', 'password' => 'required|min:8']);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return jsonResponse(data:[],message:'Credenciales incorrectas',status: Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return jsonResponse(data:[],message:'Sesion cerrada',status: Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh():JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token):JsonResponse
    {
        return jsonResponse(
            [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if(!auth()->user()){
            return jsonResponse(data:[],message:'Usuario no autenticado',status: Response::HTTP_UNAUTHORIZED);
        }
        return jsonResponse(data:auth()->user(),message:'',status: Response::HTTP_OK);
    }


    public function register(RegisterUserRequest $request):JsonResponse
    {

        $user = User::create($request->all());
        return jsonResponse($user,Response::HTTP_CREATED,'Usuario creado con exito');

    }
}
