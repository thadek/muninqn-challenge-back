<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Requests\MarkTaskCompletedRequest;
use App\Models\Task;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{


    public function index(Request $request)
    {
        Gate::authorize('viewAny', Task::class);


        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'only_assigned' => ['nullable', 'boolean'], // Validar el parámetro como booleano
        ]);

        $user = auth()->user();
        $query = Task::with('users');


        if (!empty($validated['status'])) {
            $statuses = explode(',', $validated['status']);
            $query->whereIn('status', $statuses);
        }

        // Si el usuario es ADMIN, decide si ve todas las tareas o solo las asignadas
        if ($user->hasRole(Roles::ADMIN->value)) {
            if ($request->boolean('only_assigned')) {
                // Solo las tareas asignadas al admin
                $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
            }
        } else {
            // Usuarios normales: solo sus tareas asignadas
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        return jsonResponse(data: $query->get(), message: '', status: Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        Gate::authorize('create', Task::class);
        $task = Task::create($request->validated());

        if($request->has('users')){
            $task->users()->sync($request->input('users'));
        }

        $task->load('users');

        return jsonResponse(data:['task'=>TaskResource::make($task)] , message: 'Task created successfully', status: Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {   $task->load('users');
        Gate::authorize('view', $task);

        if(auth()->user()->hasRole(Roles::ADMIN->value)){
            return jsonResponse(data:['task'=>TaskResource::make($task)] , message: '', status: Response::HTTP_OK);
        }else {
            return jsonResponse(data:$task , message: '', status: Response::HTTP_OK);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $data = $request->validated();
        $task->load('users');

        if(isset($data['status']) && $data['status'] === 'completada'){
            $allUsersCompleted = $task->users->every(function ($user) {
                return $user->pivot->is_finished;
            });

            if (!$allUsersCompleted) {
                return jsonResponse(data:[],message:"Solo se puede pasar una tarea a completada si todos sus usuarios han marcado como completa su parte.",status:Response::HTTP_CONFLICT);
            }
        }


        //Caso de uso en el que una tarea finalizada vuelve para atrás.
        if (isset($data['status']) && $data['status'] === Task::STATUSES[0] && $task->status === Task::STATUSES[2]) {
            // Actualizo todos los pivots is_finished a false
            $task->users()->updateExistingPivot($task->users->pluck('id')->toArray(), [
                'is_finished' => false,
            ]);
        }

        $task->update($data);

        if($request->has('users') && auth()->user()->hasRole(Roles::ADMIN->value)){
            $task->users()->sync($request->input('users'));
        }
        $task->load('users');

       return jsonResponse(data:['task'=>TaskResource::make($task)] , message: 'Task updated successfully', status: Response::HTTP_OK);
    }


    /**
     * @param MarkTaskCompletedRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     * Metodo para marcar como completada la parte de un usuario asignado a la tarea
     */
    public function markAsCompleted(MarkTaskCompletedRequest $request, Task $task)
    {

        $user = auth()->user();

        // Verifico si el usuario está asignado a la tarea
        $isAssigned = $task->users()->where('users.id', $user->id)->exists();
        if (!$isAssigned ) {
            return jsonResponse(data:[],message:"No estas autorizado a completar tu parte de esta tarea.",status:Response::HTTP_FORBIDDEN);
        }



        // Verifico si alguno de los usuarios aún no ha marcado la tarea como completada
        $someUsersPending = $task->users->contains(function ($user) {
            return !$user->pivot->is_finished;
        });

        // Si la tarea está pendiente y hay usuarios que faltan confirmar, se cambia a "en curso"
        if ($task->status === Task::STATUSES[0] && $someUsersPending) {
            $task->update(['status' => Task::STATUSES[1]]);
        }



        // Actualizo el atributo is_completed en la tabla intermedia
        $task->users()->updateExistingPivot($user->id, [
            'is_finished' => $request->validated()['is_finished'],
        ]);

        return jsonResponse(data:[],message:"Actualizado correctamente",status:Response::HTTP_OK);
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return jsonResponse(data:[],message:'Tarea eliminada correctamente.',status:Response::HTTP_OK);

    }






}
