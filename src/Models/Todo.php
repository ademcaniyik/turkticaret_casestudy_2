<?php

namespace App\Models;

use App\Database\QueryBuilder;

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

        return array_map(fn($item) => new Category($item), $query->get());
    }

    public function setCategories(array $categoryIds): void
    {
        // Önce mevcut ilişkileri silelim
        $query = new QueryBuilder('todo_category');
        $query->where('todo_id', '=', $this->id)->delete();

        // Yeni ilişkileri ekleyelim
        foreach ($categoryIds as $categoryId) {
            $query = new QueryBuilder('todo_category');
            $query->insert([
                'todo_id' => $this->id,
                'category_id' => $categoryId
            ]);
        }
    }

    public function delete(): bool
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }

        $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
        return $this->save();
    }

    public static function search(string $term): array
    {
        $query = self::query()
            ->where('deleted_at', 'IS', 'NULL')
            ->where('title', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%");

        return array_map(fn($item) => new static($item), $query->get());
    }

    public static function all(): array
    {
        $results = static::query()
            ->where('deleted_at', 'IS', 'NULL')
            ->get();
        return array_map(fn($item) => new static($item), $results);
    }

    public static function getStats(): array
    {
        $stats = [
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'total' => 0,
            'overdue' => 0
        ];

        // Status dağılımını al
        $query = self::query()
            ->select(['status', 'COUNT(*) as count'])
            ->groupBy('status');

        foreach ($query->get() as $row) {
            $stats[$row['status']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }

        // Gecikmiş todo'ları say
        $stats['overdue'] = self::query()
            ->where('due_date', '<', date('Y-m-d H:i:s'))
            ->where('status', 'NOT IN', ['completed', 'cancelled'])
            ->count();

        return $stats;
    }
}