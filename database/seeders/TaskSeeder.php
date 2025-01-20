<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tasks = [
            [
                "title" => "Implementar autenticación",
                "description" => "Agregar autenticación con JWT en la API.",
                "status" => Task::STATUSES[1],
                "priority" => Task::PRIORITIES[0],
            ],
            [
                "title" => "Refactorizar código",
                "description" => "Mejorar la estructura del código en los controladores.",
                "status" => Task::STATUSES[0],
                "priority" => Task::PRIORITIES[1],
            ],
            [
                "title" => "Optimizar consultas SQL",
                "description" => "Reducir el tiempo de respuesta en las queries complejas.",
                "status" => Task::STATUSES[0],
                "priority" => Task::PRIORITIES[2],
            ],
            [
                "title" => "Documentar API",
                "description" => "Generar documentación para los endpoints de la API con Swagger.",
                "status" => Task::STATUSES[2],
                "priority" => Task::PRIORITIES[2],
            ],
            [
                "title" => "Revisión de seguridad",
                "description" => "Analizar posibles vulnerabilidades en el sistema.",
                "status" => Task::STATUSES[1],
                "priority" => Task::PRIORITIES[0],
            ],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::create($taskData);
            $users = User::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $task->users()->attach($users);
        }


        $task = Task::create([
            "title" => "Configurar entorno de producción",
            "description" => "Configurar servidor y base de datos para producción.",
            "status" => Task::STATUSES[0],
            "priority" => Task::PRIORITIES[1],
        ]);
        $task->users()->attach(1);

    }
}
