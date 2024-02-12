<?php

class Realestate extends Model
{
    /**
     * @var string
     */
    protected static string $table = 'realestates';
    protected static bool|null $softDelete = true;
    /**
     * @return void
     */
    public static function up(): void
    {
        $db = Connector::getInstance();
        $table = self::getTable();
        $sql = "CREATE TABLE $table
                (
                    `id`             INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                    `address`        CHAR(250) NOT NULL,
                    `city`           CHAR(250) NOT NULL,
                    `country`        CHAR(250) NOT NULL,
                    `price`          INT UNSIGNED NOT NULL,
                    `beds_id`        INT UNSIGNED NOT NULL,
                    `bath_id`        INT UNSIGNED NOT NULL,
                    `floor_id`       INT UNSIGNED NOT NULL,
                    `type_id`        INT UNSIGNED NOT NULL,
                    `appointment_id` INT UNSIGNED NOT NULL,
                    `description`    TEXT NOT NULL,
                    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    `deleted_at`     TIMESTAMP DEFAULT NULL,
                    FOREIGN KEY (`beds_id`) REFERENCES `beds` (`id`),
                    FOREIGN KEY (`bath_id`) REFERENCES `baths` (`id`),
                    FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`),
                    FOREIGN KEY (`type_id`) REFERENCES `types` (`id`),
                    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`)
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