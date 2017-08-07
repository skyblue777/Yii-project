DROP TABLE IF EXISTS `ann_favorites`;

CREATE TABLE `ann_favorites` (
  `user_id` int(11) NOT NULL,
  `annonce_id` int(5) NOT NULL,
  PRIMARY KEY (`user_id`,`annonce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `annonce` */

DROP TABLE IF EXISTS `annonce`;

CREATE TABLE `annonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `type` char(1) CHARACTER SET utf8 NOT NULL,
  `title` varchar(150) CHARACTER SET utf8 NOT NULL,
  `price` int(12) NOT NULL,
  `opt_price` char(1) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `description_notag` text CHARACTER SET utf8,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `area` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `lat` float NOT NULL DEFAULT '0',
  `lng` float NOT NULL DEFAULT '0',
  `photos` text COLLATE utf8_bin,
  `video` text CHARACTER SET utf8,
  `viewed` int(11) NOT NULL DEFAULT '0',
  `replied` int(11) NOT NULL DEFAULT '0',
  `code` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `public` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `featured` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `feature_status` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `feature_days` int(3) NOT NULL DEFAULT '0',
  `feature_total` float NOT NULL DEFAULT '0',
  `feature_mdp` varchar(5) CHARACTER SET utf8 NOT NULL,
  `feature_txn` text CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL,
  `evt` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `txn_id` text CHARACTER SET utf8 NOT NULL,
  `update_time` datetime NOT NULL,
  `send` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `homepage` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `homepage_status` char(1) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `homepage_days` int(3) NOT NULL DEFAULT '0',
  `homepage_total` float NOT NULL DEFAULT '0',
  `homepage_mdp` varchar(5) CHARACTER SET utf8 NOT NULL,
  `homepage_txn` text CHARACTER SET utf8 NOT NULL,
  `for_import` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`title`,`price`,`email`),
  KEY `opt_price` (`opt_price`),
  KEY `id_cat` (`category_id`),
  KEY `date` (`date`),
  KEY `date0` (`create_time`,`update_time`),
  KEY `evt` (`evt`),
  FULLTEXT KEY `NewIndex1` (`title`,`description`),
  FULLTEXT KEY `NewIndex2` (`title`),
  FULLTEXT KEY `NewIndex3` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Table structure for `article`
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` char(5) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(512) NOT NULL,
  `alias` varchar(512) NOT NULL,
  `leading_text` text NOT NULL,
  `content` text,
  `photo` varchar(256) DEFAULT NULL,
  `tags` text,
  `status` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_post_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;

/*Table structure for table `article_tag` */

DROP TABLE IF EXISTS `article_tag`;

CREATE TABLE `article_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `frequency` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `authitem` */

DROP TABLE IF EXISTS `AuthAssignment`;
DROP TABLE IF EXISTS `AuthItemChild`;
DROP TABLE IF EXISTS `AuthItem`;

CREATE TABLE `AuthItem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `authassignment` */

CREATE TABLE `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` int(11) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`),
  CONSTRAINT `FK_authassignment` FOREIGN KEY (`itemname`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `authitemchild` */

CREATE TABLE `AuthItemChild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `FK_authitemchild_1` (`child`),
  CONSTRAINT `FK_authitemchild` FOREIGN KEY (`parent`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_authitemchild_1` FOREIGN KEY (`child`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `banner` */

DROP TABLE IF EXISTS `banner`;

CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `html` text NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_category` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `expired` datetime DEFAULT NULL,
  `duration` tinyint(4) DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `warning_page` tinyint(1) NOT NULL DEFAULT '0',
  `show_ad_counter` tinyint(1) NOT NULL DEFAULT '0',
  `price_required` tinyint(1) NOT NULL DEFAULT '0',
  `paid_ad_required` tinyint(1) NOT NULL DEFAULT '0',
  `show_banner` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_unique` (`alias`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `extension` */

DROP TABLE IF EXISTS `extension`;

CREATE TABLE `extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(255) NOT NULL,
  `class` varchar(64) NOT NULL,
  `method` varchar(64) NOT NULL,
  `config` text,
  `enabled` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `gallery_list` */

DROP TABLE IF EXISTS `gallery_list`;

CREATE TABLE `gallery_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `folder_path` varchar(255) NOT NULL,
  `thumb_width` smallint(6) NOT NULL,
  `thumb_height` smallint(6) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `images` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `lookup` */

DROP TABLE IF EXISTS `lookup`;

CREATE TABLE `lookup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `code` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `module` */

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `friendly_name` varchar(255) DEFAULT NULL,
  `description` text,
  `version` varchar(64) DEFAULT NULL,
  `has_back_end` char(1) NOT NULL DEFAULT 'y',
  `ordering` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `setting` */

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `label` varchar(64) DEFAULT NULL,
  `value` text NOT NULL,
  `description` text,
  `setting_group` varchar(128) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `visible` smallint(6) DEFAULT NULL,
  `module` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `language` */
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` char(10) CHARACTER SET utf8 NOT NULL,
  `code` varchar(128) CHARACTER SET utf8 NOT NULL,
  `value` varchar(512) CHARACTER SET utf8 NOT NULL,
  `group` char(10) NOT NULL,
  `module` char(64) CHARACTER SET utf8 NOT NULL,
  `type` char(64) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `last_login` date DEFAULT NULL,
  `validation_code` varchar(64) DEFAULT NULL,
  `validation_type` smallint(6) DEFAULT NULL,
  `validation_expired` datetime DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `yii_cache` */

DROP TABLE IF EXISTS `yii_cache`;

CREATE TABLE `yii_cache` (
  `id` char(128) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `value` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;