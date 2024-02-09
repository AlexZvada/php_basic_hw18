CREATE TABLE `beds`
(
    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `value`      TINYINT UNSIGNED UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP DEFAULT NULL
);
CREATE TABLE `baths`
(
    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `value`      TINYINT UNSIGNED UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP DEFAULT NULL
);
CREATE TABLE `floors`
(
    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `value`      TINYINT UNSIGNED UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP DEFAULT NULL
);
CREATE TABLE `types`
(
    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `value`      CHAR(50) UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP DEFAULT NULL
);
CREATE TABLE `appointments`
(
    `id`         INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `value`      CHAR(50) UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP DEFAULT NULL
);

CREATE TABLE `realestates`
(
    `id`             INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `address`        CHAR(250),
    `city`           CHAR(250),
    `country`        CHAR(250),
    `price`          INT UNSIGNED,
    `beds_id`        INT UNSIGNED,
    `bath_id`        INT UNSIGNED,
    `floor_id`       INT UNSIGNED,
    `type_id`        INT UNSIGNED,
    `appointment_id` INT UNSIGNED,
    `description`    TEXT,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `deleted_at`     TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (`beds_id`) REFERENCES `beds` (`id`),
    FOREIGN KEY (`bath_id`) REFERENCES `baths` (`id`),
    FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`),
    FOREIGN KEY (`type_id`) REFERENCES `types` (`id`),
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`)
);

INSERT INTO `beds`(`value`)
VALUES (1),
       (2),
       (3),
       (4),
       (0);
INSERT INTO `baths`(`value`)
VALUES (1),
       (2),
       (3),
       (4),
       (0);
INSERT INTO `floors`(`value`)
VALUES (1),
       (2),
       (3),
       (4),
       (5),
       (6),
       (7),
       (8);
INSERT INTO `appointments`(`value`)
VALUES ('for rent'),
       ('for sale'),
       ('commercial');
INSERT INTO `types`(`value`)
VALUES ('flat'),
       ('house'),
       ('room'),
       ('villa'),
       ('townhouse');

INSERT INTO `realestates`(`address`, `city`, `country`, `price`, `beds_id`, `bath_id`, `floor_id`, `type_id`,
                          `appointment_id`, `description`)
VALUES ('SMyasoidivska 21/5', 'Odessa', 'Ukraine', 26000, 3, 1, 5, 1, 1, 'some description text'),
       ('SMyasoidivska 22/4', 'Odessa', 'Ukraine', 25000, 3, 2, 2, 1, 3, 'some description text'),
       ('SMyasoidivska 11', 'Odessa', 'Ukraine', 22200, 3, 3, 1, 2, 2, 'some description text'),
       ('SMyasoidivska 4', 'Odessa', 'Ukraine', 46000, 3, 4, 5, 1, 2, 'some description text'),
       ('SMyasoidivska 4', 'Odessa', 'Ukraine', 250000, 3, 3, 5, 1, 2, 'some description text'),
       ('Khreshcatyk', 'Kyiv', 'Ukraine', 125000, 5, 5, 1, 3, 3, 'some description text');

SELECT realestates.city, realestates.address
FROM realestates
WHERE bath_id = 5;

SELECT *
FROM realestates
WHERE price > 50000;

UPDATE realestates SET updated_at=CURRENT_TIMESTAMP, deleted_at = CURRENT_TIMESTAMP WHERE id = 1;

DELETE FROM floors WHERE id = 7;

SELECT * FROM realestates WHERE deleted_at IS NULL ;