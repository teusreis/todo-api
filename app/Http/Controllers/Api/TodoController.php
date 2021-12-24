<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $availableOrderBy = ['title', 'description', 'expiration-asc', 'expiration-desc', 'created_at-asc', 'created_at-desc'];

        $query = Todo::query();

        $query->where("user_id", auth()->id());

        if ($request->title) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        if ($request->description) {
            $query->where('description', 'like', "%{$request->description}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->orderby && in_array($request->orderby, $availableOrderBy)) {

            $orderDirection = 'asc';

            if (str_contains($request->orderby, '-')) {
                $orderByArray = explode('-', $request->orderby);
                $orderBy = $orderByArray[0];

                if ($orderByArray[1] === 'asc' || $orderByArray[1] === 'desc') {
                    $orderDirection = $orderByArray[1];
                }
            }

            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->paginate(10);
    }

    public function store(StoreTodoRequest $request)
    {
        $data = $request->validated();

        $data["user_id"] = auth()->id();

        $todo = Todo::create($data);

        if (!$todo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Todo couldn\'t be created!'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Todo created successfully!',
            'data' => $todo
        ], 201);
    }

    public function show(Todo $todo)
    {
        $this->authorize("view", $todo);

        return $todo;
    }

    public function update(StoreTodoRequest $request, Todo $todo)
    {
        $this->authorize("update", $todo);

        $data = $request->validated();

        $todo->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully!',
            'data' => $todo
        ]);
    }

    public function toggle(Todo $todo)
    {
        $this->authorize("update", $todo);

        $newStatus = $todo->status === 'completed'
            ? 'undone'
            : 'completed';

        $todo->update(['status' => $newStatus]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Todo updated successfully!',
            'data' => $todo
        ]);
    }
}
