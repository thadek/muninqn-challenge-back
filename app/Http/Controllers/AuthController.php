<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Devuelvo un JWT a partir de las credenciales recibidas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $request->validate(['identifier' => 'required|string', 'password' => 'required|min:8']);

        $credentials = filter_var($request->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->identifier]
            : ['dni' => $request->identifier];

        $credentials['password'] = $request->password;

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(["error"=>'Credenciales incorrectas',"status"=> Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
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
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => UserResource::make(auth()->user())

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
        return jsonResponse(data:UserResource::make(auth()->user()),message:'',status: Response::HTTP_OK);
    }


    /**
     * Registrar un usuario a traves de una transacción a la DB.
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request):JsonResponse
    {

        return  transactional(function () use ($request){
           $user = User::create($request->validated());
           $user->assignRole(Roles::USER);
           return jsonResponse(data:['user'=>UserResource::make($user)],status:Response::HTTP_CREATED,message:'Usuario creado con exito');
       });

    }
}
