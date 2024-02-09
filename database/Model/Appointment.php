<?php

class Appointment extends Model
{
    protected static string $table = 'appointments';


    public static function up(): void
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        $sql = "CREATE TABLE $table
                (
                    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                    `value`      CHAR(50) UNIQUE NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `deleted_at` TIMESTAMP DEFAULT NULL
                );";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    /**
     * @return void
     */
    public static function down()
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        $sql = "DROP TABLE IF EXISTS $table";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
}