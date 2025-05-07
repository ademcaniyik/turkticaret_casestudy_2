<?php

namespace App\Models;

use App\Database\QueryBuilder;

abstract class Model
{
    protected static string $table;
    protected array $attributes = [];
    protected array $original = [];
    protected array $fillable = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, $this->fillable)) {
            $this->attributes[$name] = $value;
        }
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    public function save(): bool
    {
        $query = new QueryBuilder(static::$table);

        if (isset($this->attributes['id'])) {
            // Update
            $result = $query->where('id', '=', $this->attributes['id'])->update($this->attributes);
        } else {
            // Insert
            $id = $query->insert($this->attributes);
            if ($id) {
                $this->attributes['id'] = $id;
                $result = true;
            } else {
                $result = false;
            }
        }

        if ($result) {
            $this->original = $this->attributes;
        }

        return $result;
    }

    public function delete(): bool
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }

        $query = new QueryBuilder(static::$table);
        return (bool) $query->where('id', '=', $this->attributes['id'])->delete();
    }

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::$table);
    }

    public static function find(int $id): ?static
    {
        $result = static::query()->where('id', '=', $id)->first();
        return $result ? new static($result) : null;
    }

    public static function all(): array
    {
        $results = static::query()->get();
        return array_map(fn($item) => new static($item), $results);
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function paginate(int $page = 1, int $perPage = 10, array $filters = [], string $sortBy = null, string $sortOrder = 'ASC'): array
    {
        $query = static::query();

        // Filtreleri uygula
        foreach ($filters as $column => $value) {
            if ($value !== null && $value !== '') {
                $query->where($column, '=', $value);
            }
        }

        // Sıralama uygula
        if ($sortBy) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Toplam kayıt sayısını al
        $total = $query->count();

        // Sayfalama uygula
        $query->limit($perPage)
              ->offset(($page - 1) * $perPage);

        // Sonuçları al
        $items = array_map(fn($item) => new static($item), $query->get());

        return [
            'data' => $items,
            'meta' => [
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ]
            ]
        ];
    }
}
