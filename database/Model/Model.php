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
     * @return iterable|bool
     */
    public static function all(): iterable|bool
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        $sql = "SELECT * FROM $table WHERE deleted_at IS NULL";
        $stmt = $db->query($sql);
        if ($stmt) return self::generator($stmt);
        return $stmt;

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

        $keys = implode(",", array_keys($array));
        $values = array_values($array);

        $sql = self::sqlWhereBuilder(count($array), $table, $keys);
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function where(string $column, mixed $value): mixed
    {
        $db = Connector::getInstance();
        $table = self::getTable();

        $sql = "SELECT * FROM  $table WHERE $column = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $value
        ]);

        $data = $stmt->fetch();
        if (!$data) {
            return false;
        }
        return $data;
    }

    /**
     * @param array $array
     * @return void
     * @throws Exception
     */
    public static function update(array $array): void
    {
        if (key_exists('id', $array) ){
            $id = $array['id'];  //save id
            unset($array['id']); //remove id field from array for preparing sql request
        }else {
            throw new Exception('Params must contain key "id"');
        }
        $db = Connector::getInstance();
        $table = self::getTable();

        self::checkColumn($array, $db, $table);  //check if column exist and user provide right type of data

        $keys = array_keys($array);
        $values = array_values($array);
        $values[] = $id; //push id value to the end of array for matching with sql request

        self::findId($id, $db, $table); //find if record with such $id exists

        $sql = self::sqlUpdateBuilder($keys, $table);
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public static function delete(int $id): void
    {
        $db = Connector::getInstance();
        $table = self::getTable();

        self::findId($id, $db, $table);

        if (self::$softDelete) {
            $sql = "UPDATE $table SET updated_at=CURRENT_TIMESTAMP, deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
        } else {
            $sql = "DELETE FROM $table WHERE id = ?";
        }
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

    /**
     * @param array $arrayOfKeys
     * @param PDO $database
     * @param string $table
     * @return void
     * @throws Exception
     */
    protected static function checkColumn(array $arrayOfKeys, PDO $database, string $table): void
    {
        foreach ($arrayOfKeys as $column => $value) {
            $sql = "SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '$table'
                    AND COLUMN_NAME = ?";
            $stmt = $database->prepare($sql);
            $stmt->execute([
                $column
            ]);
            $obj = $stmt->fetch();
            if (!$obj) {
                throw new Exception("Column '$column' does not exist");
            } else {
                $sql = "SELECT COLUMN_TYPE
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '$table'
                    AND COLUMN_NAME = ?";
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
    protected static function findId(int $id, PDO $database, string $table): void
    {
        $sql = "SELECT `id` FROM  $table WHERE id = ?";
        $stmt = $database->prepare($sql);
        $stmt->execute([
            $id
        ]);
        if (!$stmt->fetch()) {
            throw new Exception('There is no such record');
        }
    }

    /**
     * @param int $count
     * @param string $table
     * @param string $keys
     * @return string
     */
    protected static function sqlWhereBuilder(int $count, string $table, string $keys): string
    {
        return match ($count) {
            2 => "INSERT INTO $table ($keys)
                VALUES (?,?)",
            3 => "INSERT INTO $table ($keys)
                VALUES (?,?,?)",
            4 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?)",
            5 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?)",
            6 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?)",
            7 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?)",
            8 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?)",
            9 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?)",
            10 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?)",
            11 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            12 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            13 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            14 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            15 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            16 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            17 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            18 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            19 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            20 => "INSERT INTO $table ($keys)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            default => "INSERT INTO $table ($keys)
                VALUES (?)",
        };
    }

    protected static function sqlUpdateBuilder(array $keys, string $table): string|bool
    {
        $count = count($keys);
        return match ($count) {
            2 => "UPDATE $table SET updated_at=CURRENT_TIMESTAMP, $keys[0] = ?, $keys[1] = ? WHERE  id = ?",
            3 => "UPDATE $table SET updated_at=CURRENT_TIMESTAMP, $keys[0] = ?, $keys[1] = ?, $keys[2] = ? WHERE  id = ?",
            4 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?, 
                        $keys[1] = ?, 
                        $keys[2] = ?,
                        $keys[3]=?
                    WHERE  id = ?",
            5 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?, 
                        $keys[1] = ?, 
                        $keys[2] = ?
                        $keys[3] = ?,
                        $keys[4] = ?,
                    WHERE  id = ?",
            6 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?, 
                        $keys[1] = ?, 
                        $keys[2] = ?
                        $keys[3] = ?,
                        $keys[4] = ?,
                        $keys[5] = ?,
                    WHERE  id = ?",
            7 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?, 
                        $keys[1] = ?, 
                        $keys[2] = ?
                        $keys[3] = ?,
                        $keys[4] = ?,
                        $keys[5] = ?,
                        $keys[6] = ?,
                    WHERE  id = ?",
            8 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?, 
                        $keys[1] = ?, 
                        $keys[2] = ?
                        $keys[3] = ?,
                        $keys[4] = ?,
                        $keys[5] = ?,
                        $keys[6] = ?,
                        $keys[7] = ?,
                    WHERE  id = ?",
            9 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP, 
                        $keys[0] = ?,
                        $keys[1] = ?, 
                        $keys[2] = ?, 
                        $keys[3] = ?
                        $keys[4] = ?,
                        $keys[5] = ?,
                        $keys[6] = ?,
                        $keys[7] = ?,
                        $keys[8] = ?,
                        
                    WHERE  id = ?",
            10 => "UPDATE $table 
                    SET 
                        updated_at=CURRENT_TIMESTAMP,
                        $keys[0] = ?,
                        $keys[1] = ?, 
                        $keys[2] = ?, 
                        $keys[3] = ?
                        $keys[4] = ?,
                        $keys[5] = ?,
                        $keys[6] = ?,
                        $keys[7] = ?,
                        $keys[8] = ?,
                        $keys[9] = ?,
                        
                    WHERE  id = ?",
            default => "UPDATE $table SET updated_at=CURRENT_TIMESTAMP, $keys[0] = ? WHERE  id = ?"
        };
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