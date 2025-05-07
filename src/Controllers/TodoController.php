<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\Todo;
use App\Models\Category;
use Rakit\Validation\Validator;
use Rakit\Validation\Validation;

class TodoController
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator;
    }

    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = min((int) ($_GET['limit'] ?? 10), 50); // maksimum 50 kayıt
        $sort = $_GET['sort'] ?? 'created_at';
        $order = strtoupper($_GET['order'] ?? 'DESC');
        $status = $_GET['status'] ?? null;
        $priority = $_GET['priority'] ?? null;

        // Geçerli sıralama alanlarını kontrol et
        $allowedSortFields = ['created_at', 'due_date', 'priority', 'status'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }

        // Geçerli sıralama yönünü kontrol et
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        $filters = array_filter([
            'status' => $status,
            'priority' => $priority
        ]);

        $result = Todo::paginate($page, $limit, $filters, $sort, $order);
        Response::json($result['data'], 'success', null, 200, $result['meta']);
    }

    public function show(array $params): void
    {
        $todo = Todo::find($params['id']);
        if (!$todo) {
            Response::json(null, 'error', 'Todo not found', 404);
            return;
        }

        $todo->categories = $todo->getCategories();
        Response::json($todo);
    }

    public function store(array $data): void
    {
        $validation = $this->validator->make($data, [
            'title' => 'required|min:3',
            'description' => 'required|min:10',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
            'category_ids' => 'array',
            'category_ids.*' => 'integer'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json($validation->errors()->all(), 'error', 'Validation failed', 422);
            return;
        }

        $todo = Todo::create($data);

        if (isset($data['category_ids'])) {
            $todo->setCategories($data['category_ids']);
        }

        Response::json($todo, 'success', 'Todo created successfully', 201);
    }

    public function update(array $params, array $data): void
    {
        $todo = Todo::find($params['id']);
        if (!$todo) {
            Response::json(null, 'error', 'Todo not found', 404);
            return;
        }

        $validation = $this->validator->make($data, [
            'title' => 'min:3',
            'description' => 'min:10',
            'status' => 'in:pending,in_progress,completed,cancelled',
            'priority' => 'in:low,medium,high',
            'due_date' => 'date',
            'category_ids' => 'array',
            'category_ids.*' => 'integer'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json($validation->errors()->all(), 'error', 'Validation failed', 422);
            return;
        }

        $todo->fill($data);
        $todo->save();

        if (isset($data['category_ids'])) {
            $todo->setCategories($data['category_ids']);
        }

        Response::json($todo, 'success', 'Todo updated successfully');
    }

    public function destroy(array $params): void
    {
        $todo = Todo::find($params['id']);
        if (!$todo) {
            Response::json(null, 'error', 'Todo not found', 404);
            return;
        }

        $todo->delete();
        Response::json(null, 'success', 'Todo deleted successfully');
    }

    public function search(array $params): void
    {
        if (!isset($params['q']) || empty($params['q'])) {
            Response::json(null, 'error', 'Search term is required', 422);
            return;
        }

        $todos = Todo::search($params['q']);
        Response::json($todos);
    }

    public function stats(): void
    {
        $stats = Todo::getStats();
        Response::json($stats);
    }

    public function updateStatus(array $params, array $data): void
    {
        $todo = Todo::find($params['id']);
        if (!$todo) {
            Response::json(null, 'error', 'Todo not found', 404);
            return;
        }

        $validation = $this->validator->make($data, [
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json($validation->errors()->all(), 'error', 'Validation failed', 422);
            return;
        }

        $todo->status = $data['status'];
        $todo->save();

        Response::json([
            'id' => $todo->id,
            'status' => $todo->status,
            'updated_at' => $todo->updated_at
        ], 'success', 'Todo status updated successfully');
    }
}