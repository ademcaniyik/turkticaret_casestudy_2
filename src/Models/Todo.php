<?php

namespace App\Models;

class Todo extends Model
{
    protected static string $table = 'todos';

    protected array $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'deleted_at'
    ];

    public function getCategories(): array
    {
        $query = Category::query()
            ->select(['categories.*'])
            ->join('todo_category', 'categories.id', '=', 'todo_category.category_id')
            ->where('todo_category.todo_id', '=', $this->id);

        $results = $query->get();
        $arrayResults = array_map('get_object_vars', $results->toArray());
        return array_map(fn($item) => new Category($item), $arrayResults);
    }

    public function setCategories(array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $query = new QueryBuilder('todo_category');
        $query->where('todo_id', '=', $this->id)->delete();

        foreach ($categoryIds as $categoryId) {
            $query->insert([
                'todo_id' => $this->id,
                'category_id' => $categoryId
            ]);
        }
    }

    public static function search(string $term): array
    {
        $query = static::query()
            ->where('title', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%");

        $results = $query->get();
        $arrayResults = array_map('get_object_vars', $results->toArray());
        return array_map(fn($item) => new static($item), $arrayResults);
    }

    public static function all(): array
    {
        $query = static::query()
            ->where('deleted_at', 'IS', null);

        $results = $query->get();
        $arrayResults = array_map('get_object_vars', $results->toArray());
        return array_map(fn($item) => new static($item), $arrayResults);
    }

    public static function getStats(): array
    {
        $query = static::query()
            ->select([
                'status',
                'priority',
                'COUNT(*) as count'
            ])
            ->where('deleted_at', 'IS', null)
            ->groupBy('status', 'priority');

        $results = $query->get();
        $arrayResults = array_map('get_object_vars', $results->toArray());
        return array_map(fn($item) => new static($item), $arrayResults);
    }
}
