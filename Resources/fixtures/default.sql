SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `admin`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT 1 NOT NULL,
    `init` TINYINT(1) DEFAULT 0 NOT NULL,
    `init_hash` VARCHAR(40) NOT NULL,
    `init_url` VARCHAR(255) NOT NULL,
    `recovery_hash` VARCHAR(40),
    `recovery_hash_created_at` DATETIME,
    `last_login_at` DATETIME,
    `login_count` INTEGER DEFAULT 0 NOT NULL,
    `last_activity_at` DATETIME,
    `smart_menu` TINYINT(1) DEFAULT 0 NOT NULL,
    `smart_header` TINYINT(1) DEFAULT 0 NOT NULL,
    `per_page` INTEGER,
    `last_project_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_email` (`email`),
    UNIQUE INDEX `unique_init_hash` (`init_hash`),
    UNIQUE INDEX `unique_recovery_hash` (`recovery_hash`),
    INDEX `last_name` (`last_name`),
    INDEX `fi_in_FK_1` (`last_project_id`),
    CONSTRAINT `admin_FK_1`
        FOREIGN KEY (`last_project_id`)
        REFERENCES `project` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `password`, `init`, `init_hash`, `init_url`, `recovery_hash`, `recovery_hash_created_at`, `last_login_at`, `login_count`, `last_activity_at`, `smart_menu`, `smart_header`, `per_page`, `last_project_id`, `created_at`, `updated_at`) VALUES
(1, 'Tester', 'Tester', 'tester@fontai.net',  '$2y$13$fIvanE1Edv7KaUizQgbp2uY47zFaK9Kg5A2d4QxxltndrdF0CDN3y', 1,  '5d13cdc369f456851a723956ad52173d2dd6ead5', '', NULL, NULL, NULL,  0,  NULL,  1,  0,  NULL, 1,  NOW(),  NOW())
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `admin_login_attempt`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `admin_id` INTEGER NOT NULL,
    `ip` VARCHAR(15) NOT NULL,
    `created_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_in_login_attempt_FK_1` (`admin_id`),
    CONSTRAINT `admin_login_attempt_FK_1`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `admin_permission_group`
(
    `admin_id` INTEGER NOT NULL,
    `permission_group_id` INTEGER NOT NULL,
    PRIMARY KEY (`admin_id`,`permission_group_id`),
    INDEX `fi_in_permission_group_FK_2` (`permission_group_id`),
    CONSTRAINT `admin_permission_group_FK_1`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `admin_permission_group_FK_2`
        FOREIGN KEY (`permission_group_id`)
        REFERENCES `permission_group` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `admin_permission_group` (`admin_id`, `permission_group_id`) VALUES
(1, 1)
ON DUPLICATE KEY UPDATE admin_id = admin_id;

CREATE TABLE IF NOT EXISTS `admin_permission_module_action`
(
    `admin_id` INTEGER NOT NULL,
    `permission_module_action_id` INTEGER NOT NULL,
    PRIMARY KEY (`admin_id`,`permission_module_action_id`),
    INDEX `fi_in_permission_module_action_FK_2` (`permission_module_action_id`),
    CONSTRAINT `admin_permission_module_action_FK_1`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `admin_permission_module_action_FK_2`
        FOREIGN KEY (`permission_module_action_id`)
        REFERENCES `permission_module_action` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `admin_project`
(
    `admin_id` INTEGER NOT NULL,
    `project_id` INTEGER NOT NULL,
    PRIMARY KEY (`admin_id`,`project_id`),
    INDEX `fi_in_project_FK_2` (`project_id`),
    CONSTRAINT `admin_project_FK_1`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `admin_project_FK_2`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `admin_project` (`admin_id`, `project_id`) VALUES
(1, 1)
ON DUPLICATE KEY UPDATE admin_id = admin_id;

CREATE TABLE IF NOT EXISTS `permission_action`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_name` (`name`)
) ENGINE=InnoDB;

INSERT INTO `permission_action` (`id`, `name`) VALUES
(1, 'create'),
(3, 'delete'),
(2, 'edit'),
(4, 'export'),
(5, 'index')
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_action_i18n`
(
    `id` INTEGER NOT NULL,
    `culture` VARCHAR(5) DEFAULT 'cs' NOT NULL,
    `title` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`,`culture`),
    CONSTRAINT `permission_action_i18n_fk_8af4ea`
        FOREIGN KEY (`id`)
        REFERENCES `permission_action` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_action_i18n` (`id`, `culture`, `title`) VALUES
(1, 'cs', 'Přidat'),
(1, 'en', 'Create'),
(2, 'cs', 'Upravit'),
(2, 'en', 'Edit'),
(3, 'cs', 'Mazat'),
(3, 'en', 'Delete'),
(4, 'cs', 'Exportovat'),
(4, 'en', 'Export'),
(5, 'cs', 'Prohlížet'),
(5, 'en', 'List')
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_group`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `infopanel` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_name` (`name`)
) ENGINE=InnoDB;

INSERT INTO `permission_group` (`id`, `name`, `infopanel`) VALUES
(1, 'Superadmin', 1),
(2, 'Administrátor',  1)
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_group_permission_module_action`
(
    `permission_group_id` INTEGER NOT NULL,
    `permission_module_action_id` INTEGER NOT NULL,
    PRIMARY KEY (`permission_group_id`,`permission_module_action_id`),
    INDEX `fi_mission_group_permission_module_action_FK_2` (`permission_module_action_id`),
    CONSTRAINT `permission_group_permission_module_action_FK_1`
        FOREIGN KEY (`permission_group_id`)
        REFERENCES `permission_group` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `permission_group_permission_module_action_FK_2`
        FOREIGN KEY (`permission_module_action_id`)
        REFERENCES `permission_module_action` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_group_permission_module_action` (`permission_group_id`, `permission_module_action_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(2, 13),
(2, 14),
(2, 15),
(2, 16)
ON DUPLICATE KEY UPDATE permission_group_id = permission_group_id;

CREATE TABLE IF NOT EXISTS `permission_module`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `permission_module_category_id` INTEGER NOT NULL,
    `permission_module_id` INTEGER,
    `priority` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_name` (`name`),
    INDEX `fi_mission_module_FK_1` (`permission_module_category_id`),
    INDEX `fi_mission_module_FK_2` (`permission_module_id`),
    CONSTRAINT `permission_module_FK_1`
        FOREIGN KEY (`permission_module_category_id`)
        REFERENCES `permission_module_category` (`id`)
        ON UPDATE CASCADE,
    CONSTRAINT `permission_module_FK_2`
        FOREIGN KEY (`permission_module_id`)
        REFERENCES `permission_module` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO `permission_module` (`id`, `name`, `permission_module_category_id`, `permission_module_id`, `priority`) VALUES
(1, 'FontaiGenerator\\PermissionModule',  6,  NULL, 60),
(2, 'FontaiGenerator\\PermissionAction',  6,  1,  60),
(3, 'FontaiGenerator\\PermissionGroup', 6,  NULL, 60),
(4, 'FontaiGenerator\\Admin', 6,  NULL, 50),
(5, 'FontaiGenerator\\Settings',  6,  NULL, 0),
(6, 'FontaiGenerator\\Cron',  6,  NULL, 0),
(7, 'FontaiGenerator\\Session', 7,  NULL, 0),
(8, 'FontaiGenerator\\EmailLog',  7,  NULL, 0),
(9, 'FontaiGenerator\\SmsLog',  7,  NULL, 0),
(10,  'FontaiGenerator\\EmailTemplateGroup',  5,  13, 0),
(11,  'FontaiGenerator\\Page',  5,  NULL, 0),
(12,  'FontaiGenerator\\Text',  5,  NULL, 0),
(13,  'FontaiGenerator\\EmailTemplate', 5,  NULL, 0),
(15,  'FontaiGenerator\\PageGroup', 5,  11, 0),
(16,  'FontaiGenerator\\Watchdog',  6,  NULL, 0),
(17,  'FontaiGenerator\\WatchdogEvent', 7,  NULL, 0),
(18,  'FontaiGenerator\\SmsTemplateGroup',  5,  19, 0),
(19,  'FontaiGenerator\\SmsTemplate', 5,  NULL, 0),
(20,  'FontaiGenerator\\Email', 7,  NULL, 0),
(21,  'FontaiGenerator\\TextGroup', 5,  12, 0)
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_module_action`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `permission_module_id` INTEGER NOT NULL,
    `permission_action_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fi_mission_module_action_FK_1` (`permission_module_id`),
    INDEX `fi_mission_module_action_FK_2` (`permission_action_id`),
    CONSTRAINT `permission_module_action_FK_1`
        FOREIGN KEY (`permission_module_id`)
        REFERENCES `permission_module` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `permission_module_action_FK_2`
        FOREIGN KEY (`permission_action_id`)
        REFERENCES `permission_action` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_module_action` (`id`, `permission_module_id`, `permission_action_id`) VALUES
(1, 1,  1),
(2, 1,  2),
(3, 1,  3),
(4, 1,  5),
(5, 2,  1),
(6, 2,  2),
(7, 2,  3),
(8, 2,  5),
(9, 3,  1),
(10,  3,  2),
(11,  3,  3),
(12,  3,  5),
(13,  4,  1),
(14,  4,  2),
(15,  4,  3),
(16,  4,  5),
(17,  5,  1),
(18,  5,  2),
(19,  5,  3),
(20,  5,  5),
(21,  6,  1),
(22,  6,  2),
(23,  6,  3),
(24,  6,  5),
(25,  7,  5),
(26,  8,  5),
(27,  9,  5),
(28,  10, 1),
(29,  10, 2),
(30,  10, 3),
(31,  10, 5),
(32,  11, 1),
(33,  11, 2),
(34,  11, 3),
(35,  11, 5),
(36,  12, 1),
(37,  12, 2),
(38,  12, 3),
(39,  12, 5),
(40,  13, 1),
(41,  13, 2),
(42,  13, 3),
(43,  13, 5),
(45,  15, 1),
(46,  15, 2),
(47,  15, 3),
(48,  15, 5),
(49,  16, 1),
(50,  16, 2),
(51,  16, 3),
(52,  16, 5),
(53,  17, 2),
(54,  17, 3),
(55,  17, 4),
(56,  17, 5),
(57,  18, 1),
(58,  18, 2),
(59,  18, 3),
(60,  18, 5),
(61,  19, 1),
(62,  19, 2),
(63,  19, 3),
(64,  19, 5),
(65,  20, 5),
(66,  21, 1),
(67,  21, 2),
(68,  21, 3),
(69,  21, 5),
(70,  11, 4)
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_module_category`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `priority` INTEGER DEFAULT 0 NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `permission_module_category` (`id`, `priority`, `is_default`) VALUES
(1, 100,  1),
(2, 90, 0),
(3, 80, 0),
(4, 70, 0),
(5, 60, 0),
(6, 50, 0),
(7, 40, 0)
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_module_category_i18n`
(
    `id` INTEGER NOT NULL,
    `culture` VARCHAR(5) DEFAULT 'cs' NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`,`culture`),
    CONSTRAINT `permission_module_category_i18n_fk_0e491a`
        FOREIGN KEY (`id`)
        REFERENCES `permission_module_category` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_module_category_i18n` (`id`, `culture`, `name`) VALUES
(1, 'cs', 'Info panel'),
(1, 'en', 'Info panel'),
(2, 'cs', 'Transakce'),
(2, 'en', 'Transactions'),
(3, 'cs', 'Produkty'),
(3, 'en', 'Products'),
(4, 'cs', 'Prodejní jednotky'),
(4, 'en', 'Sale units'),
(5, 'cs', 'Obsah'),
(5, 'en', 'Content'),
(6, 'cs', 'Nastavení'),
(6, 'en', 'Settings'),
(7, 'cs', 'Systémové info'),
(7, 'en', 'System info')
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_module_i18n`
(
    `id` INTEGER NOT NULL,
    `culture` VARCHAR(5) DEFAULT 'cs' NOT NULL,
    `title` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`,`culture`),
    CONSTRAINT `permission_module_i18n_fk_352cf3`
        FOREIGN KEY (`id`)
        REFERENCES `permission_module` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_module_i18n` (`id`, `culture`, `title`) VALUES
(1, 'cs', 'Moduly'),
(1, 'en', 'Modules'),
(2, 'cs', 'Akce'),
(2, 'en', 'Actions'),
(3, 'cs', 'Skupiny oprávnění'),
(3, 'en', 'Permission groups'),
(4, 'cs', 'Administrátoři'),
(4, 'en', 'Admins'),
(5, 'cs', 'Nastavení'),
(5, 'en', 'Settings'),
(6, 'cs', 'Naplánované úlohy'),
(6, 'en', 'Scheduled tasks'),
(7, 'cs', 'Přehled relací'),
(7, 'en', 'Sessions'),
(8, 'cs', 'Historie odesílání e-mailů'),
(8, 'en', 'E-mail history'),
(9, 'cs', 'Historie odesílání SMS'),
(9, 'en', 'SMS history'),
(10,  'cs', 'Skupiny'),
(10,  'en', 'Groups'),
(11,  'cs', 'Statické stránky'),
(11,  'en', 'Static pages'),
(12,  'cs', 'Texty'),
(12,  'en', 'Strings'),
(13,  'cs', 'Šablony e-mailů'),
(13,  'en', 'E-mail templates'),
(15,  'cs', 'Skupiny'),
(15,  'en', 'Groups'),
(16,  'cs', 'Watchdog'),
(16,  'en', 'Watchdog'),
(17,  'cs', 'Události watchdogu'),
(17,  'en', 'Watchdog events'),
(18,  'cs', 'Skupiny'),
(18,  'en', 'Groups'),
(19,  'cs', 'Šablony SMS'),
(19,  'en', 'SMS templates'),
(20,  'cs', 'E-maily k odeslání'),
(20,  'en', 'E-mail queue'),
(21,  'cs', 'Skupiny'),
(21,  'en', 'Groups')
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS `permission_module_priority`
(
    `admin_id` INTEGER NOT NULL,
    `permission_module_id` INTEGER NOT NULL,
    `priority` INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY (`admin_id`,`permission_module_id`),
    INDEX `fi_mission_module_priority_FK_3` (`permission_module_id`),
    CONSTRAINT `permission_module_priority_FK_1`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `permission_module_priority_FK_3`
        FOREIGN KEY (`permission_module_id`)
        REFERENCES `permission_module` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `permission_module_project`
(
    `permission_module_id` INTEGER NOT NULL,
    `project_id` INTEGER NOT NULL,
    PRIMARY KEY (`permission_module_id`,`project_id`),
    INDEX `fi_mission_module_project_FK_2` (`project_id`),
    CONSTRAINT `permission_module_project_FK_1`
        FOREIGN KEY (`permission_module_id`)
        REFERENCES `permission_module` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `permission_module_project_FK_2`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `permission_module_project` (`permission_module_id`, `project_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10,  1),
(11,  1),
(12,  1),
(13,  1),
(15,  1),
(16,  1),
(17,  1),
(18,  1),
(19,  1),
(20,  1),
(21,  1)
ON DUPLICATE KEY UPDATE permission_module_id = permission_module_id;

CREATE TABLE IF NOT EXISTS `project`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `priority` INTEGER DEFAULT 0 NOT NULL,
    `fcc_id` INTEGER NOT NULL,
    `fcc_key` VARCHAR(50) NOT NULL,
    `fcc_pass` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_name` (`name`)
) ENGINE=InnoDB;

INSERT INTO `project` (`id`, `name`, `priority`, `fcc_id`, `fcc_key`, `fcc_pass`) VALUES
(1, 'Fontai', 0,  56, 'MD5(RAND())', 'MD5(RAND())')
ON DUPLICATE KEY UPDATE id = id;