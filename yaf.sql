/*
Navicat MySQL Data Transfer

Source Server         : centos
Source Server Version : 50556
Source Host           : 192.168.247.10:3306
Source Database       : imooc

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2018-05-29 11:14:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for art
-- ----------------------------
DROP TABLE IF EXISTS `art`;
CREATE TABLE `art` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '文章标题',
  `contents` text COLLATE utf8_unicode_ci NOT NULL COMMENT '文章内容',
  `author` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '作者名称',
  `cate` int(4) NOT NULL COMMENT '文章分类ID',
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'modify time',
  `ctime` timestamp NULL DEFAULT NULL COMMENT 'create time',
  `status` enum('delete','online','offline') COLLATE utf8_unicode_ci DEFAULT 'offline' COMMENT '是否被删除',
  PRIMARY KEY (`id`),
  KEY `Title index` (`title`),
  KEY `分类索引` (`cate`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='文章';

-- ----------------------------
-- Table structure for bill
-- ----------------------------
DROP TABLE IF EXISTS `bill`;
CREATE TABLE `bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账单id',
  `itemid` int(11) NOT NULL COMMENT '商品id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `price` int(11) NOT NULL DEFAULT '0' COMMENT '商品价格，单位为分',
  `status` enum('paid','unpaid','failed','') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unpaid' COMMENT '支付状态',
  `transaction` text COLLATE utf8_unicode_ci COMMENT '交易ID',
  `mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `ctime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `ptime` timestamp NULL DEFAULT NULL COMMENT '支付时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for cate
-- ----------------------------
DROP TABLE IF EXISTS `cate`;
CREATE TABLE `cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '类目名',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分类信息';

-- ----------------------------
-- Table structure for item
-- ----------------------------
DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品名',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT '商品描述',
  `price` bigint(20) NOT NULL DEFAULT '0' COMMENT '商品价格，单位为分',
  `stock` int(11) NOT NULL COMMENT '商品数量',
  `mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `ctime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `etime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商品';

-- ----------------------------
-- Table structure for sms_record
-- ----------------------------
DROP TABLE IF EXISTS `sms_record`;
CREATE TABLE `sms_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `contents` text COLLATE utf8_unicode_ci NOT NULL COMMENT '消息内容',
  `template` int(11) NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '发送时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='短信发送记录';

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user name',
  `pwd` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user password',
  `email` text COLLATE utf8_unicode_ci COMMENT '用户邮箱',
  `mobile` bigint(11) DEFAULT NULL COMMENT '用户手机号',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'information change time',
  `reg_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'user register time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户注册信息表';
SET FOREIGN_KEY_CHECKS=1;
