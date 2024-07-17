<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all(); 
        return Inertia::render('Tasks', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3|max:1000',
            'completed' => 'boolean',
        ]);

        $validated['user_id'] = auth()->user()->id;

        Task::create($validated);

        return redirect()->route('tasks')
            ->with('message', 'Task criada com sucesso!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:1000',
            'completed' => 'sometimes|boolean',
        ]);

        $validated['user_id'] = auth()->user()->id;

        $task->update($validated);

        return redirect()->back()->with('message', 'Task atualizada com sucesso!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks')
            ->with('message', 'Task deletada com sucesso!');
    }
}
