-- --------------------------------------------------------
-- Author: Michael Mifsud
-- WWW: http://www.tropotek.com/
--


-- --------------------------------------------------------
--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupId` INT UNSIGNED NOT NULL,
  `username` VARCHAR(64) NOT NULL DEFAULT '',

  `password` TEXT NOT NULL,

  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `publicName` VARCHAR(128) NOT NULL DEFAULT '',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '',

  `notes` TEXT NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `sessionId` VARCHAR(64) NOT NULL DEFAULT '',
  `cookie` VARCHAR(64) DEFAULT '',
  `ip` VARCHAR(64) NOT NULL DEFAULT '',
  `hash` VARCHAR(64) NOT NULL DEFAULT '',
  `timezone` VARCHAR(64) NOT NULL DEFAULT '',
  `lastLogin` DATETIME NULL,
  `firstActive` DATETIME NULL,
  `del` TINYINT(1) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `cookie` (`cookie`),
  KEY `email` (`email`),
  KEY `groupId` (`groupId`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB;

INSERT INTO `user` (`groupId`, `username`, `password`, `email`, `avatar`, `notes`, `active`, `sessionId`, `cookie`, `ip`, `hash`, `timezone`, `lastLogin`, `firstActive`, `del`, `modified`, `created`)
VALUES
  (1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'info@tropotek.com.au', '', '', 1, '', '', '', 'fd9a8f9950fb46c6c24771d750a44212', '', '', '', 0, NOW(), NOW()),
  (2, 'mifsudm', '5f4dcc3b5aa765d61d8327deb882cf99', 'michael.mifsud@unimelb.edu.au', '', '', 1, '', '', '', 'a5ae9e07f60193160699ef16b0834415', '', '', '', 0, NOW(), NOW())
;


DROP TABLE IF EXISTS `userData`;
CREATE TABLE IF NOT EXISTS `userData` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` INT UNSIGNED NOT NULL DEFAULT '0',
  `key` VARCHAR(255) DEFAULT NULL,
  `value` TEXT,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `key` (`key`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `userGroup`;
CREATE TABLE IF NOT EXISTS `userGroup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT NOT NULL,
--  `area` VARCHAR(64) NOT NULL DEFAULT '',
  `homeUrl` VARCHAR(64) NOT NULL DEFAULT '/user/index.html',
  `deletable` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;
INSERT INTO `userGroup` ( `id`, `name`, `description`, `homeUrl`, `deletable` ) VALUES
( NULL, 'admin', 'Full Admin Role', '/admin/index.html', 0),
( NULL, 'user', 'Standard User Role', '/user/index.html', 1) ;



DROP TABLE IF EXISTS `userGroup_userPermission`;
CREATE TABLE IF NOT EXISTS `userGroup_userPermission` (
  `groupId` INT UNSIGNED NOT NULL,
  `permissionId` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`groupId`, `permissionId`)
) ENGINE=InnoDB;
INSERT INTO `userGroup_userPermission` ( `groupId`, `permissionId` ) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(2, 3)
;


DROP TABLE IF EXISTS `userPermission`;
CREATE TABLE IF NOT EXISTS `userPermission` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;
INSERT INTO `userPermission` ( `id`, `name`, `description` ) VALUES
( NULL, 'admin', 'Access to basic admin controls'),
( NULL, 'user', 'Access to basic user controls'),
( NULL, 'public', 'Access to non-authoritive controls') ;



--
-- Install Widgets
--
-- INSERT INTO `widget` (`id` ,`permission` ,`category` ,`name` ,`widgetUri` ,`description` ,`notes`)
-- VALUES
--   (NULL , 'public', 'system', 'Online Users',    '/widget/Usr_Widget_OnlineUsers',  'Current Online Users', NULL )
-- ;
