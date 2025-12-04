<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->orderByDesc('created_at')
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Task::class);

        return view('tasks.create');
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Task::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $request->user()->id,
            'organization_id' => $request->user()->organization_id,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
