<?php

namespace App\Models;

class Category extends Model
{
    protected static string $table = 'categories';

    protected array $fillable = [
        'name',
        'description',
        'color',
        'deleted_at'
    ];

    public function getTodos(): array
    {
        $query = Todo::query()
            ->select(['todos.*'])
            ->join('todo_category', 'todos.id', '=', 'todo_category.todo_id')
            ->where('todo_category.category_id', '=', $this->id);

        return array_map(fn($item) => new Todo($item), $query->get());
    }

    public static function getWithTodoCount(): array
    {
        $query = self::query()
            ->select([
                'categories.*',
                '(SELECT COUNT(*) FROM todo_category WHERE todo_category.category_id = categories.id) as todo_count'
            ]);

        return array_map(fn($item) => new static($item), $query->get());
    }
}