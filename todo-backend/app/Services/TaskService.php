<?php

namespace App\Services;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected TaskRepositoryInterface $taskRepo;

    public function __construct(TaskRepositoryInterface $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    /**
     * Lister toutes les tâches de l'utilisateur connecté
     */
    public function listTasks()
    {
        return $this->taskRepo->getAllForUser(Auth::id());
    }

    /**
     * Afficher une tâche si elle appartient à l'utilisateur
     */
    public function showTask(int $id): ?Task
    {
        $task = $this->taskRepo->getById($id);
        if ($task && $task->user_id === Auth::id()) {
            return $task;
        }
        return null;
    }

    /**
     * Créer une nouvelle tâche pour l'utilisateur connecté
     */
    public function createTask(array $data): Task
    {
        $data['user_id'] = Auth::id();
        return $this->taskRepo->create($data);
    }

    /**
     * Mettre à jour une tâche si elle appartient à l'utilisateur
     */
    public function updateTask(int $id, array $data): ?Task
    {
        $task = $this->showTask($id);
        if ($task) {
            return $this->taskRepo->update($task, $data);
        }
        return null;
    }

    /**
     * Supprimer une tâche si elle appartient à l'utilisateur
     */
    public function deleteTask(int $id): bool
    {
        $task = $this->showTask($id);
        if ($task) {
            return $this->taskRepo->delete($task);
        }
        return false;
    }
}
