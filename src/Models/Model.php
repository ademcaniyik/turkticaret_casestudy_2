<?php

namespace App\Models;

use App\Database\QueryBuilder;

abstract class Model
{
    protected static string $table;
    protected array $attributes = [];
    protected array $original = [];
    protected array $fillable = [];

    public function __construct(?array $attributes = [])
    {
        if (is_object($attributes)) {
            $attributes = (array) $attributes;
        }
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
            $result = $query->where('id', '=', $this->attributes['id'])->update($this->attributes);
        } else {
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

        $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
        return $this->save();
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
        $query = static::query();
        $results = $query->get();
        $arrayResults = array_map('get_object_vars', $results->toArray());
        return array_map(fn($item) => new static($item), $arrayResults);
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function paginate(int $page = 1, int $perPage = 10, array $filters = [], ?string $sortBy = null, ?string $sortOrder = 'ASC'): array
    {
        $query = static::query();

        foreach ($filters as $column => $value) {
            if ($value !== null && $value !== '') {
                $query->where($column, '=', $value);
            }
        }

        if ($sortBy) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $total = $query->count();

        $query->limit($perPage)
              ->offset(($page - 1) * $perPage);

        $items = array_map(fn($item) => new static($item), $query->get()->toArray());

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
