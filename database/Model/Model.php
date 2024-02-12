<?php

class Model
{
    /**
     * @var string
     */
    protected static string $table = '';

    /**
     * @var bool|null
     */
    protected static bool|null $softDelete = null;


    /**
     * @param array $params
     * @return iterable|bool
     */
    public static function all(array $params): iterable|bool
    {
        $table = self::getTable();

        $db = Connector::getInstance();
        $query = new SqlBuilder();
        $sql = $query->select($table, $params)
            ->where('deleted_at', 'IS NULL', "", true)
            ->getSQL();
        $stmt = $db->query($sql);
        return self::generator($stmt);

    }

    /**
     * @param array $array
     * @return void
     * @throws Exception
     */
    public static function create(array $array): void
    {
        if (key_exists('id', $array)) throw new Exception('Key "id" must not be in an array');

        $db = Connector::getInstance();
        $table = self::getTable();

        self::checkColumn($array, $db, $table);  //check if column exist and user provide right type of data

        $query = new SqlBuilder();
        $keys = array_keys($array);
        $values = array_values($array);
        $sql = $query->insert($table, $keys)->getSQL();
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * @param string $column
     * @param string $value
     * @param array $fields
     * @param string $operator
     * @return mixed
     */
    public static function where(string $column, string $value, array $fields =['*'] , string $operator = '='): Generator
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        $query = new SqlBuilder();
        $sql = $query->select($table, $fields)
            ->where($column, $value, $operator)
            ->getSQL();
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $value
        ]);

        return self::generator($stmt);
    }

    /**
     * @param array $array
     * @param string $operator
     * @return void
     * @throws Exception
     */
    public static function update(array $array, string $operator = '='): void
    {
        if (key_exists('id', $array)) {
            $id = $array['id'];  //save id
            unset($array['id']); //remove id field from array for preparing sql request
        } else {
            throw new Exception('Params must contain key "id"');
        }
        $db = Connector::getInstance();
        $table = self::getTable();

        self::checkColumn($array, $db, $table);  //check if column exist and user provide right type of data

        $keys = array_keys($array);
        $values = array_values($array);
        $values[] = $id; //push id value to the end of array for matching with sql request

        self::findId($id, $db, $table); //find if record with such $id exists

        $query = new SqlBuilder();
        $sql = $query->update($table, $keys)
            ->where('id', $id, $operator)
            ->getSQL();
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * @param int $id
     * @param string $operator
     * @return void
     * @throws Exception
     */
    public static function delete(int $id, string $operator = '='): void
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        self::findId($id, $db, $table);
        $softDelete = self::getSoftDelete();
        $query = new SqlBuilder();
        $sql = $query->delete($table, $softDelete)
            ->where('id', $id, $operator)
            ->getSQL();
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $id,
        ]);
    }

    /**
     * @return string
     */
    protected static function getTable(): string
    {
        return static::$table;
    }

    protected static function getSoftDelete(): ?bool
    {
        return static::$softDelete;
    }

    /**
     * @param array $arrayOfKeys
     * @param PDO $database
     * @param string $table
     * @param string $operator
     * @return void
     * @throws Exception
     */
    protected static function checkColumn(array $arrayOfKeys, PDO $database, string $table, string $operator = '='): void
    {
        foreach ($arrayOfKeys as $column => $value) {
            $query = new SqlBuilder();
            $sql = $query->select("INFORMATION_SCHEMA.COLUMNS", ['COLUMN_NAME'])
                ->where("TABLE_NAME", $table, $operator, true)
                ->where("COLUMN_NAME", $column, $operator)
                ->getSQL();
            $stmt = $database->prepare($sql);
            $stmt->execute([
                $column
            ]);
            $obj = $stmt->fetch();
            if (!$obj) {
                throw new Exception("Column '$column' does not exist");
            } else {
                $query = new SqlBuilder();
                $sql = $query->select("INFORMATION_SCHEMA.COLUMNS", ['COLUMN_TYPE'])
                    ->where("TABLE_NAME", $table, $operator, true)
                    ->where("COLUMN_NAME", $column, $operator)
                    ->getSQL();
                $stmt = $database->prepare($sql);
                $stmt->execute([
                    $column
                ]);
                $obj = $stmt->fetch();
                $type = self::typeHelper($obj->COLUMN_TYPE);
                if (gettype($value) !== $type) {
                    throw new Exception("Type of column: '$column' must be $type");
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    protected static function findId(int $id, PDO $database, string $table, string $operator = '='): void
    {
        $query = new SqlBuilder();
        $sql = $query->select($table, ["id"])
            ->where('id', $id, $operator)
            ->getSQL();
        $stmt = $database->prepare($sql);
        $stmt->execute([
            $id
        ]);
        if (!$stmt->fetch()) {
            throw new Exception('There is no such record');
        }
    }

    /**
     * @param string $type
     * @return string
     */
    protected static function typeHelper(string $type): string
    {
        [$firstEntry] = explode(" ", $type);
        if (str_contains($firstEntry, '(')) {
            $offset = strpos($type, '(');
            $firstEntry = substr_replace($type, '', $offset);
        }
        return match ($firstEntry) {
            'char',
            'varchar',
            'nchar',
            'nvarchar',
            'ntext',
            'binary',
            'text',
            'varbinary',
            'image' => 'string',
            'bit',
            'tinyint',
            'smallint',
            'int',
            'bigint', => 'integer',
            'decima',
            'numeric',
            'smallmoney',
            'money',
            'float',
            'real' => 'double',
            'datetime',
            'datetime2',
            'smalldatetime',
            'date',
            'time',
            'datetimeoffset',
            'timestamp', => 'date',
            default => 'other'
        };
    }

    /**
     * @param PDOStatement $array
     * @return Generator
     */
    protected static function generator(PDOStatement $array): Generator
    {
        foreach ($array as $value)
            yield $value;
    }

}