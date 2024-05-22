/*
Navicat MySQL Data Transfer

Source Server         : 本地数据
Source Server Version : 80025
Source Host           : 127.0.0.1:3306
Source Database       : gadminlog

Target Server Type    : MYSQL
Target Server Version : 80025
File Encoding         : 65001

Date: 2021-12-26 22:09:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for g_api
-- ----------------------------
DROP TABLE IF EXISTS `g_api`;
CREATE TABLE `g_api` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手机号码',
  `params` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '传递参数信息',
  `uid` int DEFAULT NULL COMMENT '操作者id',
  `add_time` int DEFAULT NULL COMMENT '操作时间',
  `ret` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '返回结果',
  `status` int NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='ThinkApi调用接口日志表';

-- ----------------------------
-- Records of g_api
-- ----------------------------

-- ----------------------------
-- Table structure for g_err
-- ----------------------------
DROP TABLE IF EXISTS `g_err`;
CREATE TABLE `g_err` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `controller` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `uri` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `runtime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `memory` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sql` longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `create_time` int DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of g_err
-- ----------------------------

-- ----------------------------
-- Table structure for g_log
-- ----------------------------
DROP TABLE IF EXISTS `g_log`;
CREATE TABLE `g_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `controller` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `uri` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `params` longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `runtime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `memory` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sql` longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `create_time` int DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of g_log
-- ----------------------------

-- ----------------------------
-- Table structure for g_login
-- ----------------------------
DROP TABLE IF EXISTS `g_login`;
CREATE TABLE `g_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `uid` int DEFAULT NULL,
  `login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `login_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `login_browser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `login_time` int DEFAULT NULL,
  `login_os` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of g_login
-- ----------------------------
