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

    public function show(array $params): ?Todo
    {
        $todo = Todo::find($params['id']);
        if (!$todo) {
            Response::json(null, 'error', 'Todo not found', 404);
            return null;
        }

        $categories = $todo->getCategories();
        Response::json(['data' => $todo, 'categories' => $categories]);
        return $todo;
    }

    public function store(array $data): ?Todo
    {
        $validation = $this->validator->make($data, [
            'title' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'due_date' => 'date_format:Y-m-d H:i:s',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json(['errors' => $validation->errors()->all()], 'error', 'Validation failed', 422);
            return null;
        }

        $todo = new Todo($data);
        $todo->save();

        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $todo->setCategories($data['category_ids']);
        }

        Response::json($todo, 'Todo created successfully');
        return $todo;
    }

    public function update(int $id, array $data): ?Todo
    {
        $todo = Todo::find($id);
        if (!$todo) {
            Response::json(null, 'Todo not found', null, 404);
            return null;
        }

        $validation = $this->validator->make($data, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date_format:Y-m-d H:i:s',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json(null, 'Validation failed', $validation->errors()->first(), 422);
            return null;
        }

        $todo->fill($data);
        $todo->save();

        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $todo->setCategories($data['category_ids']);
        }

        Response::json($todo, 'success', 'Todo updated successfully');
        return true;
    }

    public function delete(int $id): bool
    {
        $todo = Todo::find($id);
        if (!$todo) {
            Response::json(null, 'Todo not found', null, 404);
            return false;
        }

        $todo->delete();
        Response::json(null, 'Todo deleted successfully', null, 204);
        return true;
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

    public function stats(): array
    {
        $stats = Todo::getStats();
        Response::json($stats);
        return $stats;
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