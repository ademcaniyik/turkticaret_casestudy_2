<?php

namespace App\Database;

class QueryBuilder
{
    private string $table;
    private array $columns = ['*'];
    private array $where = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $groupBy = [];
    private array $params = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = ['AND', $column, $operator, $value];
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $this->where[] = ['OR', $column, $operator, $value];
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = array_fill(0, count($values), '?');
        $this->where[] = [$column, 'IN', '(' . implode(',', $placeholders) . ')'];
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ["INNER JOIN", $table, $first, $operator, $second];
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ["LEFT JOIN", $table, $first, $operator, $second];
        return $this;
    }

    public function toSql(): string
    {
        $query = ["SELECT", implode(", ", $this->columns)];
        $query[] = "FROM {$this->table}";

        foreach ($this->joins as $join) {
            $query[] = "{$join[0]} {$join[1]} ON {$join[2]} {$join[3]} {$join[4]}";
        }

        if (!empty($this->where)) {
            $conditions = [];
            $firstCondition = true;
            foreach ($this->where as $condition) {
                [$type, $column, $operator, $value] = $condition;
                if (!$firstCondition) {
                    $conditions[] = $type;
                }
                if ($operator === 'IN') {
                    $conditions[] = "{$column} {$operator} {$value}";
                } else {
                    $conditions[] = "{$column} {$operator} ?";
                    $this->params[] = $value;
                }
                $firstCondition = false;
            }
            $query[] = "WHERE " . implode(" ", $conditions);
        }

        if (!empty($this->groupBy)) {
            $query[] = "GROUP BY " . implode(", ", $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $orders = [];
            foreach ($this->orderBy as $order) {
                $orders[] = "{$order[0]} {$order[1]}";
            }
            $query[] = "ORDER BY " . implode(", ", $orders);
        }

        if ($this->limit !== null) {
            $query[] = "LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $query[] = "OFFSET {$this->offset}";
        }

        return implode(" ", $query);
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function get(): array
    {
        $sql = $this->toSql();
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $result = $this->limit(1)->get();
        return !empty($result) ? $result[0] : null;
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($values), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(", ", $columns),
            implode(", ", $placeholders)
        );

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($values);

        return (int) Database::getConnection()->lastInsertId();
    }

    public function update(array $data): int
    {
        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
        }

        $values = array_merge($values, $this->params);

        $sql = sprintf(
            "UPDATE %s SET %s",
            $this->table,
            implode(", ", $sets)
        );

        if (!empty($this->where)) {
            $conditions = [];
            foreach ($this->where as $condition) {
                [$column, $operator, $value] = $condition;
                $conditions[] = "{$column} {$operator} ?";
            }
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql = sprintf("DELETE FROM %s", $this->table);

        if (!empty($this->where)) {
            $conditions = [];
            foreach ($this->where as $condition) {
                [$column, $operator, $value] = $condition;
                $conditions[] = "{$column} {$operator} ?";
            }
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($this->params);

        return $stmt->rowCount();
    }

    public function count(): int
    {
        $originalColumns = $this->columns;
        $this->columns = ['COUNT(*) as count'];
        $result = $this->get();
        $this->columns = $originalColumns;
        return (int) $result[0]['count'];
    }
}