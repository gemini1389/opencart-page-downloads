SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `oc_down`
-- ----------------------------
DROP TABLE IF EXISTS `oc_down`;
CREATE TABLE `oc_down` (
  `download_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `sort_order` int(3) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `mask` varchar(128) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `filesize` int(20) NOT NULL,
  PRIMARY KEY (`download_id`),
  KEY `download_id` (`download_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oc_down_categories`
-- ----------------------------
DROP TABLE IF EXISTS `oc_down_categories`;
CREATE TABLE `oc_down_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` int(3) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oc_down_categories_description`
-- ----------------------------
DROP TABLE IF EXISTS `oc_down_categories_description`;
CREATE TABLE `oc_down_categories_description` (
  `category_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`category_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oc_down_descriptions`
-- ----------------------------
DROP TABLE IF EXISTS `oc_down_descriptions`;
CREATE TABLE `oc_down_descriptions` (
  `download_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`download_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
