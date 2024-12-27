<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;

class TaskController extends Controller
{
    /**
     * Display a list of tasks, optionally filtered by project.
     *
     * @param ListTaskRequest $request
     * @return View
     */
    public function index(ListTaskRequest $request): View
    {
        $projectId = $request->input('project_id');

        $projects = Project::get();

        $tasks = Task::query()
            ->when($projectId, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Store a new task and assign it the next highest priority.
     *
     * @param StoreTaskRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $maxPriority = Task::max('priority') ?? 0;

        Task::create([
            'name' => $request->name,
            'priority' => $maxPriority + 1,
            'project_id' => $request->project_id,
        ]);

        return redirect()->back();
    }

    /**
     * Update the name of a specific task.
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update(['name' => $request->name]);

        return redirect()->back();
    }

    /**
     * Delete a specific task.
     *
     * @param Task $task
     * @return RedirectResponse
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->back();
    }

    /**
     * Reorder tasks based on the provided priority array.
     *
     * @param ReorderTaskRequest $request
     * @return JsonResponse
     */
    public function reorder(ReorderTaskRequest $request): JsonResponse
    {
        $tasks = $request->tasks;

        foreach ($tasks as $priority => $id) {
            Task::query()
                ->where('project_id', $request->project_id)
                ->where('id', $id)
                ->update(['priority' => $priority + 1]);
        }

        return response()->json(['success' => true]);
    }
}
