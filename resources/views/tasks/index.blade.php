@extends('layouts.app')

@section('content')
<div class="mx-auto p-6 flex flex-col gap-3">
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex gap-2 items-center">
        <form id="project-form" class="flex gap-4">
            <select name="project_id" id="project-input" class="border rounded px-2 py-1">
                <option value="">All Projects</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </form>
        <form method="POST" action="{{ route('tasks.store') }}" class="flex gap-2">
            @csrf
            <input type="hidden" name="project_id" id="task-project-id" value="{{ request('project_id') }}">
            <input type="text" name="name" placeholder="Name of task" class="border rounded px-2 py-1 w-full" required>
            <button type="submit" class="bg-blue-600 flex text-white px-4 py-1 rounded">Add</button>
        </form>
    </div>

    <ul id="task-list" class="space-y-2">
        @foreach ($tasks as $task)
            <li data-id="{{ $task->id }}" class="bg-sky-200 hover:pointer rounded-md p-2 flex justify-between items-center">
                <span class="task-name">{{ $task->name }}</span>
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="hidden edit-form">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $task->name }}" class="border rounded px-2 py-1 w-full">
                    <div class="mt-2 flex gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Save</button>
                        <button type="button" class="cancel-edit text-gray-500 px-4 py-1">Cancel</button>
                    </div>
                </form>
                <div class="flex gap-2">
                    <button class="edit-button text-blue-500">Edit</button>
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500">Remove</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>

    @if($tasks->isEmpty())
        <div>No tasks found</div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    // Handle Reordering
    const taskList = document.getElementById('task-list');
    new Sortable(taskList, {
        onEnd: function () {
            const tasks = Array.from(taskList.children).map((item, index) => ({
                id: item.dataset.id,
                priority: index + 1
            }));
            const projectId = document.getElementById('task-project-id').value;

            fetch('{{ route("tasks.reorder") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ tasks, project_id: projectId}),
            });
        },
    });

    // Handle project change
    document.getElementById('project-input').addEventListener('change', function () {
        document.getElementById('project-form').submit();
    });

    // Edit Feature
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function () {
            const listItem = this.closest('li');
            const taskName = listItem.querySelector('.task-name');
            const editForm = listItem.querySelector('.edit-form');
            taskName.classList.add('hidden');
            editForm.classList.remove('hidden');
        });
    });

    document.querySelectorAll('.cancel-edit').forEach(button => {
        button.addEventListener('click', function () {
            const listItem = this.closest('li');
            const taskName = listItem.querySelector('.task-name');
            const editForm = listItem.querySelector('.edit-form');
            taskName.classList.remove('hidden');
            editForm.classList.add('hidden');
        });
    });
</script>
@endsection
