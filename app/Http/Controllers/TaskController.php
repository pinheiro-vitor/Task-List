<?php

namespace App\Http\Controllers;

use App\Models\Task;
use League\Csv\Writer;
use League\Csv\Header;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

    $tasks = $query->get();

    return Inertia::render('Tasks', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3|max:1000',
            'completed' => 'boolean',
            'due_date' => 'nullable|date|after:today',
            'due_time' => 'nullable|date_format:H:i',
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
            'due_date' => 'sometimes|date|after:today',
            'due_time' => 'sometimes|date_format:H:i',
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

    public function export()
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['ID', 'Título', 'Status','Descrição', 'Data marcada', 'Hora marcada']);

        $tasks = Task::all();
        foreach ($tasks as $task) {
            $csv->insertOne([
                $task->id,
                $task->title,
                $task->description,
                $task->completed ? 'Concluída' : 'Pendente',
                $task->due_date,
                $task->due_time,
            ]);
        }

        $response = new StreamedResponse(function() use ($csv) {
            $csv->output('tasks.csv');
        });

        $response->headers->set('Content-Type', 'text/csv');
        return $response;
    }
}
