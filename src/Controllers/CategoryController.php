<?php

namespace App\Controllers;  

use App\Helpers\Response;
use App\Models\Category;
use Rakit\Validation\Validator;

class CategoryController
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator;
    }

    public function index(): void
    {
        $categories = Category::getWithTodoCount();
        Response::json($categories);
    }

    public function show(array $params): void
    {
        $category = Category::find($params['id']);
        if (!$category) {
            Response::json(null, 'error', 'Category not found', 404);
            return;
        }

        $category->todos = $category->getTodos();
        Response::json($category);
    }

    public function store(array $data): void
    {
        $validation = $this->validator->make($data, [
            'name' => 'required|min:3',
            'description' => 'required|min:10',
            'color' => 'required|regex:/^#[0-9a-fA-F]{6}$/'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json($validation->errors()->all(), 'error', 'Validation failed', 422);
            return;
        }

        $category = Category::create($data);
        Response::json($category, 'success', 'Category created successfully', 201);
    }

    public function update(array $params, array $data): void
    {
        $category = Category::find($params['id']);
        if (!$category) {
            Response::json(null, 'error', 'Category not found', 404);
            return;
        }

        $validation = $this->validator->make($data, [
            'name' => 'min:3',
            'description' => 'min:10',
            'color' => 'regex:/^#[0-9a-fA-F]{6}$/'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            Response::json($validation->errors()->all(), 'error', 'Validation failed', 422);
            return;
        }

        $category->fill($data);
        $category->save();

        Response::json($category, 'success', 'Category updated successfully');
    }

    public function destroy(array $params): void
    {
        $category = Category::find($params['id']);
        if (!$category) {
            Response::json(null, 'error', 'Category not found', 404);
            return;
        }

        $category->delete();
        Response::json(null, 'success', 'Category deleted successfully');
    }

    public function todos(array $params): void
    {
        $category = Category::find($params['id']);
        if (!$category) {
            Response::json(null, 'error', 'Category not found', 404);
            return;
        }

        $todos = $category->getTodos();
        Response::json($todos);
    }
}