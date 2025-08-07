<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        // Middleware d'authentification (si ce n'est pas déjà global)
        $this->middleware('auth:sanctum'); // ou 'auth' selon ta config
    }

    // GET /tasks
    public function index(): JsonResponse
    {
        $tasks = $this->taskService->listTasks();
        return response()->json($tasks);
    }

    // GET /tasks/{id}
    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->showTask($id);

        if (!$task) {
            return response()->json(['message' => 'Tâche non trouvée ou non autorisée'], 404);
        }

        return response()->json($task);
    }

    // POST /tasks
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            // ajoute d'autres règles selon tes champs
        ]);

        $task = $this->taskService->createTask($validated);

        return response()->json($task, 201);
    }

    // PUT /tasks/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            // autres règles
        ]);

        $task = $this->taskService->updateTask($id, $validated);

        if (!$task) {
            return response()->json(['message' => 'Tâche non trouvée ou non autorisée'], 404);
        }

        return response()->json($task);
    }

    // DELETE /tasks/{id}
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->taskService->deleteTask($id);

        if (!$deleted) {
            return response()->json(['message' => 'Tâche non trouvée ou non autorisée'], 404);
        }

        return response()->json(['message' => 'Tâche supprimée avec succès']);
    }
}
