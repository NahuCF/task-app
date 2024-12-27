<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(ListTaskRequest $request)
    {
        $projectId = $request->input('project_id');

        $projects = Project::get();

        $tasks = Task::query()
            ->when($projectId, fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function store(StoreTaskRequest $request)
    {
        $maxPriority = Task::max('priority') ?? 0;

        Task::create([
            'name' => $request->name,
            'priority' => $maxPriority + 1,
            'project_id' => $request->project_id,
        ]);

        return redirect()->back();
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update(['name' => $request->name]);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back();
    }

    public function reorder(ReorderTaskRequest $request)
    {
        $tasks = $request->tasks;

        foreach ($tasks as $priority => $id) {
            Task::query()
                ->where('project_id', $request->project_id)
                ->where('id', $id)->update(['priority' => $priority + 1]);
        }

        return response()->json(['success' => true]);
    }
}
