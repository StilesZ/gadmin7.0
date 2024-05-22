/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80029
 Source Host           : localhost:3306
 Source Schema         : gadmin7

 Target Server Type    : MySQL
 Target Server Version : 80029
 File Encoding         : 65001

 Date: 24/10/2023 10:44:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for g_fk_data
-- ----------------------------
DROP TABLE IF EXISTS `g_fk_data`;
CREATE TABLE `g_fk_data`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NULL DEFAULT NULL,
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_open` int NOT NULL DEFAULT 0,
  `exp_time` date NULL DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `add_time` datetime NULL DEFAULT NULL,
  `uid` int NULL DEFAULT NULL,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT 0,
  `act` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '方块报表-设计数据' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_fk_data
-- ----------------------------

-- ----------------------------
-- Table structure for g_fk_imags
-- ----------------------------
DROP TABLE IF EXISTS `g_fk_imags`;
CREATE TABLE `g_fk_imags`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '方块报表-图片数据库' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_fk_imags
-- ----------------------------

-- ----------------------------
-- Table structure for g_fk_main
-- ----------------------------
DROP TABLE IF EXISTS `g_fk_main`;
CREATE TABLE `g_fk_main`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `type` smallint NULL DEFAULT NULL COMMENT '类别',
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '附件',
  `add_time` datetime NULL DEFAULT NULL COMMENT '时间',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `height` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `width` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `update_time` int NULL DEFAULT NULL,
  `fun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '方块报表-核心主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_fk_main
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_billno
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_billno`;
CREATE TABLE `g_sfdp_billno`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '基本设置id',
  `type` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '关联业务',
  `name` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `lenth` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `state` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 单据编号规则表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_billno
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_btable
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_btable`;
CREATE TABLE `g_sfdp_btable`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名称',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表别名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 黑名单表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_btable
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_data
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_data`;
CREATE TABLE `g_sfdp_data`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT 'sid关联',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'token验证',
  `is_open` int NOT NULL DEFAULT 0 COMMENT '是否开启',
  `exp_time` date NULL DEFAULT NULL COMMENT '过期时间',
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '开放元素',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `uid` int NULL DEFAULT NULL COMMENT '添加用户',
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路由信息',
  `type` tinyint NOT NULL DEFAULT 0 COMMENT '类别',
  `act` tinyint NOT NULL DEFAULT 0 COMMENT '方法',
  `numb` int NULL DEFAULT NULL COMMENT '收集总量',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '收集说明',
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '访问密码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 设计数据' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_data
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_design
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_design`;
CREATE TABLE `g_sfdp_design`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `s_bill` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单名称',
  `s_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名',
  `s_db` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据库表名',
  `s_search` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '查询字段',
  `s_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '列表字段',
  `s_design` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '设计状态0：设计中|1：开始设计|2：启用部署',
  `s_look` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '是否锁定',
  `s_field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '字段数据',
  `add_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `s_db_bak` int NOT NULL DEFAULT 0 COMMENT '0：不存在备份表 1：存在',
  `s_type` int NOT NULL DEFAULT 0 COMMENT '设计类别',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 设计器主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_design
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_design_ver
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_design_ver`;
CREATE TABLE `g_sfdp_design_ver`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务编号',
  `s_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '业务名称',
  `s_db` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据表名',
  `s_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '列表字段',
  `s_search` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '查询字段',
  `s_fun_id` int NULL DEFAULT NULL COMMENT '脚本ID',
  `s_fun_ver` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '脚本版本',
  `s_field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '字段JSON',
  `add_user` int NULL DEFAULT NULL COMMENT '用户id',
  `add_time` int NULL DEFAULT NULL COMMENT '创建时间',
  `status` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '版本状态0:停用|1:启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 设计器版本表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_design_ver
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_field
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_field`;
CREATE TABLE `g_sfdp_field`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sid` int NOT NULL COMMENT '版本编号',
  `field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名称',
  `name_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段别名',
  `zanwei` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '占位内容',
  `width` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '120',
  `moren` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认内容',
  `is_request` int NULL DEFAULT NULL COMMENT '是否必填',
  `is_read` int NULL DEFAULT NULL,
  `length` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '长度',
  `type_lx` int NOT NULL DEFAULT 0 COMMENT '选择类型',
  `type_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段格式内容',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
  `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段数据',
  `function` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '调用的函数方法',
  `is_list` int NULL DEFAULT 0 COMMENT '是否列表',
  `is_search` int NULL DEFAULT 0 COMMENT '是否查询',
  `search_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '查询类型',
  `field_wz` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '查询位置',
  `update_time` int NULL DEFAULT NULL,
  `fid` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '字段id',
  `table_type` int NOT NULL DEFAULT 0 COMMENT '0 主表 1子表',
  `table_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 字段库' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_field
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_field_index
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_field_index`;
CREATE TABLE `g_sfdp_field_index`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT '关联设计id',
  `field` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '索引类型',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 索引表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_field_index
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_function
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_function`;
CREATE TABLE `g_sfdp_function`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bill` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '编号',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `fun_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '函数名',
  `function` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '函数SQL',
  `add_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `status` int NOT NULL DEFAULT 0 COMMENT '0编辑中，1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 函数方法表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_function
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_modue
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_modue`;
CREATE TABLE `g_sfdp_modue`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `dbtable` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据表',
  `btn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '按钮',
  `script` int NULL DEFAULT NULL COMMENT '脚本',
  `field_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `access` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `linkdata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据拓展',
  `update_time` int NULL DEFAULT NULL,
  `order` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `height` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '行高度',
  `show_type` smallint NULL DEFAULT 0 COMMENT '列表信息显示：0|普 1|树型',
  `show_fun` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '列表函数',
  `show_field` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联字段',
  `count_field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_saas` int NULL DEFAULT 1 COMMENT '是否saas模式',
  `is_delete` int NULL DEFAULT 1 COMMENT '是否删除模式',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 模块库' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_modue
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_script
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_script`;
CREATE TABLE `g_sfdp_script`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '脚本编号',
  `s_fun` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '脚本代码',
  `add_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加用户',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 脚本表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_script
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_user_config
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_user_config`;
CREATE TABLE `g_sfdp_user_config`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int NOT NULL COMMENT '用户id',
  `sid` int NULL DEFAULT NULL COMMENT '关联sid',
  `field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '字段',
  `field_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '字段名称',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户自定义字段库' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_user_config
-- ----------------------------

-- ----------------------------
-- Table structure for g_sfdp_widget
-- ----------------------------
DROP TABLE IF EXISTS `g_sfdp_widget`;
CREATE TABLE `g_sfdp_widget`  (
  `sid` int NULL DEFAULT NULL COMMENT 'sid值',
  `widget` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组件ids',
  `uptime` int NULL DEFAULT NULL COMMENT '更新事件'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '超级表单 - 列表容器关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_sfdp_widget
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_access
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_access`;
CREATE TABLE `g_soft_access`  (
  `role_id` smallint UNSIGNED NOT NULL COMMENT '角色ID',
  `node_id` smallint UNSIGNED NOT NULL COMMENT '节点ID',
  `pid` smallint UNSIGNED NOT NULL COMMENT 'PID',
  `level` tinyint(1) NOT NULL COMMENT '级别',
  `data` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限URL',
  `user_test1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'test',
  INDEX `groupId`(`role_id`) USING BTREE,
  INDEX `nodeId`(`node_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户权限表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_access
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_access_uid
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_access_uid`;
CREATE TABLE `g_soft_access_uid`  (
  `uid_id` smallint NOT NULL COMMENT '角色ID',
  `node_id` smallint NOT NULL COMMENT '节点ID',
  `pid` smallint NOT NULL COMMENT 'PID',
  `level` tinyint(1) NOT NULL COMMENT '级别',
  `data` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限URL',
  INDEX `groupId`(`uid_id`) USING BTREE,
  INDEX `nodeId`(`node_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户权限表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_access_uid
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_app
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_app`;
CREATE TABLE `g_soft_app`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` tinyint NULL DEFAULT 0 COMMENT '类别，0系统，1开发',
  `app_id` int NULL DEFAULT NULL,
  `con` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `add_time` datetime NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'APP - 内容表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_app
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_app_config
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_app_config`;
CREATE TABLE `g_soft_app_config`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `mid` int NULL DEFAULT NULL COMMENT '目录id',
  `list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '列表设计',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联表',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态',
  `sql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'SQL方法',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `sid` int NULL DEFAULT NULL COMMENT '关联业务sid',
  `content` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `typeid` int NULL DEFAULT NULL COMMENT '类别id',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类别图标',
  `yw_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `yw_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'APP - 配置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_app_config
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_app_type
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_app_type`;
CREATE TABLE `g_soft_app_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '主题',
  `uid` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'APP - 栏目配置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_app_type
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_config
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_config`;
CREATE TABLE `g_soft_config`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '基本设置id',
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据值',
  `name` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `describe` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `type` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型（0系统默认, 1自定义）',
  `only_tag` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '唯一的标记',
  `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `only_tag`(`only_tag`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统配置参数' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_config
-- ----------------------------
INSERT INTO `g_soft_config` VALUES (1, '7.0.0', 'app_version', '', '0', 'app_version', 1676518467);
INSERT INTO `g_soft_config` VALUES (2, '10', 'sys_page', '', '0', 'sys_page', 1676518467);
INSERT INTO `g_soft_config` VALUES (3, '流之云科技', '公司名称', '', '0', 'name', 1696818821);
INSERT INTO `g_soft_config` VALUES (4, 'Gadmin', '系统名称', '', '0', 'sysname', 1696818821);
INSERT INTO `g_soft_config` VALUES (5, 'V7.0.0', '系统版本', '', '0', 'sysv', 1696818821);
INSERT INTO `g_soft_config` VALUES (6, '版权归属：流之云科技', '版权信息', '', '0', 'copy', 1696818821);
INSERT INTO `g_soft_config` VALUES (7, '闽ICP备14008181号', '备案号', '', '0', 'icp', 1696818821);
INSERT INTO `g_soft_config` VALUES (8, 'static/background.jpg', '登录页背景', '', '0', 'logimg', 1696818821);
INSERT INTO `g_soft_config` VALUES (9, 'static/img/logo.png', '登入页LOGO', '', '0', 'logo', 1696818821);
INSERT INTO `g_soft_config` VALUES (10, 'static/layui/icon/logo.png', '后台首页LOGO', '', '0', 'home_logo', 1696818821);
INSERT INTO `g_soft_config` VALUES (11, 'layui', '模板配置', '', '0', 'view', 1696818821);
INSERT INTO `g_soft_config` VALUES (12, 'D:/Tool/program/soffice.exe', 'LibreOffice', '', '0', 'office', 1696818821);
INSERT INTO `g_soft_config` VALUES (13, '2', '启用验证码', '', '0', 'verify', 1696818821);
INSERT INTO `g_soft_config` VALUES (14, '1', '阅读模式', '', '0', 'viewmode', 1696818821);
INSERT INTO `g_soft_config` VALUES (15, 'https://view.officeapps.live.com/op/view.aspx?src=', 'voffice', '', '0', 'voffice', 1696818821);
INSERT INTO `g_soft_config` VALUES (16, '', '授权appid', '', '0', 'username', 1696818821);
INSERT INTO `g_soft_config` VALUES (17, '', '授权密码', '', '0', 'password', 1676518468);
INSERT INTO `g_soft_config` VALUES (18, 'https://up.gadmin8.com', '官网接口', '', '0', 'g_api', 1696818821);
INSERT INTO `g_soft_config` VALUES (19, '1', '启用钉钉', '', '0', 'is_dd', 1696818821);
INSERT INTO `g_soft_config` VALUES (20, '1', '去审核权', '', '0', 'wf_qs', 1696818821);
INSERT INTO `g_soft_config` VALUES (21, '2', '启用微信', '', '0', 'is_wx', 1696818821);
INSERT INTO `g_soft_config` VALUES (22, NULL, 'IP位置接口', '', '0', 'iptoken', 1696818821);
INSERT INTO `g_soft_config` VALUES (23, '1,3,4', '删表权限', '', '0', 'sfdp_db', 1696818821);
INSERT INTO `g_soft_config` VALUES (24, '1,3', '部署权限', '', '0', 'sfdp_fix', 1696818821);
INSERT INTO `g_soft_config` VALUES (25, '1', '桌面版本', '', '0', 'desktype', 1696818821);
INSERT INTO `g_soft_config` VALUES (26, '1', 'Api接口开关', '', '0', 'is_api', 1696818821);
INSERT INTO `g_soft_config` VALUES (27, '1', '打印版本', '', '0', 'print', 1696818821);
INSERT INTO `g_soft_config` VALUES (28, '1', '页面水印', '', '0', 'watermark', 1696818821);
INSERT INTO `g_soft_config` VALUES (29, '1', '数据回收', '', '0', 'datarecycling', 1696818821);
INSERT INTO `g_soft_config` VALUES (30, '0', '在线用户', '', '0', 'online', 1696818821);
INSERT INTO `g_soft_config` VALUES (32, '2', '单一登入', '', '0', 'is_login', 1696818821);
INSERT INTO `g_soft_config` VALUES (33, '流之云智慧平台', 'APP名称', '', '0', 'app_name', 1684221907);
INSERT INTO `g_soft_config` VALUES (34, 'V7.0.0', 'APP版本号', '', '0', 'app_ver', 1684221907);
INSERT INTO `g_soft_config` VALUES (35, '2023-10-24流之云智慧平台移动端正式启用', 'APP滚动广告', '', '0', 'app_ad', 1684221907);
INSERT INTO `g_soft_config` VALUES (36, '0', '查看页面调用审批组件', '', '0', 'view_is_wf', 1696818821);
INSERT INTO `g_soft_config` VALUES (37, '0', '日志查看模式', '', '0', 'view_is_log', 1696818821);
INSERT INTO `g_soft_config` VALUES (39, 'Id:{uid}{date}{name} ', '文档水印', '', '0', 'watermark_content', 1696818821);

-- ----------------------------
-- Table structure for g_soft_crontab
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_crontab`;
CREATE TABLE `g_soft_crontab`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务标题',
  `type` tinyint NOT NULL DEFAULT 0 COMMENT '任务类型',
  `rule` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务频率',
  `shell` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务脚本',
  `run_time` int NOT NULL DEFAULT 0 COMMENT '运行次数',
  `last_time` int NOT NULL DEFAULT 0 COMMENT '最近时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '任务备注',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '任务状态',
  `create_time` int NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `title`(`title`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统定时任务表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_crontab
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_custom
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_custom`;
CREATE TABLE `g_soft_custom`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `php` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `js` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `ver` int NULL DEFAULT NULL COMMENT '版本号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 函数自定义表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_custom
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_datarecycling
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_datarecycling`;
CREATE TABLE `g_soft_datarecycling`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int NOT NULL COMMENT '操作人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '回收时间',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '回收表',
  `table_id` int NOT NULL COMMENT '回收主键',
  `table_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '回收数据json',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 数据回收表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_datarecycling
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_day
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_day`;
CREATE TABLE `g_soft_day`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '日程标题',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `uid` int NOT NULL COMMENT '创建人ID',
  `create_time` int NOT NULL COMMENT '创建时间',
  `update_time` int NOT NULL COMMENT '更新时间',
  `uids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '参与人',
  `remark` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `theme` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '主题标签',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统日程表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_day
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_day_type
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_day_type`;
CREATE TABLE `g_soft_day_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '主题',
  `uid` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 日程类别表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_day_type
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_dept
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_dept`;
CREATE TABLE `g_soft_dept`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dept_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组织编码',
  `dept_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组织名称',
  `dept_pid` int NULL DEFAULT NULL COMMENT '上级组织',
  `status` smallint NULL DEFAULT NULL COMMENT '状态',
  `sort` smallint NULL DEFAULT NULL COMMENT '排序',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `remark` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 组织表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_dept
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_dictionary
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_dictionary`;
CREATE TABLE `g_soft_dictionary`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dict_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '编码',
  `dict_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '名称',
  `dict_type` smallint NULL DEFAULT NULL COMMENT '类别',
  `status` int NULL DEFAULT NULL COMMENT '状态',
  `uid` int NULL DEFAULT NULL COMMENT '用户',
  `add_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 数据字典表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_dictionary
-- ----------------------------
INSERT INTO `g_soft_dictionary` VALUES (1, 'color_code', '颜色列表', 2, 2, 1, 'admin', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for g_soft_dictionary_d
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_dictionary_d`;
CREATE TABLE `g_soft_dictionary_d`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dict_id` int NOT NULL COMMENT '字典id',
  `detail_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详细名称',
  `detail_value` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详细值',
  `detail_sort` smallint NOT NULL COMMENT '排序',
  `bg_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '背景颜色',
  `ft_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字体颜色',
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '状态',
  `uid` smallint NULL DEFAULT NULL COMMENT '更新人员',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 数据字典详细表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_dictionary_d
-- ----------------------------
INSERT INTO `g_soft_dictionary_d` VALUES (1, 1, '纯黑', 'BLACK', 8, 'Black', NULL, '2', NULL, NULL, '#000000');
INSERT INTO `g_soft_dictionary_d` VALUES (2, 1, '青色', 'CYAN', 21, 'Cyan', NULL, '2', NULL, NULL, '#00FFFF');
INSERT INTO `g_soft_dictionary_d` VALUES (3, 1, '纯黄', 'YELLOW', 139, 'Yellow', NULL, '2', NULL, NULL, '#FFFF00');
INSERT INTO `g_soft_dictionary_d` VALUES (4, 1, '粉红', 'PINK', 110, 'Pink', NULL, '2', NULL, NULL, '#FFC0CB');

-- ----------------------------
-- Table structure for g_soft_event
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_event`;
CREATE TABLE `g_soft_event`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT '关联业务',
  `act` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `code` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '代码',
  `uid` int NULL DEFAULT NULL COMMENT ' 用户id',
  `uptime` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统事件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_event
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_file
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_file`;
CREATE TABLE `g_soft_file`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `original` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '原文件名',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '上传IP',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `mtime` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传时间',
  `uptime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统附件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_file
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_help
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_help`;
CREATE TABLE `g_soft_help`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int NULL DEFAULT NULL COMMENT '关联sfdp id',
  `help` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '创建人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `uptime` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 业务帮助中心' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_help
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_message
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_message`;
CREATE TABLE `g_soft_message`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `suid` int NOT NULL,
  `title` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '详情',
  `yw_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务id',
  `yw_type` char(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '业务类型，字符串',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息类型（0工作流消息，1，其他消息）',
  `is_read` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否已读（0否, 1是）',
  `is_delete` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除（0否, 大于0删除时间）',
  `add_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `is_send` tinyint(1) NOT NULL DEFAULT 0 COMMENT '发送状态(0，未发送，1已发送)',
  `send_time` int NOT NULL DEFAULT 0 COMMENT '发送时间',
  `send_num` int NOT NULL DEFAULT 0 COMMENT '发送总次数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 消息通知' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_message
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_navigation
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_navigation`;
CREATE TABLE `g_soft_navigation`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int NOT NULL DEFAULT 0 COMMENT '关联主题',
  `nid` int NOT NULL COMMENT '菜单id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '结束时间',
  `left` int NULL DEFAULT NULL COMMENT 'left',
  `top` int NULL DEFAULT NULL COMMENT 'top',
  `update_time` int NOT NULL COMMENT '更新时间',
  `uid` int NULL DEFAULT 0 COMMENT '参与人',
  `process_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统导航表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_navigation
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_navigation_type
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_navigation_type`;
CREATE TABLE `g_soft_navigation_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '主题',
  `uid` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 导航配置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_navigation_type
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_node
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_node`;
CREATE TABLE `g_soft_node`  (
  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '节点名称',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否激活 1：是 2：否',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注说明',
  `pid` smallint UNSIGNED NOT NULL COMMENT '父ID',
  `level` tinyint UNSIGNED NOT NULL COMMENT '节点等级',
  `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '附加参数',
  `sort` smallint UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重',
  `display` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单显示类型 0:不显示 1:导航菜单 2:左侧菜单',
  `sid` int NULL DEFAULT NULL COMMENT '关联SFDP版本表ID',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ICO',
  `mobile` int NULL DEFAULT 0 COMMENT '移动端开关',
  `mobile_ico` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '移动端图标',
  `lay_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `opentype` tinyint NULL DEFAULT 0,
  `is_page` tinyint NULL DEFAULT 1 COMMENT '一级页面；1关闭，0打开',
  `is_open` tinyint NULL DEFAULT 1 COMMENT '1关闭，0打开',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `level`(`level`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 164 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 菜单表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_node
-- ----------------------------
INSERT INTO `g_soft_node` VALUES (1, 'Gadmin', '根节点', 1, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (3, 'Sys', '企业中枢', 1, NULL, 1, 1, NULL, 999, 1, NULL, 'manage', 0, NULL, 'app', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (4, '', '企业组织', 1, NULL, 3, 0, NULL, 2, 2, NULL, 'user2', 0, NULL, 'username', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (5, '', '企业授权', 1, NULL, 3, 0, 'access/index', 1, 2, NULL, 'arrow2-right', 0, NULL, 'slider', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (6, '', '流程监控', 1, NULL, 3, 0, 'wf/wfctrl', 1, 2, NULL, 'arrow2-right', 1, 'tags', 'chart', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (7, '', '企业BI', 1, NULL, 3, 0, NULL, 3, 2, NULL, 'bold', 0, NULL, 'align-center', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (8, '', '工作流平台', 1, NULL, 3, 0, NULL, 4, 2, NULL, 'jiangjia', 0, NULL, 'chart', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (9, '', '业务平台', 1, NULL, 3, 0, NULL, 5, 2, NULL, 'code', 0, NULL, 'engine', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (10, '', '日志系统', 1, NULL, 3, 0, NULL, 6, 2, NULL, 'ordered-list', 0, NULL, 'time', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (11, '', '系统配置', 1, NULL, 3, 0, NULL, 7, 2, NULL, 'manage2', 0, NULL, 'set', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (12, '', '角色管理', 1, NULL, 4, 2, 'role/index', 0, 2, NULL, NULL, 0, NULL, '', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (13, '', '用户管理', 1, NULL, 4, 2, 'user/index', 0, 2, NULL, NULL, 1, 'account', NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (14, '', '报表管理', 1, NULL, 7, 2, 'report/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (16, '', '流程管理', 1, NULL, 8, 2, 'wf/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (17, '', '流程授权', 1, NULL, 8, 2, 'wf/wfapi?act=wfdl', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (18, '', '业务设计', 1, NULL, 9, 2, 'sfdp/sfdpList', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (19, '', '函数管理', 0, NULL, 9, 2, 'sfdp/sfdpApi?act=fun', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (20, '', '错误日志', 1, NULL, 10, 2, 'Slog/err', 0, 2, NULL, NULL, 1, 'list-dot', NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (21, '', '访问日志', 1, NULL, 10, 2, 'Slog/info', 0, 2, NULL, NULL, 1, 'eye', NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (22, '', '登入日志', 1, NULL, 10, 2, 'Slog/login', 0, 2, NULL, NULL, 1, 'file-text', NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (23, '', '基础数据', 1, NULL, 11, 2, 'sys/base', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (24, '', '消息推送', 1, NULL, 81, 2, 'sys/msg', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (25, '', '桌面配置', 1, NULL, 11, 2, 'sys/desk', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (26, '', '目录管理', 1, NULL, 4, 2, 'node/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (27, 'node', '列表', 1, '', 26, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (28, 'report', '基础配置', 1, NULL, 7, 0, 'report/base', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (29, 'role', '列表', 1, NULL, 12, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (30, 'role', '添加', 1, NULL, 12, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (31, 'role', '节点编辑', 1, NULL, 12, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (32, 'role', '去审核准', 1, NULL, 12, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (33, 'role', '组织结构树', 1, NULL, 12, 3, 'show', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (34, 'user', '列表', 1, NULL, 13, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (35, 'user', '添加', 1, NULL, 13, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (36, 'user', '节点编辑', 1, NULL, 13, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (37, 'user', '去审核准', 1, NULL, 13, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (38, 'user', '用户修改', 1, NULL, 13, 3, 'change', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (39, 'user', '删除用户', 1, NULL, 13, 3, 'del', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (40, 'node', '添加', 1, NULL, 26, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (41, 'node', '节点编辑', 1, NULL, 26, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (42, 'report', '预览', 1, NULL, 14, 3, 'views_s', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (43, 'report', '查看', 1, NULL, 14, 3, 'views', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (44, 'report', '节点头部构建', 1, NULL, 14, 3, 'buildHead', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (45, 'report', '列表', 1, NULL, 14, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (46, 'report', '新增报表', 1, NULL, 14, 3, 'add_report', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (47, 'report', '编辑报表', 1, NULL, 14, 3, 'edit_report', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (48, 'report', '报表状态', 1, NULL, 14, 3, 'status_report', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (49, 'report', '删除报表', 1, NULL, 14, 3, 'del_report', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (50, 'report', '基础表头', 1, NULL, 14, 3, 'base', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (51, 'report', '表头审核', 1, NULL, 14, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (52, 'report', '表头删除', 1, NULL, 14, 3, 'del', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (53, 'report', '表头添加', 1, NULL, 14, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (54, 'report', '表头编辑', 1, NULL, 14, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (55, 'report', '头部设计', 1, NULL, 14, 3, 'head', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (56, 'report', '目录设计', 1, NULL, 14, 3, 'menu', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (57, 'report', 'AJAX目录', 1, NULL, 14, 3, 'ajax_sql', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (58, 'slog', '预览', 1, NULL, 20, 3, 'login', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (59, 'slog', '查看', 1, NULL, 20, 3, 'info', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (60, 'slog', '节点头部构建', 1, NULL, 20, 3, 'err', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (61, 'slog', '列表', 1, NULL, 20, 3, 'del', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (62, 'sys', '基础', 1, NULL, 23, 3, 'base', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (63, 'sys', '消息', 1, NULL, 23, 3, 'msg', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (64, 'sys', '桌面', 1, NULL, 23, 3, 'desk', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (65, 'sys', '配置', 1, NULL, 23, 3, 'deskconfig', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (66, 'sys', '添加', 1, NULL, 23, 3, 'deskadd', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (67, 'sys', '预览', 1, NULL, 23, 3, 'deskview', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (68, 'sys', '状态', 1, NULL, 23, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (69, 'sys', '编辑', 1, NULL, 23, 3, 'deskEdit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (70, 'sys', '删除', 1, NULL, 23, 3, 'del', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (71, 'access', '列表', 1, NULL, 5, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (72, 'access', '授权内容', 1, NULL, 5, 3, 'data', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (73, 'access', '授权', 1, NULL, 5, 3, 'access_edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (74, NULL, '版本管理', 1, NULL, 11, 2, 'sys/up', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (76, '', 'OA办公', 1, '', 1, 1, '', 0, 1, NULL, 'jiangjia', 0, NULL, 'template', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (77, '', '移动端管理', 1, '', 3, 0, NULL, 3, 2, NULL, 'phone', 0, NULL, 'cellphone', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (79, 'app', 'APP管理', 1, NULL, 77, 0, 'app/base', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (80, 'plug', '功能商店', 1, NULL, 11, 2, 'plug/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (81, NULL, '消息中心', 1, NULL, 3, 2, NULL, 49, 2, NULL, NULL, 0, NULL, 'set', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (82, '', '我的消息', 1, '', 81, 2, 'msg/index', 0, 2, NULL, '', 0, '', NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (83, '', '元数管理', 1, '', 3, 2, 'source/index', 50, 2, NULL, '', 0, '', 'loading', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (84, 'initdata', '初始化数据', 1, NULL, 11, 2, 'sys/initdata', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (85, '', '代码自定义', 1, '', 9, 2, 'sys/custom', 0, 2, NULL, NULL, 0, '', '', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (86, '', '方案导航', 1, '', 9, 2, 'sys/navigation', 0, 2, NULL, NULL, 0, '', '', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (87, '', '大屏设计', 1, '', 7, 2, 'report/fk', 0, 2, NULL, NULL, 0, '', '', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (88, 'sys', '初始化', 1, NULL, 23, 3, 'Initdata', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (89, 'sys', '自定义', 1, NULL, 23, 3, 'custom', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (90, 'sys', '日程', 1, NULL, 23, 3, 'schedule', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (91, 'sys', '日程查看', 1, NULL, 23, 3, 'schedule_view', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (92, 'sys', '日程添加', 1, NULL, 23, 3, 'schedule_add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (93, 'sys', '日程标签', 1, NULL, 23, 3, 'schedule_tag', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (94, 'sys', '导航', 1, NULL, 23, 3, 'navigation', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (95, 'sys', '导航保存', 1, NULL, 23, 3, 'navigation_save', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (96, 'sys', '导航阅读', 1, NULL, 23, 3, 'navigation_show', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (97, 'sys', '导航添加', 1, NULL, 23, 3, 'navigation_add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (98, 'sys', '导航方案', 1, NULL, 23, 3, 'navigation_tag', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (99, NULL, '定时任务', 1, NULL, 11, 2, 'crontab/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (100, NULL, '工作台', 1, NULL, 1, 1, '', 0, 1, NULL, NULL, 0, NULL, 'console', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (101, NULL, '流程中心', 1, NULL, 100, 2, 'wf/wfmy', 0, 2, NULL, NULL, 0, NULL, 'chart', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (102, NULL, '日程计划', 1, NULL, 100, 2, 'sys/schedule', 0, 2, NULL, NULL, 0, NULL, 'date', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (103, NULL, '我的消息', 1, NULL, 100, 2, 'msg/index', 0, 2, NULL, NULL, 0, NULL, 'dialogue', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (104, 'access', '用户授权', 1, NULL, 5, 3, 'data2', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (105, 'sys', '桌面方案', 1, NULL, 23, 3, 'deskmain', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (106, 'sys', '方案类别', 1, NULL, 23, 3, 'desk_tag', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (107, NULL, '个人中心', 1, NULL, 197, 2, 'index/personal', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (108, NULL, '用户层级', 1, NULL, 4, 2, 'node/saas', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (109, 'node', '列表', 1, NULL, 108, 3, 'saas', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (110, 'node', '添加', 1, NULL, 108, 3, 'saas_add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (111, 'node', '修改', 1, NULL, 108, 3, 'saas_edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (112, 'node', '状态变更', 1, NULL, 108, 3, 'saas_status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (132, '', '数据回收', 1, NULL, 11, 2, 'sys/datar', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (133, NULL, '超级中心', 1, NULL, 3, 0, 'sys/sup', 1, 2, NULL, 'fire', 0, NULL, 'fire', 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (140, 'access', '用户授权2', 1, NULL, 5, 3, 'access_edit2', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (141, 'user', '登入事件', 1, NULL, 13, 3, 'event', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (142, 'user', '锁定解锁', 1, NULL, 13, 3, 'lock', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (143, 'node', '节点删除', 1, NULL, 26, 3, 'del', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (144, 'node', '节点排序', 1, NULL, 26, 3, 'sort', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (145, 'node', '节点注册', 1, NULL, 26, 3, 'reg', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (146, 'node', '层级管理', 1, NULL, 26, 3, 'saas', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (147, 'node', '层级添加', 1, NULL, 26, 3, 'saas_add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (148, 'node', '层级修改', 1, NULL, 26, 3, 'saas_edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (149, 'node', '层级状态', 1, NULL, 26, 3, 'saas_status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (150, NULL, '字段自定义', 1, NULL, 11, 2, 'sys/field', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (151, 'Dictionary', '数据字典', 1, NULL, 11, 2, 'Dictionary/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (152, 'Dept', '组织管理', 1, NULL, 4, 2, 'Dept/index', 0, 2, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (153, 'dept', '列表', 1, NULL, 152, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (154, 'dept', '添加', 1, NULL, 152, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (155, 'dept', '修改', 1, NULL, 152, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (156, 'dept', '状态', 1, NULL, 152, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (157, 'dept', '展示', 1, NULL, 152, 3, 'show', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (158, 'dictionary', '列表', 1, NULL, 151, 3, 'index', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (159, 'dictionary', '状态', 1, NULL, 151, 3, 'status', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (160, 'dictionary', '添加', 1, NULL, 151, 3, 'add', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (161, 'dictionary', '修改', 1, NULL, 151, 3, 'edit', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (162, 'dictionary', '类目', 1, NULL, 151, 3, 'app_tag', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);
INSERT INTO `g_soft_node` VALUES (163, 'dictionary', '配置', 1, NULL, 151, 3, 'config', 0, 0, NULL, NULL, 0, NULL, NULL, 0, 1, 1);

-- ----------------------------
-- Table structure for g_soft_node_quick
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_node_quick`;
CREATE TABLE `g_soft_node_quick`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int NULL DEFAULT NULL COMMENT '用户id',
  `data` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '网址数据',
  `update` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户快捷栏目' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_node_quick
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_oplog
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_oplog`;
CREATE TABLE `g_soft_oplog`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bill_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '单据表',
  `bill_id` int NOT NULL COMMENT '单据id',
  `op_uid` int NULL DEFAULT NULL COMMENT '操作uid',
  `op_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作姓名',
  `op_time` datetime NULL DEFAULT NULL COMMENT '操作时间',
  `op_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类别',
  `op_con` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `op_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
  `op_os` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '客户端信息',
  `op_platform` tinyint NULL DEFAULT 0 COMMENT '0 pc 端 1为app',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 单据操作日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_oplog
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_plug
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_plug`;
CREATE TABLE `g_soft_plug`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pid` int NULL DEFAULT NULL COMMENT '关联插件表的id',
  `sid` int NULL DEFAULT NULL COMMENT '安装成功后对应的sfdp id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 插件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_plug
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_print
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_print`;
CREATE TABLE `g_soft_print`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `sid` int NULL DEFAULT NULL COMMENT '关联业务',
  `fun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联函数',
  `con` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '模板内容',
  `uptime` int NULL DEFAULT NULL COMMENT '更新时间戳',
  `type` tinyint NOT NULL DEFAULT 0 COMMENT '类型',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 打印模板表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_print
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_report
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_report`;
CREATE TABLE `g_soft_report`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '报表名称',
  `head` int NULL DEFAULT NULL COMMENT '报表头部',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '报表类别',
  `sql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'Sql语句',
  `where` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '查询条件',
  `fjsql` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '附属查询条件',
  `status` int NOT NULL DEFAULT 0 COMMENT '报表状态',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `sid` int NULL DEFAULT 0 COMMENT '函数报表id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 报表主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_report
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_report_head
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_report_head`;
CREATE TABLE `g_soft_report_head`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '名称',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联数据表',
  `field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '关联字段',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 报表基础表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_report_head
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_role
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_role`;
CREATE TABLE `g_soft_role`  (
  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '后台组名',
  `pid` smallint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `status` tinyint UNSIGNED NULL DEFAULT 0 COMMENT '是否激活 2：是 0：否',
  `sort` smallint UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注说明',
  `update_time` int NULL DEFAULT NULL,
  `dept_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '钉钉角色id',
  `dept_wx` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信角色id',
  `orgname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组织名称',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色编号',
  `pcode` int NULL DEFAULT NULL COMMENT '父组织id',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户角色表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_role
-- ----------------------------
INSERT INTO `g_soft_role` VALUES (1, '系统管理员', 0, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for g_soft_role_user
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_role_user`;
CREATE TABLE `g_soft_role_user`  (
  `user_id` int UNSIGNED NOT NULL COMMENT '用户ID',
  `role_id` smallint UNSIGNED NOT NULL COMMENT '角色ID',
  INDEX `group_id`(`role_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户角色关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_role_user
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_saas
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_saas`;
CREATE TABLE `g_soft_saas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint NULL DEFAULT 0,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `uptime` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户层级' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_saas
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_source
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_source`;
CREATE TABLE `g_soft_source`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '元素名称',
  `fun` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '元素方法',
  `conn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '连接数据库',
  `table` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '连接数据表',
  `type` int NULL DEFAULT NULL COMMENT '取数据方法',
  `field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '取字段',
  `order` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '排序',
  `group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '分组查询',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `join` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联子表',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加人',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态',
  `where` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '查询条件',
  `stype` int NULL DEFAULT NULL COMMENT '分组类别',
  `customsql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `open` tinyint NOT NULL DEFAULT 0 COMMENT '开放元素',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 函数表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_source
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_source_join
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_source_join`;
CREATE TABLE `g_soft_source_join`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `link_mid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联字段',
  `link_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联字段',
  `conn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '链接数据库',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '链接数据表',
  `field` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '读取字段',
  `sid` int NOT NULL COMMENT '关联主表id',
  `add_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `add_time` datetime NULL DEFAULT NULL,
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关联别名',
  `link_alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关联表名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 函数关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_source_join
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_user
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_user`;
CREATE TABLE `g_soft_user`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `realname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系电话',
  `mail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `role` int UNSIGNED NOT NULL COMMENT '角色id',
  `dept_id` int NULL DEFAULT NULL COMMENT '组织id',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 1:启用 0:禁止',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注说明',
  `last_login_time` int UNSIGNED NULL DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '最后登录IP',
  `login_count` int NULL DEFAULT 0 COMMENT '登入次数',
  `last_location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '最后登录位置',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `dataaccess` int NULL DEFAULT 0 COMMENT '新增数据权限',
  `dd_userid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '钉钉用户id',
  `wx_userid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信用户id',
  `is_lock` tinyint NOT NULL DEFAULT 0 COMMENT '账号锁定 0不锁定，1锁定',
  `is_delete` tinyint NOT NULL DEFAULT 0 COMMENT '是否软删除 0正常 1，删除',
  `login_err` tinyint NOT NULL DEFAULT 0 COMMENT '登入错误次数 5次自动锁定',
  `sass_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户层级ids',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_user
-- ----------------------------
INSERT INTO `g_soft_user` VALUES (1, 'gadmin', '$2y$12$3d/w/7tG0sFImKMHTi.w6OUgUNDhP0RQOIR0Gz3fYSq596EO202/.', '流之云', '15xx5996213', '1838188896.com', 1, NULL, 1, 'Administrator', 1698115430, '127.0.0.1', 2, '127.0.0.1	保留地址      ', 1516365994, 0, NULL, NULL, 0, 0, 0, NULL);

-- ----------------------------
-- Table structure for g_soft_user_field
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_user_field`;
CREATE TABLE `g_soft_user_field`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `title_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据',
  `mytpe` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
  `lenth` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段长度',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '内容',
  `uid` int NULL DEFAULT NULL COMMENT '用户id',
  `is_add` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '是否执行',
  `create_time` int NULL DEFAULT NULL COMMENT '创建事件',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 系统自定义字段表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_user_field
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_widget
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_widget`;
CREATE TABLE `g_soft_widget`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `widgetTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `widgetHeight` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `showtitle` tinyint NULL DEFAULT NULL,
  `is_app` tinyint NULL DEFAULT NULL,
  `widgetContent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '内容',
  `widgetWidth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '宽度',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态',
  `widgetType` int NULL DEFAULT NULL COMMENT '插件类型',
  `widgetData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '插件数据',
  `Content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `update_time` int NULL DEFAULT NULL COMMENT '更新时间',
  `type` int NULL DEFAULT 0 COMMENT '类别：0首页桌面；1列表桌面',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 桌面数据主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_widget
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_widget_home
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_widget_home`;
CREATE TABLE `g_soft_widget_home`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门户名称',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '门户内容',
  `uptime` int NULL DEFAULT NULL COMMENT '更新时间',
  `layout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '布局信息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 门户创建' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_widget_home
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_widget_type
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_widget_type`;
CREATE TABLE `g_soft_widget_type`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '主题',
  `role` int NULL DEFAULT NULL COMMENT '角色id',
  `ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '设计的桌面方案',
  `uptime` int NULL DEFAULT NULL,
  `layout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '布局',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 桌面方案' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_widget_type
-- ----------------------------

-- ----------------------------
-- Table structure for g_soft_widget_user
-- ----------------------------
DROP TABLE IF EXISTS `g_soft_widget_user`;
CREATE TABLE `g_soft_widget_user`  (
  `uid` int NOT NULL COMMENT '用户id',
  `widget` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联桌面数据',
  `utime` int NULL DEFAULT NULL COMMENT '更新时间',
  `layout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '布局',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统 - 用户数据关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_soft_widget_user
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_entrust
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_entrust`;
CREATE TABLE `g_wf_entrust`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `flow_id` int NOT NULL COMMENT '运行id',
  `flow_process` int NOT NULL COMMENT '运行步骤id',
  `entrust_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `entrust_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '被授权人',
  `entrust_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '被授权人名称',
  `entrust_stime` int NOT NULL COMMENT '授权开始时间',
  `entrust_etime` int NOT NULL COMMENT '授权结束时间',
  `entrust_con` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '授权备注',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `old_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '授权人',
  `old_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '授权人名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 委托授权表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_entrust
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_entrust_rel
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_entrust_rel`;
CREATE TABLE `g_wf_entrust_rel`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `entrust_id` int NOT NULL COMMENT '授权id',
  `process_id` int NOT NULL COMMENT '步骤id',
  `status` int NOT NULL DEFAULT 0 COMMENT '状态0为新增，2为办结',
  `add_time` datetime NULL DEFAULT NULL COMMENT '添加日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 流程授权关系表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_entrust_rel
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_event
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_event`;
CREATE TABLE `g_wf_event`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `act` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `code` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '代码',
  `uid` int NULL DEFAULT NULL COMMENT ' 用户id',
  `uptime` int NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 流程事件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_event
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_flow
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_flow`;
CREATE TABLE `g_wf_flow`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流程类别',
  `flow_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '0不可用1正常',
  `is_del` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `uid` int NULL DEFAULT NULL COMMENT '添加用户',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `is_field` int NULL DEFAULT 0 COMMENT '是否开启过滤',
  `field_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名',
  `field_value` int NULL DEFAULT 0 COMMENT '字段值',
  `tmp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '模板字段',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_flow
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_flow_process
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_flow_process`;
CREATE TABLE `g_wf_flow_process`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `flow_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程ID',
  `process_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '步骤' COMMENT '步骤名称',
  `process_type` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `auto_person` tinyint UNSIGNED NOT NULL DEFAULT 4 COMMENT '3自由选择|4指定人员|5指定角色|6事务接受',
  `auto_sponsor_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '4指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '4指定步骤主办人text',
  `work_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '6事务接受',
  `work_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '6事务接受',
  `work_auto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `work_condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `work_val` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `auto_role_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '5角色ids',
  `auto_role_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '5角色 text',
  `range_user_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '3自由选择IDS',
  `range_user_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '3自由选择用户ID',
  `is_sing` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '1允许|2不允许',
  `is_back` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '1允许|2不允许',
  `out_condition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '转出条件',
  `setleft` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '100' COMMENT '左 坐标',
  `settop` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '100' COMMENT '上 坐标',
  `style` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '样式 序列化',
  `is_del` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `uptime` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `dateline` int UNSIGNED NOT NULL DEFAULT 0,
  `wf_mode` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 单一线性，1，转出条件 2，同步模式',
  `wf_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'view' COMMENT '对应方法',
  `work_sql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `work_msg` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `auto_xt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '2协同字段',
  `auto_xt_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '2协同字段',
  `is_time` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流设计主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_flow_process
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_kpi_data
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_kpi_data`;
CREATE TABLE `g_wf_kpi_data`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_node` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色id',
  `k_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '单据类别',
  `k_type_id` int NOT NULL COMMENT '单据id',
  `k_describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `k_mark` tinyint NOT NULL DEFAULT 1 COMMENT '绩效总分',
  `k_base` tinyint NOT NULL DEFAULT 1 COMMENT '基础分',
  `k_isout` tinyint NOT NULL DEFAULT 0 COMMENT '是否超时 0=未超时 1=超时',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加年',
  `k_month` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加月',
  `k_date` date NULL DEFAULT NULL COMMENT '添加日期',
  `k_create_time` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流用户绩效明细表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_kpi_data
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_kpi_month
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_kpi_month`;
CREATE TABLE `g_wf_kpi_month`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色id',
  `k_mark` bigint NOT NULL DEFAULT 1 COMMENT '绩效总分',
  `k_time` int NOT NULL DEFAULT 1 COMMENT '基础分',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加年',
  `k_month` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加月',
  `k_create_time` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 用户绩效月度绩效' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_kpi_month
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_kpi_year
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_kpi_year`;
CREATE TABLE `g_wf_kpi_year`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `k_uid` int NOT NULL COMMENT '用户id',
  `k_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色id',
  `k_mark` bigint NOT NULL DEFAULT 1 COMMENT '绩效总分',
  `k_time` int NOT NULL DEFAULT 1 COMMENT '总次数',
  `k_year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '添加年',
  `k_create_time` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流绩效年度总表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_kpi_year
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run`;
CREATE TABLE `g_wf_run`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单据表，不带前缀',
  `from_id` int NULL DEFAULT NULL,
  `uid` int UNSIGNED NOT NULL DEFAULT 0,
  `flow_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程id 正常流程',
  `run_flow_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流转到什么ID',
  `run_flow_process` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '流转到第几步',
  `endtime` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间',
  `status` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态，0流程中，1通过',
  `is_del` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `uptime` int UNSIGNED NOT NULL DEFAULT 0,
  `dateline` int UNSIGNED NOT NULL DEFAULT 0,
  `is_sing` int NOT NULL DEFAULT 0,
  `sing_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `is_del`(`is_del`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流运行主表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run_log
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run_log`;
CREATE TABLE `g_wf_run_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `from_id` int NULL DEFAULT NULL COMMENT '单据ID',
  `from_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单据表',
  `run_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流转id',
  `run_flow` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '日志内容',
  `dateline` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `btn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提交操作信息',
  `art` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '附件日志',
  `work_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '事务日志',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `run_id`(`run_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run_log
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run_process
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run_process`;
CREATE TABLE `g_wf_run_process`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int UNSIGNED NOT NULL DEFAULT 0,
  `run_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前流转id',
  `run_flow` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '属于那个流程的id',
  `run_flow_process` smallint UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前步骤编号',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  `auto_person` tinyint NULL DEFAULT NULL,
  `sponsor_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sponsor_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `word_type` int NULL DEFAULT 1 COMMENT '类型 1为人员；2为角色',
  `is_sing` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已会签过',
  `is_back` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '被退回的 0否(默认) 1是',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回',
  `js_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收时间',
  `bl_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '办理时间',
  `is_del` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `updatetime` int UNSIGNED NOT NULL DEFAULT 0,
  `dateline` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `wf_mode` int NULL DEFAULT NULL,
  `wf_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_time` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `run_id`(`run_id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `is_del`(`is_del`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流运行步骤表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run_process
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run_process_cc
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run_process_cc`;
CREATE TABLE `g_wf_run_process_cc`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_id` int NOT NULL COMMENT '关联id',
  `from_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联表',
  `uid` int NULL DEFAULT NULL COMMENT '用户id',
  `run_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '运行run 表id',
  `process_id` int NULL DEFAULT NULL COMMENT '关联步骤id',
  `process_ccid` int NULL DEFAULT NULL COMMENT '消息步骤id',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `uptime` int NULL DEFAULT NULL COMMENT '执行时间',
  `status` smallint NOT NULL DEFAULT 0 COMMENT '0 待确认 1，已确认',
  `auto_person` int NULL DEFAULT NULL COMMENT '办理类别',
  `auto_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '办理ids',
  `user_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 流程抄送表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run_process_cc
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run_process_msg
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run_process_msg`;
CREATE TABLE `g_wf_run_process_msg`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int NULL DEFAULT NULL COMMENT '用户id',
  `run_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '运行run 表id',
  `process_id` int NULL DEFAULT NULL COMMENT '关联步骤id',
  `process_msgid` int NULL DEFAULT NULL COMMENT '消息步骤id',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `uptime` int NULL DEFAULT NULL COMMENT '执行时间',
  `status` smallint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 消息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run_process_msg
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_run_sign
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_run_sign`;
CREATE TABLE `g_wf_run_sign`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int UNSIGNED NOT NULL DEFAULT 0,
  `run_id` int UNSIGNED NOT NULL DEFAULT 0,
  `run_flow` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程ID,子流程时区分run step',
  `run_flow_process` smallint UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前步骤编号',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '会签内容',
  `is_agree` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核意见：1同意；2不同意',
  `sign_att_id` int UNSIGNED NOT NULL DEFAULT 0,
  `dateline` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `run_id`(`run_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流会签记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_run_sign
-- ----------------------------

-- ----------------------------
-- Table structure for g_wf_workinfo
-- ----------------------------
DROP TABLE IF EXISTS `g_wf_workinfo`;
CREATE TABLE `g_wf_workinfo`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `bill_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '单据JSON',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '处理数据',
  `info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '处理结果',
  `datetime` datetime NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '流程 - 工作流实务信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of g_wf_workinfo
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
