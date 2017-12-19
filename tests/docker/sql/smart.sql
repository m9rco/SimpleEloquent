CREATE DATABASE IF NOT EXISTS db_master_test DEFAULT CHARSET utf8 COLLATE utf8_general_ci;
use db_master_test;
CREATE TABLE `smart_db_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(22) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(22) NOT NULL DEFAULT '' COMMENT '手机号',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `k_phone` (`phone`,`is_delete`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `smart_db_practice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(10) unsigned NOT NULL COMMENT '测试ID',
  `content` varchar(22) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;