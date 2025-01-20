<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request):JsonResponse
    {

        Gate::authorize('viewAny', User::class);

        $search = $request->query('search');

        $query = User::query();

        if ($search) {
            $query->where('email', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('dni', 'like', "%{$search}%");
        }

        $users = $query->get();

        return jsonResponse(
            data: UserResource::collection($users),
            message: "",
            status: Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request):JsonResponse
    {
        Gate::authorize('create', User::class);

        return  transactional(function () use ($request){
            $user = User::create($request->validated());
            $user->assignRole($request->validated()['roles']);
            return jsonResponse(data:['user'=>UserResource::make($user)],status:Response::HTTP_CREATED,message:'Usuario creado con exito');
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user):JsonResponse
    {
        Gate::authorize('view', $user);
        return jsonResponse(data:UserResource::make($user),message:"",status:Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user):JsonResponse
    {
        Gate::authorize('update', $user);
        $user->update($request->validated());
        if(isset($request->validated()['roles'])){
            $user->syncRoles($request->validated()['roles']);
        }
        return jsonResponse(data:['user'=>UserResource::make($user)],message:"Usuario Actualizado",status:Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user):JsonResponse
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return jsonResponse(data:[],message:"Usuario eliminado",status:Response::HTTP_NO_CONTENT);
    }
}
