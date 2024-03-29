<?php


use interfaces\SqlQueryBuilder;

class SqlBuilder implements SqlQueryBuilder
{
    /**
     * @var object
     */
    protected object $query;

    /**
     * @return void
     */
    protected function reset(): void
    {
        $this->query = new stdClass();
    }

    /**
     * @param string $table
     * @param array $fields
     * @return SqlQueryBuilder
     */
    public function insert(string $table, array $fields): SqlQueryBuilder
    {
        $this->reset();
        $values = $this->valuesHelper($fields);
        $this->query->base = "INSERT INTO " . $table . ' (' . implode(", ", $fields) . ")" . " VALUES " . ' (' . $values . ")";
        return $this;
    }

    /**
     * @param string $table
     * @param array $fields
     * @return SqlQueryBuilder
     */
    public function select(string $table, array $fields): SqlQueryBuilder
    {
        $this->reset();
        $this->query->base = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @param bool $withoutPrepearing
     * @return SqlQueryBuilder
     * @throws Exception
     */
    public function where(string $field, mixed $value, string $operator, bool $withoutPrepearing = false): SqlQueryBuilder
    {
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new Exception("WHERE can only be added to SELECT, UPDATE OR DELETE");
        }
        if ($withoutPrepearing) {
            if ($operator) {
                $this->query->where[] = "$field $operator " . "'" . "$value" . "'";
            } else  $this->query->where[] = "$field $value";

            return $this;
        }
        $this->query->where[] = "$field $operator" . "?";
        return $this;
    }

    /**
     * @param string $table
     * @param array $keys
     * @return SqlQueryBuilder
     */
    public function update(string $table, array $keys): SqlQueryBuilder
    {
        $this->reset();
        $values = $this->updateHelper($keys);
        $this->query->base = "UPDATE " . $table . " SET " . "updated_at=CURRENT_TIMESTAMP, " . $values;
        $this->query->type = 'update';
        return $this;
    }

    /**
     * @param string $table
     * @param bool|null $softDelete
     * @return SqlQueryBuilder
     */
    public function delete(string $table, ?bool $softDelete): SqlQueryBuilder
    {
        $this->reset();
        if ($softDelete) {
            $this->query->base = "UPDATE " . $table . " SET " . "updated_at=CURRENT_TIMESTAMP, " . "deleted_at = CURRENT_TIMESTAMP";
        } else {
            $this->query->base = "DELETE FROM " . $table;
        }
        $this->query->type = 'delete';

        return $this;
    }

    /**
     * @param int $start
     * @param int $offset
     * @return SqlQueryBuilder
     * @throws Exception
     */
    public function limit(int $start, int $offset): SqlQueryBuilder
    {
        if ($this->query->type != 'select') {
            throw new Exception("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    /**
     * @return string
     */
    public function getSQL(): string
    {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        if (isset($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= ";";
        return $sql;
    }

    /**
     * @param array|string $values
     * @return string
     */
    private function valuesHelper(array|string $values): string
    {
        $result = '';
        $symbol = "?";
        if (is_array($values)) {
            $count = count($values);
        } else $count = 1;
        for ($i = 0; $i < $count; $i++) {
            if ($i === $count -1) {
                $result .= $symbol;
            } else $result .= $symbol . ",";
        }
        return $result;
    }

    /**
     * @param array $keys
     * @return string
     */
    private function updateHelper(array $keys): string
    {
        $result = [];
        foreach ($keys as $value) {
            $result[] = $value . " = ?";
        }
        return implode(',', $result);
    }
}