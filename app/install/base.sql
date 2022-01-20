/*
Navicat MySQL Data Transfer

Source Server         : 开发数据库
Source Server Version : 50731
Source Host           : 125.65.42.27:3306
Source Database       : sql_lili_dome_wo

Target Server Type    : MYSQL
Target Server Version : 50731
File Encoding         : 65001

Date: 2020-12-04 16:47:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cx_advertising
-- ----------------------------
DROP TABLE IF EXISTS `cx_advertising`;
CREATE TABLE `cx_advertising` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `cont` mediumtext COMMENT '内容',
  `class` int(11) NOT NULL COMMENT '广告类型',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `addtime` int(11) DEFAULT '0' COMMENT '发送时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='广告';

-- ----------------------------
-- Records of cx_advertising
-- ----------------------------

-- ----------------------------
-- Table structure for cx_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `cx_auth_rule`;
CREATE TABLE `cx_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) NOT NULL DEFAULT '' COMMENT '规则地址',
  `title` char(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `condition` char(100) DEFAULT '' COMMENT '附加规则',
  `type_class` mediumint(9) NOT NULL DEFAULT '0' COMMENT '0为后台，1为前台，2为会员中心，3为api',
  `pid` mediumint(9) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `sort` mediumint(9) NOT NULL DEFAULT '50' COMMENT '排序',
  `open` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为开发者模式0为否，1为是',
  `menusee` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在后台菜单显示0为隐藏1为显示',
  `topsee` tinyint(4) DEFAULT '0' COMMENT '是否在顶部导航显示',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_auth_rule
-- ----------------------------
INSERT INTO `cx_auth_rule` VALUES ('1', 'cms.Base', '文章内容管理', '1', '', '1', '0', '9999', '0', '1', '0', 'cx-icon cx-iconwendang');
INSERT INTO `cx_auth_rule` VALUES ('2', 'cms.part/index', '文章栏目管理', '1', '', '1', '1', '0', '0', '1', '0', 'cx-icon cx-iconliebiao');
INSERT INTO `cx_auth_rule` VALUES ('3', 'cms.part/create', '添加栏目', '1', '', '1', '2', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('4', 'cms.part/edit', '编辑栏目', '1', '', '1', '2', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('5', 'cms.part/del', '删除栏目', '1', '', '1', '2', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('6', 'cms.article/index', '文章内容管理', '1', '', '1', '1', '10', '0', '1', '0', 'cx-icon cx-iconbianji2');
INSERT INTO `cx_auth_rule` VALUES ('7', 'cms.article/create', '添加内容', '1', '', '1', '6', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('8', 'cms.article/edit', '编辑内容', '1', '', '1', '6', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('9', 'cms.article/del', '删除内容', '1', '', '1', '6', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('10', 'special/index', '专题管理', '1', '', '1', '66', '0', '0', '1', '0', 'cx-icon cx-iconcaidan1');
INSERT INTO `cx_auth_rule` VALUES ('11', 'special/create', '添加专题', '1', '', '1', '10', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('12', 'special/edit', '编辑专题', '1', '', '1', '10', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('13', 'special/del', '删除专题', '1', '', '1', '10', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('14', 'specialclass/index', '专题分类', '1', '', '1', '66', '0', '0', '1', '0', 'cx-icon cx-iconapp');
INSERT INTO `cx_auth_rule` VALUES ('15', 'specialclass/create', '添加专题分类', '1', '', '1', '14', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('16', 'specialclass/edit', '编辑专题分类', '1', '', '1', '14', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('17', 'specialclass/del', '删除专题分类', '1', '', '1', '14', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('18', 'User', '用户管理系统', '1', '', '1', '0', '2000', '0', '1', '1', 'cx-icon cx-iconusergroup1');
INSERT INTO `cx_auth_rule` VALUES ('19', 'usergroup/index', '用户组列表', '1', '', '1', '18', '5', '0', '1', '0', 'cx-icon cx-iconhaoyou2');
INSERT INTO `cx_auth_rule` VALUES ('20', 'usergroup/create', '添加用户组', '1', '', '1', '19', '0', '0', '0', '0', 'cx-icon cx-iconadd');
INSERT INTO `cx_auth_rule` VALUES ('21', 'usergroup/edit', '编辑用户组', '1', '', '1', '19', '0', '0', '0', '0', 'cx-icon cx-iconbianji1');
INSERT INTO `cx_auth_rule` VALUES ('22', 'usergroup/del', '删除用户组', '1', '', '1', '19', '0', '0', '0', '0', 'cx-icon cx-icondelete');
INSERT INTO `cx_auth_rule` VALUES ('23', 'user/index', '用户列表', '1', '', '1', '18', '0', '0', '1', '0', 'cx-icon cx-iconusergroup1');
INSERT INTO `cx_auth_rule` VALUES ('24', 'user/create', '添加用户', '1', '', '1', '23', '0', '0', '0', '0', 'cx-icon cx-iconadd');
INSERT INTO `cx_auth_rule` VALUES ('25', 'user/edit', '编辑用户', '1', '', '1', '23', '0', '0', '0', '0', 'cx-icon cx-iconbianji1');
INSERT INTO `cx_auth_rule` VALUES ('26', 'user/del', '删除用户', '1', '', '1', '23', '0', '0', '0', '0', 'cx-icon cx-icondelete');
INSERT INTO `cx_auth_rule` VALUES ('27', 'userfile/index', '用户字段管理', '1', '', '1', '18', '0', '0', '1', '0', 'cx-icon cx-iconshiming1');
INSERT INTO `cx_auth_rule` VALUES ('28', 'userfile/edit', '编辑字段', '1', '', '1', '27', '0', '0', '0', '0', 'cx-icon cx-iconbianji2');
INSERT INTO `cx_auth_rule` VALUES ('29', 'userfile/del', '删除字段', '1', '', '1', '27', '0', '0', '0', '0', 'cx-icon cx-iconblueberryuserset');
INSERT INTO `cx_auth_rule` VALUES ('30', 'userfile/create', '添加字段', '1', '', '1', '27', '0', '0', '0', '0', 'cx-icon cx-iconadd');
INSERT INTO `cx_auth_rule` VALUES ('31', 'Nav', '网站导航管理', '1', '', '1', '0', '1998', '0', '1', '0', 'cx-icon cx-iconzhinanzhen2');
INSERT INTO `cx_auth_rule` VALUES ('32', 'navclass/index', '导航分类', '1', '', '1', '31', '0', '0', '1', '0', 'cx-icon cx-iconapp');
INSERT INTO `cx_auth_rule` VALUES ('33', 'navclass/create', '添加分类', '1', '', '1', '32', '0', '0', '0', '0', 'cx-icon cx-iconjia');
INSERT INTO `cx_auth_rule` VALUES ('34', 'navclass/edit', '编辑分类', '1', '', '1', '32', '0', '0', '0', '0', 'cx-icon cx-iconrizhi');
INSERT INTO `cx_auth_rule` VALUES ('35', 'navclass/del', '删除分类', '1', '', '1', '32', '0', '0', '0', '0', 'cx-icon cx-iconlajixiang');
INSERT INTO `cx_auth_rule` VALUES ('36', 'nav/index', '网站导航', '1', '', '1', '31', '0', '0', '1', '0', 'cx-icon cx-iconzhinanzhen1');
INSERT INTO `cx_auth_rule` VALUES ('37', 'nav/create', '添加导航', '1', '', '1', '36', '0', '0', '0', '0', 'cx-icon cx-iconjia');
INSERT INTO `cx_auth_rule` VALUES ('38', 'nav/edit', '编辑导航', '1', '', '1', '36', '0', '0', '0', '0', 'cx-icon cx-iconrizhi');
INSERT INTO `cx_auth_rule` VALUES ('39', 'nav/del', '删除导航', '1', '', '1', '36', '0', '0', '0', '0', 'cx-icon cx-iconlajixiang');
INSERT INTO `cx_auth_rule` VALUES ('40', 'Link', '友情链接管理', '1', '', '1', '0', '1996', '0', '1', '0', 'cx-icon cx-iconlink');
INSERT INTO `cx_auth_rule` VALUES ('41', 'linkclass/index', '友情链接分类', '1', '', '1', '40', '0', '0', '1', '0', 'cx-icon cx-iconfenlei');
INSERT INTO `cx_auth_rule` VALUES ('42', 'linkclass/create', '添加分类', '1', '', '1', '41', '0', '0', '0', '0', 'cx-icon cx-iconadd');
INSERT INTO `cx_auth_rule` VALUES ('43', 'linkclass/edit', '编辑分类', '1', '', '1', '41', '0', '0', '0', '0', 'cx-icon cx-iconbianji2');
INSERT INTO `cx_auth_rule` VALUES ('44', 'linkclass/del', '删除分类', '1', '', '1', '41', '0', '0', '0', '0', 'cx-icon cx-iconlajixiang');
INSERT INTO `cx_auth_rule` VALUES ('45', 'link/index', '友情链接列表', '1', '', '1', '40', '0', '0', '1', '0', 'cx-icon cx-iconcaidan5555');
INSERT INTO `cx_auth_rule` VALUES ('46', 'link/create', '添加友情链接', '1', '', '1', '45', '0', '0', '0', '0', 'cx-icon cx-iconjia');
INSERT INTO `cx_auth_rule` VALUES ('47', 'link/edit', '编辑友情链接', '1', '', '1', '45', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('48', 'link/del', '删除友情链接', '1', '', '1', '45', '0', '0', '0', '0', 'cx-icon cx-iconlajixiang');
INSERT INTO `cx_auth_rule` VALUES ('49', 'Model', '模块管理', '1', '', '1', '0', '20', '0', '1', '1', 'cx-icon cx-iconmenu');
INSERT INTO `cx_auth_rule` VALUES ('50', 'cms.artmodel/index', '内容模块管理', '1', '', '1', '49', '74', '0', '1', '0', 'cx-icon cx-icontianjiagouwuche');
INSERT INTO `cx_auth_rule` VALUES ('51', 'cms.config/index', '内容模型配置', '1', '', '1', '50', '50', '0', '0', '0', 'cx-icon cx-iconbrowser');
INSERT INTO `cx_auth_rule` VALUES ('52', 'cms.artmodel/index', '模型列表', '1', '', '1', '50', '0', '0', '0', '0', 'cx-icon cx-iconcaidan2');
INSERT INTO `cx_auth_rule` VALUES ('53', 'cms.artmodel/del', '删除模型', '1', '', '1', '50', '6', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('54', 'cms.artmodel/create', '添加模型', '1', '', '1', '50', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('55', 'cms.artmodel/edit', '编辑模型', '1', '', '1', '50', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('56', 'model/index', '已安装模块', '1', 'class=0', '1', '49', '0', '0', '1', '0', 'cx-icon cx-iconapp');
INSERT INTO `cx_auth_rule` VALUES ('57', 'Plug', '插件管理', '1', '', '1', '0', '18', '0', '1', '0', 'cx-icon cx-iconshezhi1');
INSERT INTO `cx_auth_rule` VALUES ('58', 'model/index', '已安装插件', '1', 'class=1', '1', '57', '0', '0', '1', '0', 'cx-icon cx-iconneirong');
INSERT INTO `cx_auth_rule` VALUES ('59', 'Config', '系统配置', '1', null, '1', '0', '0', '0', '1', '1', 'cx-icon cx-iconsitting');
INSERT INTO `cx_auth_rule` VALUES ('60', 'config/confedit', '系统基本设置', '1', 'class=1', '1', '59', '50', '0', '1', '0', 'cx-icon cx-iconshezhi');
INSERT INTO `cx_auth_rule` VALUES ('61', 'config/index', '系统参数列表', '1', '', '1', '59', '0', '0', '1', '0', 'cx-icon cx-iconjia');
INSERT INTO `cx_auth_rule` VALUES ('62', 'configclass/index', '系统参数分类', '1', '', '1', '59', '0', '0', '1', '0', 'cx-icon cx-iconapp');
INSERT INTO `cx_auth_rule` VALUES ('63', 'model/copymodel', '安装模块', '1', '', '1', '56', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('64', 'model/edit', '编辑模块', '1', '', '1', '56', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('65', 'model/del', '卸载模块', '1', '', '1', '56', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('66', 'Special', '专题内容管理', '1', '', '1', '0', '7000', '0', '1', '0', 'cx-icon cx-iconliebiao');
INSERT INTO `cx_auth_rule` VALUES ('67', 'specialarticle/index', '专题内容列表', '1', '', '1', '10', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('68', 'specialarticle/del', '删除专题内容', '1', '', '1', '10', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('69', 'cms.fupart/index', '辅助栏目', '1', '', '1', '1', '0', '0', '1', '0', 'cx-icon cx-iconliebiao');
INSERT INTO `cx_auth_rule` VALUES ('70', 'cms.fupart/create', '添加辅栏目', '1', '', '1', '69', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('71', 'cms.fupart/edit', '编辑辅栏目', '1', '', '1', '69', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('72', 'cms.fupart/del', '删除辅栏目', '1', '', '1', '69', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('73', 'form.artmodel/index', '表单管理', '1', '', '1', '0', '3000', '0', '1', '0', 'cx-icon cx-iconcaidan2');
INSERT INTO `cx_auth_rule` VALUES ('74', 'form.artmodel/create', '添加模型', '1', '', '1', '73', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('75', 'form.artmodel/edit', '编辑模型', '1', '', '1', '73', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('76', 'form.artmodel/del', '删除模型', '1', '', '1', '73', '6', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('77', 'form.article/index', '表单管理', '1', '', '1', '73', '40', '0', '0', '0', 'cx-icon cx-iconbianji2');
INSERT INTO `cx_auth_rule` VALUES ('78', 'form.article/create', '添加内容', '1', '', '1', '77', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('79', 'form.article/edit', '编辑内容', '1', '', '1', '77', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('80', 'form.article/del', '删除内容', '1', '', '1', '77', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('81', 'sqldata/index', '备份数据库', '1', '', '1', '59', '0', '0', '1', '0', 'cx-icon cx-iconjinbi');
INSERT INTO `cx_auth_rule` VALUES ('82', 'admin/Advertising', '广告管理', '1', '', '1', '0', '2990', '0', '1', '0', 'cx-icon cx-iconshuju1');
INSERT INTO `cx_auth_rule` VALUES ('83', 'admin/Advertising/index', '广告列表', '1', '', '1', '82', '0', '0', '1', '0', 'cx-icon cx-iconfenlei1');
INSERT INTO `cx_auth_rule` VALUES ('84', 'admin/Advertising/create', '添加广告', '1', '', '1', '82', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('85', 'admin/Advertising/edit', '编辑广告', '1', '', '1', '82', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('86', 'admin/Advertising/del', '删除广告', '1', '', '1', '82', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('87', 'comment/index', '评论管理', '1', '', '1', '0', '2996', '0', '1', '0', 'cx-icon cx-iconcaidan5555');
INSERT INTO `cx_auth_rule` VALUES ('88', 'comment/edit', '评论审核', '1', '', '1', '87', '0', '0', '0', '0', '');
INSERT INTO `cx_auth_rule` VALUES ('89', 'comment/del', '删除评论', '1', '', '1', '87', '0', '0', '0', '0', '');

-- ----------------------------
-- Table structure for cx_auth_ruleclass
-- ----------------------------
DROP TABLE IF EXISTS `cx_auth_ruleclass`;
CREATE TABLE `cx_auth_ruleclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '权限分类名称',
  `uri` varchar(255) DEFAULT NULL COMMENT '权限链接地址',
  `sort` mediumint(9) DEFAULT '0' COMMENT '排序值',
  `status` tinyint(4) DEFAULT '1' COMMENT '是否启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_auth_ruleclass
-- ----------------------------
INSERT INTO `cx_auth_ruleclass` VALUES ('1', '后台管理', null, '0', '1');
INSERT INTO `cx_auth_ruleclass` VALUES ('2', '会员中心', null, '0', '1');
INSERT INTO `cx_auth_ruleclass` VALUES ('3', '前台权限', null, '0', '1');
INSERT INTO `cx_auth_ruleclass` VALUES ('4', 'API权限', null, '0', '1');

-- ----------------------------
-- Table structure for cx_chinacode
-- ----------------------------
DROP TABLE IF EXISTS `cx_chinacode`;
CREATE TABLE `cx_chinacode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zoneid` varchar(15) DEFAULT NULL,
  `parzoneid` varchar(15) DEFAULT NULL,
  `zonename` varchar(50) DEFAULT NULL,
  `zname` varchar(30) DEFAULT NULL,
  `zentop` varchar(5) DEFAULT NULL,
  `zenname` varchar(20) DEFAULT NULL,
  `cartid` varchar(255) DEFAULT NULL,
  `cartname` varchar(255) DEFAULT NULL,
  `cartzname` varchar(255) DEFAULT NULL,
  `zonelevel` tinyint(4) DEFAULT NULL,
  `sort` tinyint(4) DEFAULT '50',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_chinacode
-- ----------------------------

-- ----------------------------
-- Table structure for cx_cms_content
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_content`;
CREATE TABLE `cx_cms_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `uid` varchar(100) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `jian` tinyint(4) NOT NULL DEFAULT '0',
  `zan` tinyint(4) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  `hist` int(11) NOT NULL DEFAULT '0' COMMENT '点击量',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值',
  `pick` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否存在缩略图',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `status` (`status`),
  KEY `jian` (`jian`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COMMENT='模型内容索引表';

-- ----------------------------
-- Records of cx_cms_content
-- ----------------------------
INSERT INTO `cx_cms_content` VALUES ('1', '2', '1', '11', '1', '0', '0', '1603704873', '0', '23', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('2', '2', '1', '11', '1', '0', '0', '1603704922', '0', '20', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('3', '2', '1', '14', '1', '0', '0', '1603705021', '0', '31', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('4', '2', '1', '11', '1', '0', '0', '1603705047', '0', '24', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('5', '2', '1', '15', '1', '0', '0', '1603705160', '0', '31', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('6', '2', '1', '15', '1', '0', '0', '1603705207', '0', '30', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('7', '2', '1', '12', '1', '0', '0', '1603705273', '0', '42', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('8', '2', '1', '12', '1', '0', '0', '1603705317', '0', '36', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('18', '1', '1', '5', '1', '0', '0', '1603764207', '0', '34', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('19', '1', '1', '5', '1', '0', '0', '1603764231', '0', '28', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('20', '1', '1', '5', '1', '0', '0', '1603764386', '0', '38', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('21', '1', '1', '2', '1', '0', '0', '1603781857', '0', '26', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('22', '1', '1', '2', '1', '0', '0', '1603781912', '0', '34', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('23', '1', '1', '2', '1', '0', '0', '1603781948', '0', '31', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('24', '1', '1', '2', '1', '0', '0', '1603782280', '0', '103', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('25', '2', '1', '15', '1', '0', '0', '1603791340', '0', '29', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('26', '2', '1', '16', '1', '0', '0', '1603792005', '0', '32', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('27', '2', '1', '14', '1', '0', '0', '1603792279', '0', '90', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('28', '2', '1', '14', '1', '0', '0', '1603792297', '0', '78', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('29', '1', '1', '5', '1', '0', '0', '1603792707', '0', '44', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('30', '1', '1', '10', '1', '0', '0', '1603873275', '0', '23', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('31', '1', '1', '10', '1', '0', '0', '1603873519', '0', '21', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('32', '1', '1', '10', '1', '0', '0', '1603873541', '0', '23', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('33', '1', '1', '10', '1', '0', '0', '1603873565', '0', '24', '0', '0');
INSERT INTO `cx_cms_content` VALUES ('34', '1', '1', '10', '1', '0', '0', '1603873604', '0', '73', '0', '0');

-- ----------------------------
-- Table structure for cx_cms_content_1
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_content_1`;
CREATE TABLE `cx_cms_content_1` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `uid` varchar(100) NOT NULL COMMENT '用户ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `keywords` varchar(50) DEFAULT NULL COMMENT '关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '内容简介',
  `picurl` varchar(200) DEFAULT NULL COMMENT '缩略图',
  `comment_num` int(11) NOT NULL DEFAULT '0' COMMENT '评论量',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核 0 =》 未审，1 =》 已审，2=》拒绝|一般用不上',
  `hist` int(11) NOT NULL DEFAULT '0' COMMENT '点击',
  `jian` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐,0=>不推荐，1=>推荐,2以上推荐为分类级以上推荐',
  `zan` int(11) NOT NULL DEFAULT '0' COMMENT '点赞',
  `zhuan` int(11) NOT NULL DEFAULT '0' COMMENT '转发',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `sort` int(11) NOT NULL COMMENT '排序',
  `edittime` int(11) DEFAULT '0' COMMENT '修改时间',
  `addip` varchar(20) NOT NULL COMMENT '添加IP',
  `editip` varchar(20) DEFAULT NULL COMMENT '修改IP',
  `del_time` int(11) NOT NULL DEFAULT '0',
  `jumpurl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`),
  KEY `hist` (`hist`),
  KEY `comment_num` (`comment_num`),
  KEY `jian` (`jian`),
  KEY `zan` (`zan`),
  KEY `zhuan` (`zhuan`),
  KEY `addtime` (`addtime`),
  KEY `edittime` (`edittime`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COMMENT='文章模型模型表';

-- ----------------------------
-- Records of cx_cms_content_1
-- ----------------------------
INSERT INTO `cx_cms_content_1` VALUES ('18', '1', '5', '1', 'Givan', '<p>研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。 </p><p></p><p>研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专利证书，开发了MINI RF、USSRF、SCS RF等全系列化产品。</p>', '', '研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。  \n\n\n\n 研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专', 'http://www_cxbs_net/Ls_dir/2020-10/909a9df09d.png', '0', '1', '34', '0', '0', '0', '1603764207', '1603764207', '1603792661', '101.86.179.56', '101.86.179.56', '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('19', '1', '5', '1', 'CsBnhs', '<p>研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。 </p><p></p><p>研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专利证书，开发了MINI RF、USSRF、SCS RF等全系列化产品。</p>', '', '研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备', 'http://www_cxbs_net/Ls_dir/2020-10/9542141345.png', '0', '1', '28', '0', '0', '0', '1603764231', '1603764231', '1603792594', '101.86.179.56', '101.86.179.56', '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('20', '1', '5', '1', 'lisa', '<p>研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。 </p><p></p><p>研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专利证书，开发了MINI RF、USSRF、SCS RF等全系列化产品。</p>', '', '研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。  \n\n  研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专利证书，开发了MINI RF、USSRF、SCS RF等全系列化产品。', 'http://www_cxbs_net/Ls_dir/2020-10/dd73031be1.png', '0', '1', '38', '0', '0', '0', '1603764386', '1603764386', '1603792604', '101.86.179.56', '101.86.179.56', '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('21', '1', '2', '1', '全球首个特高压配套清洁能源项目投运 破解新能源消纳“卡脖子”难题', '<p>近日，在青海省海南州的切吉草原上，备受关注的海南州特高压外送通道配套电源1300MW风电项目顺利并网，青海省建设国家清洁能源示范省迈出关键一步。</p><p></p><p>据了解，海南州特高压外送通道配套电源1300MW风电项目由国家电投集团黄河上游水电开发有限责任公司（以下简称“黄河公司”）投资建设，占地面积491平方公里，工程东西直线距离100公里，平均海拔接近3100米，分6个标段建设，总共506台风机。</p><p></p><p>值得一提的是，该项目配套的青豫特高压直流输电工程是青海首条特高压外送通道，同时也是全球首条100%消纳清洁能源的特高压通道，该工程起于青海省海南藏族自治州，止于河南省驻马店市，是华中电网消纳区外清洁能源的重要通道。</p><p></p><p>远景能源解决方案负责人许锋飞告诉《中国经营报》记者，“在高海拔、高严寒、强紫外线等苛刻建设环境下，海南州1300MW大基地项目在不到一年的建设周期内顺利建成全容量并网，对于后续北方平价大基地项目，及未来青豫直流特高压二期项目的推进建设都有十分重要的借鉴意义。”</p><p></p><p>低风速区的三北风电大基地</p><p></p><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/2020-10/73734d40b7.jpeg\"></figure><p></p><p>海南州1300MW风电基地虽在“三北”（华北、西北、东北）地区，却并非是常见的高风速大基地项目。实际上，在标况下，机位处平均风速为5米/秒左右，不仅远低于内蒙古7.5米/秒左右的风速水平，甚至不及某些使用了高塔技术的低风速平原地区。</p><p></p><p>许锋飞告诉记者，在低风速区，风机的选型及各建设环节的降本提效显得尤为重要。在海南州1300MW大基地风电项目中，一标段2号地块（500MW）均采用远景能源EN-141/2.65MW低风速智能风机，塔筒高度为90米；该风场设计年等效满发小时数达2088小时，年发电量可超过10.4亿度电。</p><p></p><p>“针对本项目机型的选择我们主要从风资源状况、风机可靠性、配套产业链的成熟度及项目投资收益要求等多方面考量。”许锋飞告诉记者。远景定制化风机的环境适应性、安全可靠性和针对性的控制策略有效提升了项目全生命周期的发电量，并降低了全生命周期的运维成本。同时在设计阶段，远景利用格林威治平台对本项目的塔筒、基础、集电线路、升压站选址、道路平台等系统工程反复迭代优化，给出最合理经济的设计方案，极大降低了本项目的建设投资。</p><p></p><p>“整个项目建设周期的缩短本身就是降低风电基地度电成本的举措。”黄河水电工程建设分公司新能源建设部经理张峰华强调，“海南州1300MW风电基地并不需要平价，它是有补贴的竞价项目，但是通过各种手段，在现有的条件下我们能把度电成本压到多低，这是我们此次想要探索的东西。”</p>', '新能源', '近日，在青海省海南州的切吉草原上，备受关注的海南州特高压外送通道配套电源1300MW风电项目顺利并网，青海省建设国家清洁能源示范省迈出关键一步。\n\n\n\n据了解，海南州特高压外送通道配套电源1300MW风电项目由国家电投集团黄河上游水电开发有限责任公司（以下简称“黄河公司”）投资建设，占地面积491平方公里，工程东西直线距离100公里，平均海拔接近3100米，分6个标段建设，总共506台风机。', 'http://www_cxbs_net/Ls_dir/2020-10/9ff67cff90.jpg', '0', '1', '26', '0', '0', '0', '1603781857', '1603781857', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('22', '1', '2', '1', '四季沐歌荣获“全国清洁供暖/供热优秀项目案例”', '<p>近日，在青海省海南州的切吉草原上，备受关注的海南州特高压外送通道配套电源1300MW风电项目顺利并网，青海省建设国家清洁能源示范省迈出关键一步。</p><p></p><p>据了解，海南州特高压外送通道配套电源1300MW风电项目由国家电投集团黄河上游水电开发有限责任公司（以下简称“黄河公司”）投资建设，占地面积491平方公里，工程东西直线距离100公里，平均海拔接近3100米，分6个标段建设，总共506台风机。</p><p></p><p>值得一提的是，该项目配套的青豫特高压直流输电工程是青海首条特高压外送通道，同时也是全球首条100%消纳清洁能源的特高压通道，该工程起于青海省海南藏族自治州，止于河南省驻马店市，是华中电网消纳区外清洁能源的重要通道。</p><p></p><p>远景能源解决方案负责人许锋飞告诉《中国经营报》记者，“在高海拔、高严寒、强紫外线等苛刻建设环境下，海南州1300MW大基地项目在不到一年的建设周期内顺利建成全容量并网，对于后续北方平价大基地项目，及未来青豫直流特高压二期项目的推进建设都有十分重要的借鉴意义。”</p><p></p><p>低风速区的三北风电大基地</p><p></p><p>海南州1300MW风电基地虽在“三北”（华北、西北、东北）地区，却并非是常见的高风速大基地项目。实际上，在标况下，机位处平均风速为5米/秒左右，不仅远低于内蒙古7.5米/秒左右的风速水平，甚至不及某些使用了高塔技术的低风速平原地区。</p><p></p><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/2020-10/bb3b3c1e3c.jpeg\"></figure><p></p><p>许锋飞告诉记者，在低风速区，风机的选型及各建设环节的降本提效显得尤为重要。在海南州1300MW大基地风电项目中，一标段2号地块（500MW）均采用远景能源EN-141/2.65MW低风速智能风机，塔筒高度为90米；该风场设计年等效满发小时数达2088小时，年发电量可超过10.4亿度电。</p><p></p><p>“针对本项目机型的选择我们主要从风资源状况、风机可靠性、配套产业链的成熟度及项目投资收益要求等多方面考量。”许锋飞告诉记者。远景定制化风机的环境适应性、安全可靠性和针对性的控制策略有效提升了项目全生命周期的发电量，并降低了全生命周期的运维成本。同时在设计阶段，远景利用格林威治平台对本项目的塔筒、基础、集电线路、升压站选址、道路平台等系统工程反复迭代优化，给出最合理经济的设计方案，极大降低了本项目的建设投资。</p><p></p><p>“整个项目建设周期的缩短本身就是降低风电基地度电成本的举措。”黄河水电工程建设分公司新能源建设部经理张峰华强调，“海南州1300MW风电基地并不需要平价，它是有补贴的竞价项目，但是通过各种手段，在现有的条件下我们能把度电成本压到多低，这是我们此次想要探索的东西。”</p>', '', '海南州1300MW风电基地虽在“三北”（华北、西北、东北）地区，却并非是常见的高风速大基地项目。实际上，在标况下，机位处平均风速为5米/秒左右，不仅远低于内蒙古7.5米/秒左右的风速水平，甚至不及某些使用了高塔技术的低风速平原地区。', 'http://www_cxbs_net/Ls_dir/2020-10/1272611131.jpg', '0', '1', '34', '0', '0', '0', '1603781912', '1603781912', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('23', '1', '2', '1', '国家再次强调：大力发展清洁能源、可再生能源和绿色环保产业！', '<p>近日，在青海省海南州的切吉草原上，备受关注的海南州特高压外送通道配套电源1300MW风电项目顺利并网，青海省建设国家清洁能源示范省迈出关键一步。</p><p></p><p>据了解，海南州特高压外送通道配套电源1300MW风电项目由国家电投集团黄河上游水电开发有限责任公司（以下简称“黄河公司”）投资建设，占地面积491平方公里，工程东西直线距离100公里，平均海拔接近3100米，分6个标段建设，总共506台风机。</p><p></p><p>值得一提的是，该项目配套的青豫特高压直流输电工程是青海首条特高压外送通道，同时也是全球首条100%消纳清洁能源的特高压通道，该工程起于青海省海南藏族自治州，止于河南省驻马店市，是华中电网消纳区外清洁能源的重要通道。</p><p></p><p>远景能源解决方案负责人许锋飞告诉《中国经营报》记者，“在高海拔、高严寒、强紫外线等苛刻建设环境下，海南州1300MW大基地项目在不到一年的建设周期内顺利建成全容量并网，对于后续北方平价大基地项目，及未来青豫直流特高压二期项目的推进建设都有十分重要的借鉴意义。”</p><p></p><p>低风速区的三北风电大基地</p><p></p><p>海南州1300MW风电基地虽在“三北”（华北、西北、东北）地区，却并非是常见的高风速大基地项目。实际上，在标况下，机位处平均风速为5米/秒左右，不仅远低于内蒙古7.5米/秒左右的风速水平，甚至不及某些使用了高塔技术的低风速平原地区。</p><p></p><p>许锋飞告诉记者，在低风速区，风机的选型及各建设环节的降本提效显得尤为重要。在海南州1300MW大基地风电项目中，一标段2号地块（500MW）均采用远景能源EN-141/2.65MW低风速智能风机，塔筒高度为90米；该风场设计年等效满发小时数达2088小时，年发电量可超过10.4亿度电。</p><p></p><p>“针对本项目机型的选择我们主要从风资源状况、风机可靠性、配套产业链的成熟度及项目投资收益要求等多方面考量。”许锋飞告诉记者。远景定制化风机的环境适应性、安全可靠性和针对性的控制策略有效提升了项目全生命周期的发电量，并降低了全生命周期的运维成本。同时在设计阶段，远景利用格林威治平台对本项目的塔筒、基础、集电线路、升压站选址、道路平台等系统工程反复迭代优化，给出最合理经济的设计方案，极大降低了本项目的建设投资。</p><p></p><p>“整个项目建设周期的缩短本身就是降低风电基地度电成本的举措。”黄河水电工程建设分公司新能源建设部经理张峰华强调，“海南州1300MW风电基地并不需要平价，它是有补贴的竞价项目，但是通过各种手段，在现有的条件下我们能把度电成本压到多低，这是我们此次想要探索的东西。”</p>', '', '“针对本项目机型的选择我们主要从风资源状况、风机可靠性、配套产业链的成熟度及项目投资收益要求等多方面考量。”许锋飞告诉记者。远景定制化风机的环境适应性、安全可靠性和针对性的控制策略有效提升了项目全生命周期的发电量，并降低了全生命周期的运维成本。同时在设计阶段，远景利用格林威治平台对本项目的塔筒、基础、集电线路、升压站选址、道路平台等系统工程反复迭代优化，给出最合理经济的设计方案，极大降低了本项目的建设投资。', 'http://www_cxbs_net/Ls_dir/2020-10/fc72c5c5a5.jpg', '0', '1', '31', '0', '0', '0', '1603781948', '1603781948', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('24', '1', '2', '1', '青豫特高压配套新能源发电项目全面并网', '<p>近日，在青海省海南州的切吉草原上，备受关注的海南州特高压外送通道配套电源1300MW风电项目顺利并网，青海省建设国家清洁能源示范省迈出关键一步。</p><p></p><p>据了解，海南州特高压外送通道配套电源1300MW风电项目由国家电投集团黄河上游水电开发有限责任公司（以下简称“黄河公司”）投资建设，占地面积491平方公里，工程东西直线距离100公里，平均海拔接近3100米，分6个标段建设，总共506台风机。</p><p></p><p>值得一提的是，该项目配套的青豫特高压直流输电工程是青海首条特高压外送通道，同时也是全球首条100%消纳清洁能源的特高压通道，该工程起于青海省海南藏族自治州，止于河南省驻马店市，是华中电网消纳区外清洁能源的重要通道。</p><p></p><p>远景能源解决方案负责人许锋飞告诉《中国经营报》记者，“在高海拔、高严寒、强紫外线等苛刻建设环境下，海南州1300MW大基地项目在不到一年的建设周期内顺利建成全容量并网，对于后续北方平价大基地项目，及未来青豫直流特高压二期项目的推进建设都有十分重要的借鉴意义。”</p><p></p><p>低风速区的三北风电大基地</p><p></p><p>海南州1300MW风电基地虽在“三北”（华北、西北、东北）地区，却并非是常见的高风速大基地项目。实际上，在标况下，机位处平均风速为5米/秒左右，不仅远低于内蒙古7.5米/秒左右的风速水平，甚至不及某些使用了高塔技术的低风速平原地区。</p><p></p><p style=\"text-align:center;\">许锋飞告诉记者，在低风速区，风机的选型及各建设环节的降本提效显得尤为重要。在海南州1300MW大基地风电项目中，一标段2号地块（500MW）均采用远景能源EN-141/2.65MW低风速智能风机，塔筒高度为90米；该风场设计年等效满发小时数达2088小时，年发电量可超过10.4亿度电。</p><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/2020-10/d1ff303dff.jpeg\"></figure><p></p><p>“针对本项目机型的选择我们主要从风资源状况、风机可靠性、配套产业链的成熟度及项目投资收益要求等多方面考量。”许锋飞告诉记者。远景定制化风机的环境适应性、安全可靠性和针对性的控制策略有效提升了项目全生命周期的发电量，并降低了全生命周期的运维成本。同时在设计阶段，远景利用格林威治平台对本项目的塔筒、基础、集电线路、升压站选址、道路平台等系统工程反复迭代优化，给出最合理经济的设计方案，极大降低了本项目的建设投资。</p><p></p><p>“整个项目建设周期的缩短本身就是降低风电基地度电成本的举措。”黄河水电工程建设分公司新能源建设部经理张峰华强调，“海南州1300MW风电基地并不需要平价，它是有补贴的竞价项目，但是通过各种手段，在现有的条件下我们能把度电成本压到多低，这是我们此次想要探索的东西。”</p>', '', '“整个项目建设周期的缩短本身就是降低风电基地度电成本的举措。”黄河水电工程建设分公司新能源建设部经理张峰华强调，“海南州1300MW风电基地并不需要平价，它是有补贴的竞价项目，但是通过各种手段，在现有的条件下我们能把度电成本压到多低，这是我们此次想要探索的东西。”', 'http://www_cxbs_net/Ls_dir/2020-10/cc2f82202c.jpg', '0', '1', '103', '0', '0', '0', '1603782280', '1603782280', '1604886552', '101.86.179.56', '101.224.79.70', '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('29', '1', '5', '1', 'HiosfJ', '<p>研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。 </p><p></p><p>研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专利证书，开发了MINI RF、USSRF、SCS RF等全系列化产品。</p>', '', '研发中心团队积极创新、获得深圳市多项项目发展资金和产业化应用扶持，同时与IEC中国标委会、中国行业检测中心等国内著名科研机构和院校展开技术人才交流和研发合作。  \n\n\n\n 研发中心团队着眼于微型化高可靠射频连接器及互连系统的研发，此项目被列为深圳市“十二五”重大项目计划，在设计和制造微型和超小型连接器领域积累并具备了独到经验和优越，至今已拥有四十多项专利，其中也获得了美国、欧洲、日本专', 'http://www_cxbs_net/Ls_dir/2020-10/4214a89aa9.png', '0', '1', '44', '0', '0', '0', '1603792707', '1603792707', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('30', '1', '10', '1', '诚心供应商', '', '', '', 'http://www_cxbs_net/Ls_dir/2020-10/dd54cc7d75.jpg', '0', '1', '23', '0', '0', '0', '1603873275', '1603873275', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('31', '1', '10', '1', '诚信经营示范单位', '', '', '', 'http://www_cxbs_net/Ls_dir/2020-10/a604904ca0.jpg', '0', '1', '21', '0', '0', '0', '1603873519', '1603873519', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('32', '1', '10', '1', '信用等级证书', '', '', '', 'http://www_cxbs_net/Ls_dir/2020-10/63738784a6.jpg', '0', '1', '23', '0', '0', '0', '1603873541', '1603873541', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('33', '1', '10', '1', '质量服务等级单位', '', '', '', 'http://www_cxbs_net/Ls_dir/2020-10/88233aa358.jpg', '0', '1', '24', '0', '0', '0', '1603873565', '1603873565', '0', '101.86.179.56', null, '0', null);
INSERT INTO `cx_cms_content_1` VALUES ('34', '1', '10', '1', '重合同守信用企业', '', '', '', 'http://www_cxbs_net/Ls_dir/2020-10/8c8bebe118.jpg', '0', '1', '73', '0', '0', '0', '1603873604', '1603873604', '0', '101.86.179.56', null, '0', null);

-- ----------------------------
-- Table structure for cx_cms_content_2
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_content_2`;
CREATE TABLE `cx_cms_content_2` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `uid` varchar(100) NOT NULL COMMENT '用户ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `keywords` varchar(50) DEFAULT NULL COMMENT '关键词',
  `description` varchar(255) DEFAULT NULL COMMENT '内容简介',
  `picurl` varchar(200) DEFAULT NULL COMMENT '缩略图',
  `comment_num` int(11) NOT NULL DEFAULT '0' COMMENT '评论量',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核 0 =》 未审，1 =》 已审，2=》拒绝|一般用不上',
  `hist` int(11) NOT NULL DEFAULT '0' COMMENT '点击',
  `jian` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐,0=>不推荐，1=>推荐,2以上推荐为分类级以上推荐',
  `zan` int(11) NOT NULL DEFAULT '0' COMMENT '点赞',
  `zhuan` int(11) NOT NULL DEFAULT '0' COMMENT '转发',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `sort` int(11) NOT NULL COMMENT '排序',
  `edittime` int(11) DEFAULT '0' COMMENT '修改时间',
  `addip` varchar(20) NOT NULL COMMENT '添加IP',
  `editip` varchar(20) DEFAULT NULL COMMENT '修改IP',
  `del_time` int(11) NOT NULL DEFAULT '0',
  `jumpurl` varchar(255) DEFAULT NULL,
  `cp_type` varchar(255) DEFAULT NULL,
  `imgdata` text,
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`),
  KEY `hist` (`hist`),
  KEY `comment_num` (`comment_num`),
  KEY `jian` (`jian`),
  KEY `zan` (`zan`),
  KEY `zhuan` (`zhuan`),
  KEY `addtime` (`addtime`),
  KEY `edittime` (`edittime`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='产品模型模型表';

-- ----------------------------
-- Records of cx_cms_content_2
-- ----------------------------
INSERT INTO `cx_cms_content_2` VALUES ('1', '2', '11', '1', 'ly03太阳能', '<p>按照循环方式来分一般有强制循环式和自然循环式：</p><p></p><p>1、强制循环式系统一般采用联箱将多个太阳能集热器并联起来，再通过上、下循环管道与不锈钢保温水箱相连接，下循环泵根据太阳能集热器和水箱内的水温差（5℃~10℃）来决定是否启动：即太阳能集热器内的水温高于水箱内的水温时，循环泵工作，将水箱的水抽到太阳能集热器的底部，太阳能集热器上部的热水则被顶入水箱；当太阳能集热器和水箱内的水温基本平衡时，循环泵则停止工作，如此重复动作，保证所需热水的水温恒定。</p><p></p><p>2、自然循环式系统是由太阳能集热器和不锈钢保温水箱连接而成，依靠太阳能集热器水温变化产生的液位差，产生热虹吸效应，高温水上升进入水箱上部，低温水流入真空管，如此往复循环，提高水箱内的水温，以达到人们所需的温度。但是系统中要求水箱置于太阳能集热器上部，并保持在同一水平位置，困难较大，所以一般采取多台家庭单机串联使用，不宜做成较大的太阳能热水系统。</p><p></p><p>通过比较，自然循环式太阳能热水系统前期投资小，运行和维护费用低，但是水温提升慢，热效率较低，所以在普通家庭中应用较多。</p><p></p><p>上海木菱实业有限公司是一家从事太阳能热水器，太阳能热水工程，阳台挂壁太阳能等，相关节能产品的设计，销售，安装及服务的专业公司。公司自成立开始就树立“用心把产品做好，精心研究产品工艺，全心为用户服务”的企业精神，坚持品牌发展之路，用品质和服务树立行业品牌。</p>', '太阳能', '自然循环式系统是由太阳能集热器和不锈钢保温水箱连接而成，依靠太阳能集热器水温变化产生的液位差，产生热虹吸效应，高温水上升进入水箱上部，低温水流入真空管，如此往复循环，提高水箱内的水温，以达到人们所需的温度。但是系统中要求水箱置于太阳能集热器上部，并保持在同一水平位置，困难较大，所以一般采取多台家庭单机串联使用，不宜做成较大的太阳能热水系统。', 'http://www_cxbs_net/Ls_dir/2020-10/e2ace65c62.jpg', '0', '1', '23', '0', '0', '0', '1603704873', '1603704873', '1603791042', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"太阳能3.jpg\",\"sort\":\"0\",\"size\":\"102577\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/b2bd622227.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('2', '2', '11', '1', 'ly02太阳能', '<p>按照循环方式来分一般有强制循环式和自然循环式：</p><p></p><p>1、强制循环式系统一般采用联箱将多个太阳能集热器并联起来，再通过上、下循环管道与不锈钢保温水箱相连接，下循环泵根据太阳能集热器和水箱内的水温差（5℃~10℃）来决定是否启动：即太阳能集热器内的水温高于水箱内的水温时，循环泵工作，将水箱的水抽到太阳能集热器的底部，太阳能集热器上部的热水则被顶入水箱；当太阳能集热器和水箱内的水温基本平衡时，循环泵则停止工作，如此重复动作，保证所需热水的水温恒定。</p><p></p><p>2、自然循环式系统是由太阳能集热器和不锈钢保温水箱连接而成，依靠太阳能集热器水温变化产生的液位差，产生热虹吸效应，高温水上升进入水箱上部，低温水流入真空管，如此往复循环，提高水箱内的水温，以达到人们所需的温度。但是系统中要求水箱置于太阳能集热器上部，并保持在同一水平位置，困难较大，所以一般采取多台家庭单机串联使用，不宜做成较大的太阳能热水系统。</p><p></p><p>通过比较，自然循环式太阳能热水系统前期投资小，运行和维护费用低，但是水温提升慢，热效率较低，所以在普通家庭中应用较多。</p><p></p><p>上海木菱实业有限公司是一家从事太阳能热水器，太阳能热水工程，阳台挂壁太阳能等，相关节能产品的设计，销售，安装及服务的专业公司。公司自成立开始就树立“用心把产品做好，精心研究产品工艺，全心为用户服务”的企业精神，坚持品牌发展之路，用品质和服务树立行业品牌。</p>', '太阳能', '自然循环式系统是由太阳能集热器和不锈钢保温水箱连接而成，依靠太阳能集热器水温变化产生的液位差，产生热虹吸效应，高温水上升进入水箱上部，低温水流入真空管，如此往复循环，提高水箱内的水温，以达到人们所需的温度。但是系统中要求水箱置于太阳能集热器上部，并保持在同一水平位置，困难较大，所以一般采取多台家庭单机串联使用，不宜做成较大的太阳能热水系统。', 'http://www_cxbs_net/Ls_dir/2020-10/b535b33555.jpg', '0', '1', '20', '0', '0', '0', '1603704922', '1603704922', '1603790997', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"太阳能2.jpg\",\"sort\":\"0\",\"size\":\"101803\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/0066dd60ff.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('3', '2', '14', '1', 'ly01风能', '<p>厄尔尼诺发威，冬季天候异常暖和，今年可能是欧洲史上最热的一年。德国用电需求大减，风力发电量又飙升，供电过剩使得今年圣诞节的电费可能降到零元以下，到时用一整天的电都不用花半毛钱，电厂还得倒贴。</p><p></p><p>彭博社22日报导，外界预估，圣诞节当天，德国风力发电量将为过去一个月平均值的两倍。与此同时，欧洲气候温暖，用电量将减少。能源谘询商MarkedskraftDeutschlandGmbH估计，风力发电量增加，要是燃煤和天然气发电厂不停止供电，电费或许会降到零元以下，可能会维持好几小时、甚至数天之久。</p><p></p><p>圣诞假期，估计德国的风力发电量约在10GW~20GW(gigawatt/十亿瓦)之间，气温则比往年高出摄氏8度。1GW可供应200万户的家庭用电。德国经济部和能源游说团体BDEW数据显示，德国的可更新能源可望供给当地三分之一用电，比去年增加20%。</p><p></p><p>全球各国今年底将在巴黎会议商讨气候变迁协定，国际能源署(IEA)估计，如果各国能落实承诺，风力、太阳能等可更新能源，将在15年内取代煤炭，成为最主要的电力来源。</p>', '风能', '厄尔尼诺发威，冬季天候异常暖和，今年可能是欧洲史上最热的一年。德国用电需求大减，风力发电量又飙升，供电过剩使得今年圣诞节的电费可能降到零元以下，到时用一整天的电都不用花半毛钱，电厂还得倒贴。\n\n\n\n彭博社22日报导，外界预估', 'http://www_cxbs_net/Ls_dir/2020-10/fa22faaf44.jpg', '0', '1', '31', '0', '0', '0', '1603705021', '1603705021', '1603792240', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"风能.jpg\",\"sort\":\"0\",\"size\":\"67526\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/b5a55aad5a.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('4', '2', '11', '1', 'ly01太阳能', '<p>按照循环方式来分一般有强制循环式和自然循环式：</p><p></p><p>1、强制循环式系统一般采用联箱将多个太阳能集热器并联起来，再通过上、下循环管道与不锈钢保温水箱相连接，下循环泵根据太阳能集热器和水箱内的水温差（5℃~10℃）来决定是否启动：即太阳能集热器内的水温高于水箱内的水温时，循环泵工作，将水箱的水抽到太阳能集热器的底部，太阳能集热器上部的热水则被顶入水箱；当太阳能集热器和水箱内的水温基本平衡时，循环泵则停止工作，如此重复动作，保证所需热水的水温恒定。</p><p></p><p>2、自然循环式系统是由太阳能集热器和不锈钢保温水箱连接而成，依靠太阳能集热器水温变化产生的液位差，产生热虹吸效应，高温水上升进入水箱上部，低温水流入真空管，如此往复循环，提高水箱内的水温，以达到人们所需的温度。但是系统中要求水箱置于太阳能集热器上部，并保持在同一水平位置，困难较大，所以一般采取多台家庭单机串联使用，不宜做成较大的太阳能热水系统。</p><p></p><p>通过比较，自然循环式太阳能热水系统前期投资小，运行和维护费用低，但是水温提升慢，热效率较低，所以在普通家庭中应用较多。</p><p></p><p>上海木菱实业有限公司是一家从事太阳能热水器，太阳能热水工程，阳台挂壁太阳能等，相关节能产品的设计，销售，安装及服务的专业公司。公司自成立开始就树立“用心把产品做好，精心研究产品工艺，全心为用户服务”的企业精神，坚持品牌发展之路，用品质和服务树立行业品牌。</p>', '餐桌', '1、强制循环式系统一般采用联箱将多个太阳能集热器并联起来，再通过上、下循环管道与不锈钢保温水箱相连接，下循环泵根据太阳能集热器和水箱内的水温差（5℃~10℃）来决定是否启动：即太阳能集热器内的水温高于水箱内的水温时，循环泵工作，将水箱的水抽到太阳能集热器的底部，太阳能集热器上部的热水则被顶入水箱；当太阳能集热器和水箱内的水温基本平衡时，循环泵则停止工作，如此重复动作，保证所需热水的水温恒定。', 'http://www_cxbs_net/Ls_dir/2020-10/1f11a75aa1.jpg', '0', '1', '24', '0', '0', '0', '1603705047', '1603705047', '1603790982', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"太阳能1.jpg\",\"sort\":\"0\",\"size\":\"127637\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/34e2923344.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('5', '2', '15', '1', 'ly02新能源煤', '<p>中国是富煤、缺油、少气的国家，长期以来形成了以煤为主要能源的消费结构，在相当长的时间里，我们主要依靠煤和化石能源发展经济，而化石能源需求增长与依赖进口已形成压力，《老柳说车》根据2012年中国水利水电出版社出版的《中国新能源》中查到的数据：目前（2012年）中国石油大量进口，对外依存度高达66.8%。中东、北非局势不稳（2020年美、伊局势雾里看花）进口渠道不平坦，美国在全球开展能源圈地运动，我国开拓海外屡遭遏制。 国内占能源消费70%的煤储采比为35年，石油仅9.9年，天然气开采主要在新疆，虽然储采比29年，但使用成本太高，消费比重仅为4.4%，水电仅能支撑5%。所以发展新能源，调整国家能源战略布局是迫切需要。</p><p>目前我国新能源发展中有问题还在解决，新能源汽车的消费结构在非限牌城市还没有形成，很多短板有待进行一步修正。</p><p></p><p>当电池替代汽车作为汽车动力能源，汽车不再是汽车，将变成一种电子产品，构造更简单，进入门槛更低。部分车企一拥而上，抢占地盘，出现了骗取国家补贴等不和谐现象。</p><p></p><p>大势不可逆，随着国家发改委对汽车产业投资政策的调整，这些不和谐现象会被历史车轮无情碾压。</p><p></p><p>所以，中国发展新能源的核心问题是煤，煤是我们的主要能源，建构以煤为主体的消费结构，可以摆脱我们对化石能源的依赖。</p>', '新能源煤', '中国是富煤、缺油、少气的国家，长期以来形成了以煤为主要能源的消费结构，在相当长的时间里，我们主要依靠煤和化石能源发展经济，而化石能源需求增长与依赖进口已形成压力，《老柳说车》根据2012年中国水利水电出版社出版的《中国新能源》中查到的数据：目前（2012年）中国石油大量进口，对外依存度高达66.8%。中东、北非局', 'http://www_cxbs_net/Ls_dir/2020-10/f028778f08.jpg', '0', '1', '31', '0', '0', '0', '1603705160', '1603705160', '1603791293', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"煤1.jpg\",\"sort\":\"0\",\"size\":\"97905\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/a7dd7d73a3.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('6', '2', '15', '1', 'ly01新能源煤', '<p>中国是富煤、缺油、少气的国家，长期以来形成了以煤为主要能源的消费结构，在相当长的时间里，我们主要依靠煤和化石能源发展经济，而化石能源需求增长与依赖进口已形成压力，《老柳说车》根据2012年中国水利水电出版社出版的《中国新能源》中查到的数据：目前（2012年）中国石油大量进口，对外依存度高达66.8%。中东、北非局势不稳（2020年美、伊局势雾里看花）进口渠道不平坦，美国在全球开展能源圈地运动，我国开拓海外屡遭遏制。 国内占能源消费70%的煤储采比为35年，石油仅9.9年，天然气开采主要在新疆，虽然储采比29年，但使用成本太高，消费比重仅为4.4%，水电仅能支撑5%。所以发展新能源，调整国家能源战略布局是迫切需要。</p><p>目前我国新能源发展中有问题还在解决，新能源汽车的消费结构在非限牌城市还没有形成，很多短板有待进行一步修正。</p><p></p><p>当电池替代汽车作为汽车动力能源，汽车不再是汽车，将变成一种电子产品，构造更简单，进入门槛更低。部分车企一拥而上，抢占地盘，出现了骗取国家补贴等不和谐现象。</p><p></p><p>大势不可逆，随着国家发改委对汽车产业投资政策的调整，这些不和谐现象会被历史车轮无情碾压。</p><p></p><p>所以，中国发展新能源的核心问题是煤，煤是我们的主要能源，建构以煤为主体的消费结构，可以摆脱我们对化石能源的依赖。</p>', '新能源煤', '当电池替代汽车作为汽车动力能源，汽车不再是汽车，将变成一种电子产品，构造更简单，进入门槛更低。部分车企一拥而上，抢占地盘，出现了骗取国家补贴等不和谐现象。\n\n\n\n大势不可逆，随着国家发改委对汽车产业投资政策的调整，这些不和谐现象会被历史车轮无情碾压。\n\n\n\n所以，中国发展新能源的核心问题是煤，煤是我们的主要能源，建构以煤为主体的消费结构，可以摆脱我们对化石能源的依赖。', 'http://www_cxbs_net/Ls_dir/2020-10/8c55c88558.jpg', '0', '1', '30', '0', '0', '0', '1603705207', '1603705207', '1603791250', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"煤.jpg\",\"sort\":\"0\",\"size\":\"75831\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/1d47e1fd0e.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('7', '2', '12', '1', 'ly01天然气', '<p>当大家还在为天然气紧缺而着急的时候，在中国沉寂多年的煤层气悄悄在崛起。煤层气就是大家口中的“瓦斯”，是煤的伴生矿产资源，热值与天然气相当，可与天然气混输混用，燃烧后十分洁净，是上好的工业、化工、发电和居民生活燃料。我国煤层气储量十分丰富，但由于技术限制等因素一直以来十分低调，去年随着连续多个千亿方大气田的发现，煤层气终于迎来了“主角”时刻。</p><p></p><p>煤层气的勘探可以追溯到2006年，华北油田成立了山西煤层气分公司，在当年钻井超过100口，开始正式进入煤层气领域的勘探开发，并于2009年率先实现了中石油煤层气开发的商业化运营。此后华北油田在晋东南沁水盆地累计提交探明煤层气地质储量2131亿立方米，建成了国内第一个数字化、规模化整装煤层气田。目前，华北油田在沁水盆地煤层气的探明储量有2800多亿立方米，产量达到40多亿立方米。</p><p></p><p>沁水盆地的煤层气已经形成了上下游一体化产业格局，接下来华北油田煤层气的开发将进入新阶段：沁水高煤阶做大，河北大成的中煤阶和内蒙的低煤阶取得突破，远景目标是突破40亿方商品气量。对于华北油田而言，煤层气的突破，将成为接下来油田发展的重要部分，有望在未来十年左右实现油田能源结构的彻底转变。</p><p></p><p>中国的煤层气资源的勘探和开采潜力远比现在所表现出来的惊人，中国埋深2000米以内的煤层气资源达36.81万亿方，居世界第三位。美国能源情报署预测，到2040年，中国煤层气产量将占到全国天然气总产量的25%。但在过去的十年中，受制于技术、经验等因素，煤层气的勘探开发一直没有太大的进展，近两年我国多地勘探到巨量的煤层气资源，并实施开采，进行商用。</p><p></p><p>2017年9月，新疆乌鲁木齐市达坂城区发现可开采大型煤层气田，预测储量1000亿立方米，现已进入开采阶段。11月，高县文江地区的煤层气参数井获高产工业气流，标志着四川南部煤层气地质调查取得重大突破。初步估算，川南地区煤系气资源量约4500亿立方米，具有极好的勘查开发前景。</p><p></p><p>良好的资源前景当然还需政策扶持，国家进一步提高了“十三五”期间煤层气的补贴。此外，以山西为代表的资源大省还开放了一批煤层气探矿权，引进社会资本，推动煤层气的发展。这一切都预示着曾经默默无闻的煤层气即将登上中国的能源舞台。</p>', '天然气', '沁水盆地的煤层气已经形成了上下游一体化产业格局，接下来华北油田煤层气的开发将进入新阶段：沁水高煤阶做大，河北大成的中煤阶和内蒙的低煤阶取得突破，远景目标是突破40亿方商品气量。对于华北油田而言，煤层气的突破，将成', 'http://www_cxbs_net/Ls_dir/2020-10/c7c744497a.jpg', '0', '1', '42', '0', '0', '0', '1603705273', '1603705273', '1603791867', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"天然气1.jpg\",\"sort\":\"0\",\"size\":\"81238\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/22eee54e25.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('8', '2', '12', '1', 'ly02- 天然气', '<p>当大家还在为天然气紧缺而着急的时候，在中国沉寂多年的煤层气悄悄在崛起。煤层气就是大家口中的“瓦斯”，是煤的伴生矿产资源，热值与天然气相当，可与天然气混输混用，燃烧后十分洁净，是上好的工业、化工、发电和居民生活燃料。我国煤层气储量十分丰富，但由于技术限制等因素一直以来十分低调，去年随着连续多个千亿方大气田的发现，煤层气终于迎来了“主角”时刻。</p><p></p><p>煤层气的勘探可以追溯到2006年，华北油田成立了山西煤层气分公司，在当年钻井超过100口，开始正式进入煤层气领域的勘探开发，并于2009年率先实现了中石油煤层气开发的商业化运营。此后华北油田在晋东南沁水盆地累计提交探明煤层气地质储量2131亿立方米，建成了国内第一个数字化、规模化整装煤层气田。目前，华北油田在沁水盆地煤层气的探明储量有2800多亿立方米，产量达到40多亿立方米。</p><p></p><p>沁水盆地的煤层气已经形成了上下游一体化产业格局，接下来华北油田煤层气的开发将进入新阶段：沁水高煤阶做大，河北大成的中煤阶和内蒙的低煤阶取得突破，远景目标是突破40亿方商品气量。对于华北油田而言，煤层气的突破，将成为接下来油田发展的重要部分，有望在未来十年左右实现油田能源结构的彻底转变。</p><p></p><p>中国的煤层气资源的勘探和开采潜力远比现在所表现出来的惊人，中国埋深2000米以内的煤层气资源达36.81万亿方，居世界第三位。美国能源情报署预测，到2040年，中国煤层气产量将占到全国天然气总产量的25%。但在过去的十年中，受制于技术、经验等因素，煤层气的勘探开发一直没有太大的进展，近两年我国多地勘探到巨量的煤层气资源，并实施开采，进行商用。</p><p></p><p>2017年9月，新疆乌鲁木齐市达坂城区发现可开采大型煤层气田，预测储量1000亿立方米，现已进入开采阶段。11月，高县文江地区的煤层气参数井获高产工业气流，标志着四川南部煤层气地质调查取得重大突破。初步估算，川南地区煤系气资源量约4500亿立方米，具有极好的勘查开发前景。</p><p></p><p>良好的资源前景当然还需政策扶持，国家进一步提高了“十三五”期间煤层气的补贴。此外，以山西为代表的资源大省还开放了一批煤层气探矿权，引进社会资本，推动煤层气的发展。这一切都预示着曾经默默无闻的煤层气即将登上中国的能源舞台。</p>', '天然气', '缺而着急的时候，在中国沉寂多年的煤层气悄悄在崛起。煤层气就是大家口中的“瓦斯”，是煤的伴生矿产资源，热值与天然气相当，可与天然气混输混用，燃烧后十分洁净，是上好的工业、化工、发电和居民生活燃料。我国煤层气储量十分丰富，但由于技术限制等因素一直以来十分低调，去年随着连续多个千亿方大气田的发现，煤层气终于迎来了“主角”时刻。\n\n煤层气的勘探可以追溯到2006年，华北油田成立了山西煤层气分公司，在当年', 'http://www_cxbs_net/Ls_dir/2020-10/41b4bab4aa.jpg', '0', '1', '36', '0', '0', '0', '1603705317', '1603705317', '1603791836', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"天然气.jpg\",\"sort\":\"0\",\"size\":\"104389\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/73a57faf5f.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('25', '2', '15', '1', 'ly03新能源煤', '<p>中国是富煤、缺油、少气的国家，长期以来形成了以煤为主要能源的消费结构，在相当长的时间里，我们主要依靠煤和化石能源发展经济，而化石能源需求增长与依赖进口已形成压力，《老柳说车》根据2012年中国水利水电出版社出版的《中国新能源》中查到的数据：目前（2012年）中国石油大量进口，对外依存度高达66.8%。中东、北非局势不稳（2020年美、伊局势雾里看花）进口渠道不平坦，美国在全球开展能源圈地运动，我国开拓海外屡遭遏制。 国内占能源消费70%的煤储采比为35年，石油仅9.9年，天然气开采主要在新疆，虽然储采比29年，但使用成本太高，消费比重仅为4.4%，水电仅能支撑5%。所以发展新能源，调整国家能源战略布局是迫切需要。</p><p>目前我国新能源发展中有问题还在解决，新能源汽车的消费结构在非限牌城市还没有形成，很多短板有待进行一步修正。</p><p></p><p>当电池替代汽车作为汽车动力能源，汽车不再是汽车，将变成一种电子产品，构造更简单，进入门槛更低。部分车企一拥而上，抢占地盘，出现了骗取国家补贴等不和谐现象。</p><p></p><p>大势不可逆，随着国家发改委对汽车产业投资政策的调整，这些不和谐现象会被历史车轮无情碾压。</p><p></p><p>所以，中国发展新能源的核心问题是煤，煤是我们的主要能源，建构以煤为主体的消费结构，可以摆脱我们对化石能源的依赖。</p>', '', '大势不可逆，随着国家发改委对汽车产业投资政策的调整，这些不和谐现象会被历史车轮无情碾压。\n\n\n\n所以，中国发展新能源的核心问题是煤，煤是我们的主要能源，建构以煤为主体的消费结构，可以摆脱我们对化石能源的依赖。', 'http://www_cxbs_net/Ls_dir/2020-10/f4e44d4f4f.jpg', '0', '1', '29', '0', '0', '0', '1603791340', '1603791340', '0', '101.86.179.56', null, '0', null, 'YS-LN0888', '[{\"title\":\"煤2.jpg\",\"sort\":\"0\",\"size\":\"78951\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/dc08fdcc0d.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('26', '2', '16', '1', 'ly02石油', '<p>石油是工业名词，是相对矿产资源而言，通常所说的石油工业，是一种矿产资源工业。在石油勘探过程中，根据勘探程度和探明情况，计算并确定石油储量。石油储量是地质勘探成果，是一种待开发的原始矿产资源量。 [5] </p><p>原油是埋藏在岩石地层里被开采出来的石油，保持着其原有的物理化学形态，是石油工业的初级产品，实现了其使用价值，是油田开发的成果，原油产量是一种已经开发的矿产资源产量。 [5] </p><p>石油一词多用于说明油层渗透率、孔隙度及油藏品味。而原油一词多用于国家统计的原油产量统计数字、评价原油理化性质及用于说明采收率、采出程度及采油速度。 [5] </p><p>石油作为矿产资源是指含水、含气的油，而原油作为一种工业产品，其中的水、气已从油中分离出来，是一种合格的工业产品。</p><p>具有代表性的大庆石油属低硫石蜡基石油，已开采酌石油以低硫石蜡基居多。这种石油，硫含量低，含蜡量高，凝点高，能生产出优质的煤油、柴油、溶剂油、润滑油及商品石蜡，直馏汽油的感铅性好。 [1] </p><p>有的石油硫含量高，胶质含量高，属含硫石蜡基。其直馏汽油馏分产率高，感铅性也好。柴油馏分的十六烷值高，闪点高，硫含量高，酸度大，经精制后可生产轻柴油与专用柴油。润滑油馏分中，有一部分组分的粘度指数在90以上，是生产内燃机油的良好的原料。 [6] </p><p>有的石油硫含量低，含蜡量较高，属低硫环烷一中间基。其汽油馏分感铅性好，且也富含环烷烃与芳香烃，故也是催化重整的良好原料。柴油馏分的凝点及硫含量均较低，酸度较大，产品需碱洗。减压渣油经氧化后可生产石油建筑沥青。 [6] </p><p>另有些低凝石油硫含量低、含蜡量也低，属低硫中间基。适于生产一些特殊性能的低凝产品，同时还可提取环烷酸是不可多得的宝贵资源。 [1]</p>', '石油', '石油作为矿产资源是指含水、含气的油，而原油作为一种工业产品，其中的水、气已从油中分离出来，是一种合格的工业产品。', 'http://www_cxbs_net/Ls_dir/2020-10/8280224407.jpg', '0', '1', '32', '0', '0', '0', '1603792005', '1603792005', '0', '101.86.179.56', null, '0', null, 'YS-LN0888', '[{\"title\":\"石油.jpg\",\"sort\":\"0\",\"size\":\"57382\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/3400f0f07f.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('27', '2', '14', '1', 'ly02风能', '<p>厄尔尼诺发威，冬季天候异常暖和，今年可能是欧洲史上最热的一年。德国用电需求大减，风力发电量又飙升，供电过剩使得今年圣诞节的电费可能降到零元以下，到时用一整天的电都不用花半毛钱，电厂还得倒贴。</p><p></p><p>彭博社22日报导，外界预估，圣诞节当天，德国风力发电量将为过去一个月平均值的两倍。与此同时，欧洲气候温暖，用电量将减少。能源谘询商MarkedskraftDeutschlandGmbH估计，风力发电量增加，要是燃煤和天然气发电厂不停止供电，电费或许会降到零元以下，可能会维持好几小时、甚至数天之久。</p><p></p><p>圣诞假期，估计德国的风力发电量约在10GW~20GW(gigawatt/十亿瓦)之间，气温则比往年高出摄氏8度。1GW可供应200万户的家庭用电。德国经济部和能源游说团体BDEW数据显示，德国的可更新能源可望供给当地三分之一用电，比去年增加20%。</p><p></p><p>全球各国今年底将在巴黎会议商讨气候变迁协定，国际能源署(IEA)估计，如果各国能落实承诺，风力、太阳能等可更新能源，将在15年内取代煤炭，成为最主要的电力来源。</p>', '风能', '厄尔尼诺发威，冬季天候异常暖和，今年可能是欧洲史上最热的一年。德国用电需求大减，风力发电量又飙升，供电过剩使得今年圣诞节的电费可能降到零元以下，到时用一整天的电都不用花半毛钱，电厂还得倒贴。\n\n\n\n彭博社22日报导，外界预估', 'http://www_cxbs_net/Ls_dir/2020-10/2932249242.jpg', '0', '1', '90', '0', '0', '0', '1603792279', '1603792279', '0', '101.86.179.56', null, '0', null, 'YS-LN0888', '[{\"title\":\"风能1.jpg\",\"sort\":\"0\",\"size\":\"40693\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/faf6a6dd6d.jpg\"}]');
INSERT INTO `cx_cms_content_2` VALUES ('28', '2', '14', '1', 'ly03风能', '<p>厄尔尼诺发威，冬季天候异常暖和，今年可能是欧洲史上最热的一年。德国用电需求大减，风力发电量又飙升，供电过剩使得今年圣诞节的电费可能降到零元以下，到时用一整天的电都不用花半毛钱，电厂还得倒贴。</p><p></p><p>彭博社22日报导，外界预估，圣诞节当天，德国风力发电量将为过去一个月平均值的两倍。与此同时，欧洲气候温暖，用电量将减少。能源谘询商MarkedskraftDeutschlandGmbH估计，风力发电量增加，要是燃煤和天然气发电厂不停止供电，电费或许会降到零元以下，可能会维持好几小时、甚至数天之久。</p><p></p><p>圣诞假期，估计德国的风力发电量约在10GW~20GW(gigawatt/十亿瓦)之间，气温则比往年高出摄氏8度。1GW可供应200万户的家庭用电。德国经济部和能源游说团体BDEW数据显示，德国的可更新能源可望供给当地三分之一用电，比去年增加20%。</p><p></p><p>全球各国今年底将在巴黎会议商讨气候变迁协定，国际能源署(IEA)估计，如果各国能落实承诺，风力、太阳能等可更新能源，将在15年内取代煤炭，成为最主要的电力来源。</p>', '风能', '圣诞假期，估计德国的风力发电量约在10GW~20GW(gigawatt/十亿瓦)之间，气温则比往年高出摄氏8度。1GW可供应200万户的家庭用电。德国经济部和能源游说团体BDEW数据显示，德国的可更新能源可望供给当地三分之一用电，比去年增加20%。', 'http://www_cxbs_net/Ls_dir/2020-10/8bfbbf8fb8.jpg', '0', '1', '78', '0', '0', '0', '1603792297', '1603792297', '1603792324', '101.86.179.56', '101.86.179.56', '0', null, 'YS-LN0888', '[{\"title\":\"风能2.jpg\",\"sort\":\"0\",\"size\":\"85258\",\"uri\":\"http:\\/\\/www_cxbs_net\\/Ls_dir\\/2020-10\\/e3e4884888.jpg\"}]');

-- ----------------------------
-- Table structure for cx_cms_filed
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_filed`;
CREATE TABLE `cx_cms_filed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `sql_file` varchar(50) NOT NULL DEFAULT '' COMMENT '字段名',
  `sql_type` varchar(80) NOT NULL DEFAULT '' COMMENT '储存类型',
  `form_title` varchar(255) NOT NULL DEFAULT '' COMMENT '表单标题',
  `form_text` varchar(20) DEFAULT NULL COMMENT '表单提示',
  `form_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填',
  `form_required_list` varchar(100) DEFAULT NULL COMMENT '验证规则,非必填无效',
  `form_unit` varchar(80) DEFAULT NULL COMMENT '字段单位',
  `form_default` varchar(255) DEFAULT NULL COMMENT '默认值 ',
  `form_type` varchar(30) DEFAULT NULL COMMENT '表单类型',
  `form_class` varchar(50) DEFAULT NULL COMMENT '表单样式',
  `form_geturi` varchar(255) DEFAULT NULL,
  `form_geturitype` tinyint(2) DEFAULT NULL,
  `form_data` text COMMENT '多选项列表',
  `form_tip` varchar(150) DEFAULT NULL COMMENT '字段说明',
  `form_group` varchar(80) DEFAULT NULL COMMENT '字段分组',
  `form_js` text COMMENT '字段JS',
  `form_edit` tinyint(4) DEFAULT NULL,
  `group_see` varchar(255) DEFAULT NULL COMMENT '允许查看的会员组，默认全部可看，超级管理员可看',
  `group_edit` varchar(255) DEFAULT NULL COMMENT '允许编辑的会员组',
  `setstatus` tinyint(2) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值 ',
  `admin_list_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '后台列表是否显示，0|不显示，1|显示',
  `list_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '列表是否显示，0|不显示，1|显示',
  `cont_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '内容页是否显示，0|不显示，1|显示',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_cms_filed
-- ----------------------------
INSERT INTO `cx_cms_filed` VALUES ('1', '1', 'title', 'varchar(255) DEFAULT NULL', '标题', null, '1', null, null, null, 'text', null, null, null, null, null, null, null, null, null, null, null, '1', '100', '0', '1', '1', '1603697250', '0');
INSERT INTO `cx_cms_filed` VALUES ('2', '1', 'content', 'mediumtext DEFAULT NULL', '内容', null, '0', null, null, null, 'editor', null, null, null, null, null, null, null, null, null, null, null, '1', '99', '0', '1', '1', '1603697250', '0');
INSERT INTO `cx_cms_filed` VALUES ('3', '2', 'title', 'varchar(255) DEFAULT NULL', '标题', null, '1', null, null, null, 'text', null, null, null, null, null, null, null, null, null, null, null, '1', '100', '0', '1', '1', '1603699011', '0');
INSERT INTO `cx_cms_filed` VALUES ('4', '2', 'content', 'mediumtext DEFAULT NULL', '内容', null, '0', null, null, null, 'editor', null, null, null, null, null, null, null, null, null, null, null, '1', '0', '0', '1', '1', '1603699011', '0');
INSERT INTO `cx_cms_filed` VALUES ('5', '2', 'cp_type', 'varchar(255) DEFAULT NULL', '产品型号', '', '0', '', '', '', 'text', '', '', '0', '', '', '', '', '0', null, null, '1', '1', '90', '1', '1', '1', '1603700098', '0');
INSERT INTO `cx_cms_filed` VALUES ('10', '2', 'imgdata', 'text DEFAULT NULL', '产品图片', '', '0', '', '', '', 'upload_imgarr', '', '', '0', '', '', '', '', '0', null, null, '1', '1', '80', '0', '0', '1', '1603704550', '0');

-- ----------------------------
-- Table structure for cx_cms_fupart
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_fupart`;
CREATE TABLE `cx_cms_fupart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `limit` int(11) NOT NULL DEFAULT '0',
  `title_num` int(11) NOT NULL DEFAULT '0',
  `cont_num` int(11) NOT NULL DEFAULT '0',
  `group_see` varchar(255) DEFAULT NULL,
  `group_edit` varchar(255) DEFAULT NULL,
  `pid_see` tinyint(1) NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  `banber` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `jumpurl` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `password` varchar(20) DEFAULT NULL,
  `temp_late` varchar(255) DEFAULT NULL,
  `temp_head` varchar(255) DEFAULT NULL,
  `temp_list` varchar(255) DEFAULT NULL,
  `temp_cont` varchar(255) DEFAULT NULL,
  `temp_foot` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `order` varchar(255) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_cms_fupart
-- ----------------------------
INSERT INTO `cx_cms_fupart` VALUES ('1', '0', '热门产品推荐', '0', '0', '0', '', '', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603700432', '0');
INSERT INTO `cx_cms_fupart` VALUES ('2', '1', '本周最热', '0', '0', '0', '', '', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603700448', '0');

-- ----------------------------
-- Table structure for cx_cms_fupart_article
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_fupart_article`;
CREATE TABLE `cx_cms_fupart_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fuid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `del_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_cms_fupart_article
-- ----------------------------

-- ----------------------------
-- Table structure for cx_cms_model
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_model`;
CREATE TABLE `cx_cms_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '模型名称',
  `futitle` varchar(50) DEFAULT NULL,
  `see_group` varchar(255) DEFAULT NULL,
  `edit_group` varchar(255) DEFAULT NULL,
  `see_add` tinyint(1) DEFAULT NULL,
  `see_comment` tinyint(1) DEFAULT NULL,
  `see_description` tinyint(1) DEFAULT NULL,
  `see_keyword` tinyint(1) DEFAULT NULL,
  `see_picurl` tinyint(1) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `addtime` int(11) DEFAULT NULL,
  `del_time` int(11) DEFAULT '0',
  `order` tinyint(4) DEFAULT NULL,
  `order_money` varchar(255) DEFAULT NULL,
  `order_group` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_cms_model
-- ----------------------------
INSERT INTO `cx_cms_model` VALUES ('1', '文章模型', '文章', '', '', '1', '1', '1', '1', '1', '0', '1', '1603697249', '0', null, '', '0');
INSERT INTO `cx_cms_model` VALUES ('2', '产品模型', '产品', '', '', '1', '1', '1', '1', '1', '0', '1', '1603699010', '0', null, '', '0');

-- ----------------------------
-- Table structure for cx_cms_part
-- ----------------------------
DROP TABLE IF EXISTS `cx_cms_part`;
CREATE TABLE `cx_cms_part` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `class` tinyint(1) NOT NULL DEFAULT '1',
  `limit` int(11) NOT NULL DEFAULT '0',
  `title_num` int(11) NOT NULL DEFAULT '0',
  `cont_num` int(11) NOT NULL DEFAULT '0',
  `group_uid` varchar(255) DEFAULT NULL,
  `group_see` varchar(255) DEFAULT NULL,
  `group_edit` varchar(255) DEFAULT NULL,
  `pid_see` tinyint(1) NOT NULL DEFAULT '0',
  `comment_see` tinyint(1) NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  `banber` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `jumpurl` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `password` varchar(20) DEFAULT NULL,
  `temp_late` varchar(255) DEFAULT NULL,
  `temp_head` varchar(255) DEFAULT NULL,
  `temp_list` varchar(255) DEFAULT NULL,
  `temp_cont` varchar(255) DEFAULT NULL,
  `temp_foot` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `order` varchar(255) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_cms_part
-- ----------------------------
INSERT INTO `cx_cms_part` VALUES ('1', '1', '0', '新闻资讯', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603697285', '0');
INSERT INTO `cx_cms_part` VALUES ('2', '1', '1', '行业动态', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603697359', '0');
INSERT INTO `cx_cms_part` VALUES ('3', '1', '1', '公司新闻', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603697374', '0');
INSERT INTO `cx_cms_part` VALUES ('4', '2', '0', '新能源产品', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603699031', '0');
INSERT INTO `cx_cms_part` VALUES ('5', '1', '0', '研发团队', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', 'tuandui.htm', '', '', '0', 'top desc,jian desc,addtime desc', '1603699064', '0');
INSERT INTO `cx_cms_part` VALUES ('7', '1', '0', '关于我们', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603699092', '0');
INSERT INTO `cx_cms_part` VALUES ('8', '1', '7', '公司介绍', '1', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '<p>公司拥有自主知识产权的常压循环流化床气化技术，借助于中国科学院工程热物理研究所在循环流化床技术方面的优势，</p><p>开发和推广循环流化床煤气化技术在化工、建材、冶金、环保等领域的应用，</p><p>目标是为工业燃气客户提供经济适用的清洁能源解决方案。</p><p>公司有二十年的循环流化床气化技术研发和工艺设计、</p><p>工程化经验，并在冶金、建材等行业成功应用三十多台。</p><p>作为煤制工业燃气技术的领航者，中科清能将以“提供清洁能源解决方案”为己任，致力为工业燃气领域节能、减排、增效做出贡献！</p>', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603700221', '0');
INSERT INTO `cx_cms_part` VALUES ('9', '1', '7', '联系我们', '1', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '<figure class=\"table\"><table><tbody><tr><td style=\"padding:5px;\" rowspan=\"2\"><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/part/2020-10/28ae28b68b.png\"></figure><p>&nbsp;</p></td><td style=\"padding:5px;\"><span class=\"text-huge\">公司地址</span></td></tr><tr><td><span style=\"color:hsl(0,0%,60%);\">上海市浦东新区惠南镇绿地峰汇商务广场B座1710</span></td></tr><tr><td style=\"padding:5px;\" rowspan=\"2\"><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/part/2020-10/1b1b4eebe6.png\"></figure><p>&nbsp;</p></td><td style=\"padding:5px;\"><span class=\"text-huge\">联系电话</span></td></tr><tr><td style=\"padding:5px;\"><span style=\"color:hsl(0,0%,60%);\">021-80158202</span></td></tr><tr><td style=\"padding:5px;\" rowspan=\"2\"><figure class=\"image\"><img src=\"http://www_cxbs_net/Ls_dir/part/2020-10/71e2210081.png\"></figure><p>&nbsp;</p></td><td><span class=\"text-huge\">电子邮箱</span></td></tr><tr><td style=\"padding:5px;\">admin@cxbs.net</td></tr></tbody></table></figure>', '', '', '', 'lianxi.htm', '', '', '0', 'top desc,jian desc,addtime desc', '1603700258', '0');
INSERT INTO `cx_cms_part` VALUES ('10', '1', '7', '荣誉资质', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', 'zizhi.htm', '', '', '0', 'top desc,jian desc,addtime desc', '1603700278', '0');
INSERT INTO `cx_cms_part` VALUES ('11', '2', '4', '太阳能', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701311', '0');
INSERT INTO `cx_cms_part` VALUES ('12', '2', '4', '天然气', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701321', '0');
INSERT INTO `cx_cms_part` VALUES ('13', '2', '4', '水能', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701333', '0');
INSERT INTO `cx_cms_part` VALUES ('14', '2', '4', '风能', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701344', '0');
INSERT INTO `cx_cms_part` VALUES ('15', '2', '4', '煤', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701358', '0');
INSERT INTO `cx_cms_part` VALUES ('16', '2', '4', '石油', '0', '0', '0', '0', '', '', '', '1', '1', '', '', '1', '', '', '', '', '', '', '', '', '', '0', 'top desc,jian desc,addtime desc', '1603701427', '0');

-- ----------------------------
-- Table structure for cx_comment
-- ----------------------------
DROP TABLE IF EXISTS `cx_comment`;
CREATE TABLE `cx_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `aid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `model` varchar(30) DEFAULT '0',
  `jian` tinyint(1) DEFAULT '0',
  `type` varchar(10) DEFAULT '0',
  `content` text,
  `imgs` text,
  `status` tinyint(4) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `addip` varchar(20) DEFAULT NULL,
  `oid` varchar(80) DEFAULT NULL COMMENT '订单编号',
  PRIMARY KEY (`id`),
  KEY `model` (`model`),
  KEY `uid` (`uid`),
  KEY `mid` (`mid`),
  KEY `aid` (`aid`),
  KEY `pid` (`pid`),
  KEY `addtime` (`addtime`),
  KEY `jian` (`jian`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_comment
-- ----------------------------

-- ----------------------------
-- Table structure for cx_config
-- ----------------------------
DROP TABLE IF EXISTS `cx_config`;
CREATE TABLE `cx_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `conf` varchar(50) NOT NULL DEFAULT '',
  `conf_value` text,
  `class` varchar(30) NOT NULL DEFAULT '',
  `form_type` varchar(50) NOT NULL DEFAULT '',
  `form_required` tinyint(4) NOT NULL DEFAULT '0',
  `form_required_list` varchar(80) DEFAULT NULL,
  `form_text` varchar(50) DEFAULT NULL,
  `form_unit` varchar(50) DEFAULT NULL,
  `form_default` varchar(80) DEFAULT NULL,
  `form_data` text,
  `form_tip` varchar(80) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conf` (`conf`),
  KEY `status` (`status`),
  KEY `sort` (`sort`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_config
-- ----------------------------
INSERT INTO `cx_config` VALUES ('1', '网站名称', 'web_title', 'WORMCMS基础版', '1', 'text', '1', '', '', '', '', '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('2', '网站简称', 'web_title_min', '开发测试网站', '1', 'text', '0', null, '', '', '', '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('3', '网站关键词', 'web_keywords', '开发测试网站', '1', 'text', '0', null, '多个关键词请用英文逗号隔开', '', null, '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('4', '网站描述', 'web_description', '开发测试网站', '1', 'textarea', '0', null, '', '', '', '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('5', '系统LOGO', 'web_logo', '/upload_file/webdb/4c8ca19888.png', '1', 'upload_img', '0', null, '', '', '', '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('6', '网址', 'web_url', 'http://lili.dome.wormcms.com', '1', 'text', '1', 'url', '', '', '', '', '格式为：http://www.wormcms.com', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('7', '网站关闭', 'web_open', '1', '1', 'radio', '1', null, '', '', '1', '1|开启\n0|关闭', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('8', '关闭原因', 'web_openwhy', '', '1', 'textarea', '0', null, '', '', '', '', '', '1', '50', '0');
INSERT INTO `cx_config` VALUES ('9', '上传目录', 'web_updir', 'upload_file', '1', 'text', '1', null, '', '', 'upload_files', '', '建议不要修改', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('10', 'ICP备案号', 'web_icp', '沪ICP备17000864号', '1', 'text', '0', null, '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('11', '公安备案', 'web_policeicp', '', '1', 'text', '0', null, '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('12', '底部版权', 'web_footbq', '开发测试网站', '1', 'textarea', '0', null, '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('13', '授权码', 'web_warrant', 'T9aBDSnBRRGzK3KWSj3', '1', 'text', '0', null, '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('14', '后台验证码', 'web_adminyz', '1', '1', 'radio', '1', null, '', '', '0', '0|不启用\n1|启用', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('15', 'SEO标题', 'cms_title', 'SEO标题', 'cms', 'text', '0', null, null, null, null, null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('16', 'SEO关键字', 'cms_keywords', '', 'cms', 'text', '0', null, null, null, null, null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('17', 'SEO描述', 'cms_description', '', 'cms', 'textarea', '0', null, null, null, null, null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('18', '是否启用', 'cms_status', '1', 'cms', 'radio', '0', null, null, null, null, null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('19', '是否启用订单', 'cms_order', '0', 'cms', 'radio', '0', null, null, null, null, null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('20', '列表显示', 'cms_limit', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('21', '辅栏目简介字数', 'cms_fu_cont_num', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('22', '辅栏目标题字数', 'cms_fu_title_num', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('23', '辅栏目显示', 'cms_fu_limit', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('24', '列表简介字数', 'cms_cont_num', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('25', '列表标题字数', 'cms_title_num', '0', 'cms', 'number', '1', 'number', null, null, '0', null, null, '1', '0', '0');
INSERT INTO `cx_config` VALUES ('26', '评论审核', 'auto_comment', '1', '1', 'radio', '0', '', '', '', '1', '0|管理员手动审核 \n1|自动审核', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('27', '默认BANNER', 'cms_banner', '', 'cms', 'upload_img', '0', '', '', '', '', '', '', '1', '70', '0');
INSERT INTO `cx_config` VALUES ('28', '网站风格', 'web_template', 'default', '1', 'text', '0', '', '', '', '', '', '', '1', '20', '0');
INSERT INTO `cx_config` VALUES ('29', '会员注册', 'user_open', '0', '3', 'radio', '0', '', '', '', '1', '1|开启注册\\n 0|关闭注册', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('30', '短信验证', 'user_checkphone', '1', '3', 'radio', '0', '', '', '', '1', '1|短信验证\\n 0|不启用短信', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('31', '登录验证码', 'user_homeyz', '0', '3', 'radio', '1', '', '', '', '0', '0|不启用\\n 1|启用', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('32', '自动审核', 'user_status', '1', '3', 'radio', '0', '', '', '', '1', '1|自动通过审核\\n 0|手动审核', '', '1', '30', '0');
INSERT INTO `cx_config` VALUES ('33', '运营商', 'sms_isp', '1', '2', 'radio', '1', '', '', '', '1', ' 1|阿里云\\n 2|腾讯云', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('34', 'AccessKey', 'sms_key', 'werwer', '2', 'text', '0', '', '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('35', 'AccessSecret', 'sms_secret', '234234', '2', 'text', '0', '', '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('36', '验证码短信key', 'sms_reg', 'sdfwer', '2', 'text', '0', '', '', '', '', '', '', '1', '0', '0');
INSERT INTO `cx_config` VALUES ('37', '短信签名', 'sms_sing', 'werwer', '2', 'text', '0', '', '', '', '', '', '', '1', '0', '0');

-- ----------------------------
-- Table structure for cx_config_class
-- ----------------------------
DROP TABLE IF EXISTS `cx_config_class`;
CREATE TABLE `cx_config_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '权限分类名称',
  `icon` varchar(80) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL COMMENT '权限链接地址',
  `sort` mediumint(9) DEFAULT '0' COMMENT '排序值',
  `status` tinyint(4) DEFAULT '1' COMMENT '是否启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_config_class
-- ----------------------------
INSERT INTO `cx_config_class` VALUES ('1', '基础设置', 'cx-icon cx-iconapp', '', '0', '1');
INSERT INTO `cx_config_class` VALUES ('2', '短信配置', 'cx-icon cx-iconmail', '', '0', '1');
INSERT INTO `cx_config_class` VALUES ('3', '会员配置', 'cx-icon cx-iconquanxian2', '', '0', '1');
INSERT INTO `cx_config_class` VALUES ('4', '支付宝配置', 'cx-icon cx-iconjifen', '', '0', '1');
INSERT INTO `cx_config_class` VALUES ('5', '微信配置', '', '', '0', '0');

-- ----------------------------
-- Table structure for cx_config_up
-- ----------------------------
DROP TABLE IF EXISTS `cx_config_up`;
CREATE TABLE `cx_config_up` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_no` varchar(50) DEFAULT NULL,
  `edition_uri` varchar(255) DEFAULT NULL,
  `cont` mediumtext,
  `files` mediumtext,
  `status` int(11) DEFAULT '0',
  `addtime` int(11) DEFAULT '0',
  `edittime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_config_up
-- ----------------------------
INSERT INTO `cx_config_up` VALUES ('1', '1.1.07', '', '', null, '1', '1607070591', '1607070591');

-- ----------------------------
-- Table structure for cx_form_content_1
-- ----------------------------
DROP TABLE IF EXISTS `cx_form_content_1`;
CREATE TABLE `cx_form_content_1` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `uid` varchar(100) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` varchar(255) NOT NULL DEFAULT '0' COMMENT '标题',
  `content` text COMMENT '内容',
  `res_content` text COMMENT '回复内容',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核 0 =》 待审核，1 =》 通过，2=》拒绝|一般用不上',
  `jian` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐,0=>不推荐，1=>推荐,2以上推荐为分类级以上推荐',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `addip` varchar(20) NOT NULL DEFAULT '0' COMMENT '添加IP',
  `del_time` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`),
  KEY `jian` (`jian`),
  KEY `addtime` (`addtime`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='留言表单模型表';

-- ----------------------------
-- Records of cx_form_content_1
-- ----------------------------
INSERT INTO `cx_form_content_1` VALUES ('1', '1', '0', 'asdasd', 'sdfsdfsdfsd', null, '0', '0', '1603949925', '0', '101.86.179.56', '0', '15879975960', '15879975960@qq.com');
INSERT INTO `cx_form_content_1` VALUES ('2', '1', '0', 'sdfsdfdsf', 'sdfsdfsdfsdfsd', null, '0', '0', '1603949988', '0', '101.86.179.56', '0', '15879975960', '1774315265@qq.com');
INSERT INTO `cx_form_content_1` VALUES ('4', '1', '0', 'werwer', 'werwerwerwer', null, '0', '0', '1603950366', '0', '101.86.179.56', '0', '15879975960', '1774315265@qq.com');
INSERT INTO `cx_form_content_1` VALUES ('5', '1', '0', '啊实打实', '阿三大苏打实打实', null, '0', '0', '1603951810', '0', '101.86.179.56', '0', '15879975960', '1774315265@qq.com');
INSERT INTO `cx_form_content_1` VALUES ('6', '1', '0', '啊飒飒', '啊飒飒', null, '0', '0', '1603952137', '0', '101.86.179.56', '0', '15879975960', '1774315265@qq.com');
INSERT INTO `cx_form_content_1` VALUES ('7', '1', '0', '文本文档', '飒飒', null, '0', '0', '1603952449', '0', '101.86.179.56', '0', '15879975960', '1774315265@qq.com');

-- ----------------------------
-- Table structure for cx_form_filed
-- ----------------------------
DROP TABLE IF EXISTS `cx_form_filed`;
CREATE TABLE `cx_form_filed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `sql_file` varchar(50) NOT NULL DEFAULT '0' COMMENT '字段名',
  `sql_type` varchar(80) NOT NULL DEFAULT '0' COMMENT '储存类型',
  `form_title` varchar(255) NOT NULL DEFAULT '0' COMMENT '表单标题',
  `form_text` varchar(20) DEFAULT NULL COMMENT '表单提示',
  `form_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填',
  `form_required_list` varchar(100) DEFAULT NULL COMMENT '验证规则,非必填无效',
  `form_unit` varchar(80) DEFAULT NULL COMMENT '字段单位',
  `form_default` varchar(255) DEFAULT NULL COMMENT '默认值 ',
  `form_type` varchar(30) DEFAULT NULL COMMENT '表单类型',
  `form_class` varchar(50) DEFAULT NULL COMMENT '表单样式',
  `form_geturi` varchar(255) DEFAULT NULL,
  `form_geturitype` tinyint(2) DEFAULT NULL,
  `form_data` text COMMENT '多选项列表',
  `form_tip` varchar(150) DEFAULT NULL COMMENT '字段说明',
  `form_group` varchar(80) DEFAULT NULL COMMENT '字段分组',
  `form_js` text COMMENT '字段JS',
  `form_edit` tinyint(4) DEFAULT NULL,
  `group_see` varchar(255) DEFAULT NULL COMMENT '允许查看的会员组，默认全部可看，超级管理员可看',
  `group_edit` varchar(255) DEFAULT NULL COMMENT '允许编辑的会员组',
  `setstatus` tinyint(2) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值 ',
  `admin_list_show` tinyint(4) DEFAULT '0',
  `list_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '列表是否显示，0|不显示，1|显示',
  `cont_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '内容页是否显示，0|不显示，1|显示',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_form_filed
-- ----------------------------
INSERT INTO `cx_form_filed` VALUES ('1', '1', 'title', 'varchar(255) DEFAULT NULL', '需要资询的问题', '', '1', '', '', '', 'text', '', '', '0', '', '', '', '', '0', null, null, '1', '1', '100', '0', '1', '1', '1603946192', '0');
INSERT INTO `cx_form_filed` VALUES ('2', '1', 'content', 'mediumtext DEFAULT NULL', '内容', null, '0', null, null, null, 'editor', null, null, null, null, null, null, null, null, null, null, null, '1', '0', '0', '1', '1', '1603946192', '0');
INSERT INTO `cx_form_filed` VALUES ('3', '1', 'phone', 'varchar(255) DEFAULT NULL', '电话', '', '0', '', '', '', 'text', '', '', '0', '', '', '', '', '0', null, null, '1', '1', '80', '0', '0', '1', '1603949702', '0');
INSERT INTO `cx_form_filed` VALUES ('4', '1', 'email', 'varchar(255) DEFAULT NULL', '邮箱', '', '0', '', '', '', 'text', '', '', '0', '', '', '', '', '0', null, null, '1', '1', '78', '0', '0', '1', '1603949725', '0');

-- ----------------------------
-- Table structure for cx_form_model
-- ----------------------------
DROP TABLE IF EXISTS `cx_form_model`;
CREATE TABLE `cx_form_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '0' COMMENT '模型名称',
  `cont` text,
  `see_group` varchar(255) DEFAULT NULL,
  `add_group` varchar(255) DEFAULT NULL,
  `tourist` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_form_model
-- ----------------------------
INSERT INTO `cx_form_model` VALUES ('1', '留言表单', '', '', '', '1', '0', '1', '1603946192', '0');

-- ----------------------------
-- Table structure for cx_link
-- ----------------------------
DROP TABLE IF EXISTS `cx_link`;
CREATE TABLE `cx_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` int(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_link
-- ----------------------------
INSERT INTO `cx_link` VALUES ('1', '1', '逸点家具', '/', 'http://www_cxbs_net/Ls_dir/2020-10/8a9adee9d9.png', '0', '1');
INSERT INTO `cx_link` VALUES ('2', '1', '逸点蛋糕', '/', 'http://www_cxbs_net/Ls_dir/2020-10/81a2a82922.png', '0', '1');
INSERT INTO `cx_link` VALUES ('3', '1', '逸点茶叶', '/', 'http://www_cxbs_net/Ls_dir/2020-10/dc9636699c.png', '0', '1');
INSERT INTO `cx_link` VALUES ('4', '1', '逸点手办', '/', 'http://www_cxbs_net/Ls_dir/2020-10/3000774703.png', '0', '1');
INSERT INTO `cx_link` VALUES ('5', '1', '逸点茶具', '/', 'http://www_cxbs_net/Ls_dir/2020-10/550526df52.png', '0', '1');
INSERT INTO `cx_link` VALUES ('6', '1', '逸点车配件', '/', 'http://www_cxbs_net/Ls_dir/2020-10/2112a12021.png', '0', '1');
INSERT INTO `cx_link` VALUES ('7', '1', '逸点家具定制', '/', 'http://www_cxbs_net/Ls_dir/2020-10/55b5bbc50b.png', '0', '1');
INSERT INTO `cx_link` VALUES ('8', '1', '逸点食品', '/', 'http://www_cxbs_net/Ls_dir/2020-10/afcaa5a9fa.png', '0', '1');
INSERT INTO `cx_link` VALUES ('9', '1', '逸点服饰', '/', 'http://www_cxbs_net/Ls_dir/2020-10/ff9f0c1ff0.png', '0', '1');
INSERT INTO `cx_link` VALUES ('10', '1', '逸点灯具', '/', 'http://www_cxbs_net/Ls_dir/2020-10/2ffd3ee3e2.png', '0', '1');
INSERT INTO `cx_link` VALUES ('11', '1', '逸点清洁', '/', 'http://www_cxbs_net/Ls_dir/2020-10/33315e1e8b.png', '0', '1');
INSERT INTO `cx_link` VALUES ('12', '1', '逸点首饰', '/', 'http://www_cxbs_net/Ls_dir/2020-10/a238372c36.png', '0', '1');
INSERT INTO `cx_link` VALUES ('13', '1', '焱凤技术', 'https://cxbs.net/', null, '0', '1');

-- ----------------------------
-- Table structure for cx_link_class
-- ----------------------------
DROP TABLE IF EXISTS `cx_link_class`;
CREATE TABLE `cx_link_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `del_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_link_class
-- ----------------------------
INSERT INTO `cx_link_class` VALUES ('1', '友情链接', '0', '1', '0');
INSERT INTO `cx_link_class` VALUES ('2', '合作伙伴', '0', '1', '0');

-- ----------------------------
-- Table structure for cx_log_operate
-- ----------------------------
DROP TABLE IF EXISTS `cx_log_operate`;
CREATE TABLE `cx_log_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(50) NOT NULL DEFAULT '0',
  `cont` varchar(150) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT '0',
  `addip` varchar(15) NOT NULL COMMENT '注册IP',
  `addtime` int(11) NOT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_log_operate
-- ----------------------------

-- ----------------------------
-- Table structure for cx_log_userlog
-- ----------------------------
DROP TABLE IF EXISTS `cx_log_userlog`;
CREATE TABLE `cx_log_userlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL DEFAULT '0',
  `cont` varchar(150) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT '0',
  `addip` varchar(15) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for cx_model_list
-- ----------------------------
DROP TABLE IF EXISTS `cx_model_list`;
CREATE TABLE `cx_model_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `keys` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `addtime` int(11) DEFAULT NULL,
  `del_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_model_list
-- ----------------------------
INSERT INTO `cx_model_list` VALUES ('1', '0', 'CMS模块', 'cms', 'cms', '1', '1583740913', '0');
INSERT INTO `cx_model_list` VALUES ('2', '1', '表单管理模块', 'form', 'form', '1', '1598250449', '0');

-- ----------------------------
-- Table structure for cx_nav
-- ----------------------------
DROP TABLE IF EXISTS `cx_nav`;
CREATE TABLE `cx_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` int(255) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `target` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_nav
-- ----------------------------
INSERT INTO `cx_nav` VALUES ('1', '1', '0', '网站首页', '/', '', '0', '0', '1');
INSERT INTO `cx_nav` VALUES ('2', '1', '0', '新能源产品', '/home/part-4.html', '', '0', '0', '1');
INSERT INTO `cx_nav` VALUES ('4', '1', '0', '研发团队', '/home/part-5.html', '', '0', '0', '1');
INSERT INTO `cx_nav` VALUES ('5', '1', '0', '新闻资讯', '/home/part-1.html', '', '0', '0', '1');
INSERT INTO `cx_nav` VALUES ('6', '1', '0', '关于我们', '/home/part-8.html', '', '0', '0', '1');

-- ----------------------------
-- Table structure for cx_nav_class
-- ----------------------------
DROP TABLE IF EXISTS `cx_nav_class`;
CREATE TABLE `cx_nav_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `level` tinyint(4) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `del_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_nav_class
-- ----------------------------
INSERT INTO `cx_nav_class` VALUES ('1', 'PC头部导航', '1', '0', '1', '0');
INSERT INTO `cx_nav_class` VALUES ('2', 'PC底部导航', '1', '0', '1', '0');

-- ----------------------------
-- Table structure for cx_sms
-- ----------------------------
DROP TABLE IF EXISTS `cx_sms`;
CREATE TABLE `cx_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `cont` mediumtext COMMENT '内容',
  `to_uid` int(11) NOT NULL COMMENT '接收人UID',
  `fo_uid` int(11) DEFAULT '0' COMMENT '发送人UID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `addtime` int(11) DEFAULT '0' COMMENT '发送时间',
  `endtime` int(1) DEFAULT '0' COMMENT '读取时间',
  `del_time` int(1) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='站内信';

-- ----------------------------
-- Records of cx_sms
-- ----------------------------

-- ----------------------------
-- Table structure for cx_sms_code
-- ----------------------------
DROP TABLE IF EXISTS `cx_sms_code`;
CREATE TABLE `cx_sms_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `phone` varchar(20) NOT NULL DEFAULT '0' COMMENT '手机号',
  `cont` varchar(255) DEFAULT NULL COMMENT '内容',
  `rescont` varchar(255) DEFAULT NULL COMMENT '失败原因',
  `title` varchar(80) DEFAULT NULL COMMENT '用途',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否成功',
  `addtime` int(11) DEFAULT '0' COMMENT '发送时间',
  `addip` varchar(32) DEFAULT '0' COMMENT '发送IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='短信';

-- ----------------------------
-- Records of cx_sms_code
-- ----------------------------

-- ----------------------------
-- Table structure for cx_special
-- ----------------------------
DROP TABLE IF EXISTS `cx_special`;
CREATE TABLE `cx_special` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '0',
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `banber` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `temp_late` varchar(255) DEFAULT NULL,
  `temp_head` varchar(255) DEFAULT NULL,
  `temp_list` varchar(255) DEFAULT NULL,
  `temp_foot` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `addtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_special
-- ----------------------------

-- ----------------------------
-- Table structure for cx_special_article
-- ----------------------------
DROP TABLE IF EXISTS `cx_special_article`;
CREATE TABLE `cx_special_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(50) DEFAULT NULL,
  `sid` int(11) DEFAULT '0',
  `aid` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `del_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_special_article
-- ----------------------------

-- ----------------------------
-- Table structure for cx_special_class
-- ----------------------------
DROP TABLE IF EXISTS `cx_special_class`;
CREATE TABLE `cx_special_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_special_class
-- ----------------------------

-- ----------------------------
-- Table structure for cx_user
-- ----------------------------
DROP TABLE IF EXISTS `cx_user`;
CREATE TABLE `cx_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `u_groupid` int(11) NOT NULL DEFAULT '0' COMMENT '用户组',
  `u_name` varchar(50) NOT NULL DEFAULT '0' COMMENT '用户名',
  `u_uniname` varchar(50) DEFAULT NULL COMMENT '用户昵称 ',
  `u_uname` varchar(50) DEFAULT NULL COMMENT '用户姓名',
  `u_sex` tinyint(4) DEFAULT '2' COMMENT '用户性别 ',
  `u_phone` varchar(20) DEFAULT NULL COMMENT '手机',
  `u_icon` varchar(255) DEFAULT NULL COMMENT '头像',
  `u_mail` varchar(80) DEFAULT NULL COMMENT '邮箱 ',
  `u_password` varchar(100) DEFAULT NULL COMMENT '密码',
  `u_paypassword` varchar(100) DEFAULT NULL COMMENT '支付密码',
  `u_hasword` varchar(100) DEFAULT NULL COMMENT '二级保密',
  `u_bdy` int(11) DEFAULT NULL COMMENT '生日',
  `u_card` varchar(20) DEFAULT NULL COMMENT '身份证号',
  `u_cardimg` text,
  `u_regtime` int(11) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `u_regip` varchar(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `del_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for cx_user_api
-- ----------------------------
DROP TABLE IF EXISTS `cx_user_api`;
CREATE TABLE `cx_user_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) DEFAULT '0' COMMENT '用户UID',
  `openid` varchar(80) DEFAULT NULL COMMENT 'openid',
  `unionid` varchar(80) NOT NULL COMMENT 'unionid',
  `subscribe` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否关注',
  `subscribe_time` int(11) NOT NULL DEFAULT '0' COMMENT '关注时间',
  `conf` text NOT NULL COMMENT '用户信息',
  `type` varchar(50) DEFAULT NULL COMMENT 'API类型',
  `addtime` int(11) DEFAULT '0' COMMENT '添加时间',
  `edittime` int(11) DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户API';

-- ----------------------------
-- Records of cx_user_api
-- ----------------------------

-- ----------------------------
-- Table structure for cx_user_data
-- ----------------------------
DROP TABLE IF EXISTS `cx_user_data`;
CREATE TABLE `cx_user_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_user_data
-- ----------------------------

-- ----------------------------
-- Table structure for cx_user_file
-- ----------------------------
DROP TABLE IF EXISTS `cx_user_file`;
CREATE TABLE `cx_user_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sql_file` varchar(50) NOT NULL DEFAULT '0' COMMENT '字段名',
  `sql_type` varchar(80) NOT NULL DEFAULT '0' COMMENT '储存类型',
  `form_title` varchar(255) NOT NULL DEFAULT '0' COMMENT '表单标题',
  `form_text` varchar(20) DEFAULT NULL COMMENT '表单提示',
  `form_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填',
  `form_required_list` varchar(100) DEFAULT NULL COMMENT '验证规则,非必填无效',
  `form_unit` varchar(80) DEFAULT NULL COMMENT '字段单位',
  `form_default` varchar(255) DEFAULT NULL COMMENT '默认值 ',
  `form_type` varchar(30) DEFAULT NULL COMMENT '表单类型',
  `form_class` varchar(50) DEFAULT NULL COMMENT '表单样式',
  `form_data` text COMMENT '多选项列表',
  `form_tip` varchar(150) DEFAULT NULL COMMENT '字段说明',
  `form_group` varchar(80) DEFAULT NULL COMMENT '字段分组',
  `group_see` varchar(255) DEFAULT NULL COMMENT '允许查看的会员组，默认全部可看，超级管理员可看',
  `group_edit` varchar(255) DEFAULT NULL COMMENT '允许编辑的会员组',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值 ',
  `list_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '列表是否显示，0|不显示，1|显示',
  `cont_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '内容页是否显示，0|不显示，1|显示',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_user_file
-- ----------------------------

-- ----------------------------
-- Table structure for cx_user_group
-- ----------------------------
DROP TABLE IF EXISTS `cx_user_group`;
CREATE TABLE `cx_user_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) NOT NULL DEFAULT '0',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用，0=>禁用，1=>启用',
  `rules` mediumtext COMMENT '后台权限',
  `hrules` mediumtext COMMENT '会员中心权限 ',
  `mrules` mediumtext COMMENT '会员中心权限 ',
  `arules` mediumtext COMMENT 'API权限',
  `group_up` mediumint(9) NOT NULL DEFAULT '0' COMMENT '升级积分',
  `group_money` int(11) NOT NULL DEFAULT '1' COMMENT '升级积分类型',
  `group_icon` varchar(255) DEFAULT NULL,
  `group_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户组类型，1=>超级管理员',
  `group_admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否拥有后台权限0=>禁用，1=>启用',
  `group_space` int(11) NOT NULL DEFAULT '0' COMMENT '储存空间MB',
  `sort` mediumint(8) NOT NULL DEFAULT '50' COMMENT '排序',
  `del_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_user_group
-- ----------------------------
INSERT INTO `cx_user_group` VALUES ('1', '0', '超级管理员', '1', '{\"0\":\"1\",\"1\":\"6\",\"2\":\"7\",\"3\":\"8\",\"4\":\"9\",\"5\":\"2\",\"6\":\"3\",\"7\":\"4\",\"8\":\"5\",\"9\":\"69\",\"10\":\"70\",\"11\":\"71\",\"12\":\"72\",\"13\":\"66\",\"14\":\"10\",\"15\":\"11\",\"16\":\"12\",\"17\":\"13\",\"20\":\"14\",\"21\":\"15\",\"22\":\"16\",\"23\":\"17\",\"24\":\"73\",\"25\":\"77\",\"26\":\"78\",\"27\":\"79\",\"28\":\"80\",\"29\":\"76\",\"30\":\"74\",\"31\":\"75\",\"32\":\"18\",\"33\":\"19\",\"34\":\"20\",\"35\":\"21\",\"36\":\"22\",\"37\":\"23\",\"38\":\"24\",\"39\":\"25\",\"40\":\"26\",\"41\":\"27\",\"42\":\"28\",\"43\":\"29\",\"44\":\"30\",\"45\":\"31\",\"46\":\"32\",\"47\":\"33\",\"48\":\"34\",\"49\":\"35\",\"50\":\"36\",\"51\":\"37\",\"52\":\"38\",\"53\":\"39\",\"54\":\"40\",\"55\":\"41\",\"56\":\"42\",\"57\":\"43\",\"58\":\"44\",\"59\":\"45\",\"60\":\"46\",\"61\":\"47\",\"62\":\"48\",\"63\":\"49\",\"64\":\"50\",\"65\":\"51\",\"66\":\"53\",\"67\":\"52\",\"68\":\"54\",\"69\":\"55\",\"70\":\"56\",\"73\":\"65\",\"74\":\"57\",\"75\":\"58\",\"76\":\"59\",\"77\":\"60\",\"78\":\"61\",\"79\":\"62\",\"80\":\"81\"}', null, null, null, '0', '0', '', '0', '1', '0', '0', '0');
INSERT INTO `cx_user_group` VALUES ('2', '0', '普通管理员', '1', null, null, null, null, '0', '0', 'cx-icon cx-iconblueberryuserset', '0', '1', '0', '0', '0');
INSERT INTO `cx_user_group` VALUES ('3', '0', '普通会员', '1', null, null, null, null, '0', '0', '', '1', '0', '0', '0', '0');
INSERT INTO `cx_user_group` VALUES ('4', '0', 'VIP', '1', null, null, null, null, '0', '0', '', '1', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for cx_worm_edit
-- ----------------------------
DROP TABLE IF EXISTS `cx_worm_edit`;
CREATE TABLE `cx_worm_edit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `tempname` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `contr` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `moid` int(11) DEFAULT '0',
  `conf` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `del_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `model` (`model`),
  KEY `contr` (`contr`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cx_worm_edit
-- ----------------------------
INSERT INTO `cx_worm_edit` VALUES ('1', 'banner', 'banner', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"banner.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"36674\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/f0ef18e1ff.jpg\\\"},{\\\"title\\\":\\\"banner2.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"360714\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/3c7cc0cc70.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('2', 'index_logo', 'index_logo', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"logo.png\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"7298\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/ffdaafafdd.png\\\"}]\",\"conttemp\":\"<img src=\\\"{$rs[\'uri\']}\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('3', 'cpfutitle', 'cpfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"ENERGY PRODUCTS\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('4', 'cptitle', 'cptitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"新能源产品\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('5', 'cpleftImg', 'cpleftImg', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"cpleft.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"117368\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/a419c11a4a.jpg\\\"}]\",\"conttemp\":\"<img src=\\\"{$rs[\'uri\']}\\\" style=\'width:100%;height:auto\' alt=\\\"\\\">\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('6', 'cpparttitle', 'cpparttitle', 'partlist', 'default', 'home', 'index', 'index', '1', '{\"id\":[\"11\",\"12\",\"13\",\"14\",\"15\",\"16\"],\"mid\":\"a\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\\\"\\/home\\/part-{$rs[\'id\']}\\\" title=\\\"\\\" class=\'cx-text-black cx-text-f16 cx-pad-lr20 ll-part-hover\'>{$rs[\'title\']}<\\/a>\\r\\n<span class=\'cx-text-black-5\'>\\/<\\/span>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('7', 'cplist', 'cplist', 'partedit', 'default', 'home', 'index', 'index', '1', '{\"fid\":[\"4\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"8\",\"title_num\":\"20\",\"description_num\":\"0\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\'\\/home\\/article-{$rs[\'id\']}.html\' title=\'{$rs[\'title\']}\' class=\\\"cx-xs6 cx-xl12 cx-pad-a10 ll-pad-a5\\\">\\r\\n<div class=\\\"layout  cx-pos-r cp\\\">\\r\\n<div class=\\\"layout cx-bg-img1x1\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" class=\\\"cx-img-responsive\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-pos-a cx-fex-c cx-fex-itemsc item-cp cx-text-white cx-text-fbig\\\" style=\\\"top: 0;left: 0;bottom:0;right: 0;background-color: rgba(0,0,0,0.5)\\\">\\r\\n{$rs[\'title\']}\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('8', 'ableftImg', 'ableftImg', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"ableft.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"141712\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/5525c29920.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('9', 'abfufutitle', 'abfufutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"专业成就品质，因为专业，所以出色。\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('10', 'abfutitle', 'abfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"关于我们\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('11', 'abtitle', 'abtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"ABOUT US\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('12', 'abcontent', 'abcontent', 'labelcktext', 'default', 'home', 'index', 'index', '0', '\"<p><span style=\\\"color:hsl(0, 0%, 30%);\\\">关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家<\\/span><\\/p><p><span style=\\\"color:hsl(0, 0%, 30%);\\\">居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点<\\/span><\\/p><p><span style=\\\"color:hsl(0, 0%, 30%);\\\">家居关于逸点家居关于逸点家居关于逸点家。<\\/span><\\/p><p>&nbsp;<\\/p><p><span style=\\\"color:hsl(0, 0%, 30%);\\\">关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家<\\/span><\\/p><p><span style=\\\"color:hsl(0, 0%, 30%);\\\">居关于逸点家居关于逸点家居关于逸点家居关于逸点。<\\/span><\\/p>\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('13', 'dingzhilist', 'dingzhilist', 'partedit', 'default', 'home', 'index', 'index', '1', '{\"fid\":[\"5\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"12\",\"title_num\":\"0\",\"description_num\":\"0\",\"conttemp_name\":\"\",\"conttemp\":\"<div class=\\\"cx-xl6 dingzhi-item\\\">\\r\\n<div class=\\\"cx-bg-img1x1\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white \\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('14', 'dzfutitle', 'dzfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"定制案例\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('15', 'dztitle', 'dztitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"CUSTOM CASE\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('16', 'dzcontent', 'dzcontent', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家\\n\\n居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点\\n\\n家居关于逸点家居关于逸点家居关于逸点家。\\n\\n\\n\\n关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家居关于逸点家\\n\\n居关于逸点家居关于逸点家居关于逸点家居关于逸点。\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('17', 'newsfutitle', 'newsfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"新闻资讯\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('18', 'newstitle', 'newstitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"NEWS INFOMATION\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('19', '公司简介', 'xlcontent', 'labelcktext', 'default', 'home', 'index', 'index', '0', '\"<p>公司拥有自主知识产权的常压循环流化床气化技术，借助于中国科学院工程热物理研究所在循环流化床技术方面的优势，<\\/p><p>开发和推广循环流化床煤气化技术在化工、建材、冶金、环保等领域的应用，<\\/p><p>目标是为工业燃气客户提供经济适用的清洁能源解决方案。<\\/p><p>公司有二十年的循环流化床气化技术研发和工艺设计、<\\/p><p>工程化经验，并在冶金、建材等行业成功应用三十多台。<\\/p><p>作为煤制工业燃气技术的领航者，中科清能将以“提供清洁能源解决方案”为己任，致力为工业燃气领域节能、减排、增效做出贡献！<\\/p>\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('20', 'newnenpic', 'newnenpic', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"ne1.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"70684\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/9e6eaaa88b.jpg\\\"},{\\\"title\\\":\\\"ne2.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"67574\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/e1511f11f0.jpg\\\"},{\\\"title\\\":\\\"ne3.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"97105\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/b0bbd0b0c4.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"swiper-slide\\\">\\n<div class=\\\"layout\\\">\\n<div class=\\\"cx-bg-img1x1\\\">\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white-1\\\">\\n<img src=\\\"{$rs[\'uri\']}\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\\n<\\/div>\\n<\\/div>\\n<\\/div>\\n<\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('21', 'fengneng', 'fengneng', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"new.png\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"2314\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/2ff240ddd0.png\\\"}]\",\"conttemp\":\"<img src=\\\"{$rs[\'uri\']}\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('22', 'tianyang', 'tianyang', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"news2.png\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"2008\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/26d6d82282.png\\\"}]\",\"conttemp\":\"<img src=\\\"{$rs[\'uri\']}\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('23', 'taiyang', 'taiyang', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"new2.png\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"2132\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/c5bb5acccc.png\\\"}]\",\"conttemp\":\"<img src=\\\"{$rs[\'uri\']}\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('24', 'video', 'video', 'labelvideo', 'default', 'home', 'index', 'index', '0', '{\"videolist\":\"\",\"conttemp\":\"\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('25', 'newlist', 'newlist', 'partedit', 'default', 'home', 'index', 'index', '1', '{\"fid\":[\"1\",\"2\",\"3\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"4\",\"title_num\":\"50\",\"description_num\":\"100\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\'\\/home\\/article-{$rs[\'id\']}.html\' title=\'{$rs[\'title\']}\' class=\\\"cx-xs6 cx-pad-a10 ll-pad-a5 cx-xl12 cx-text-lh item-hover\\\" data-scroll-reveal=\\\"enter bottom,move 50px,after 0.3s\\\">\\r\\n<div class=\\\"cx-text-black-6 cx-text-fbig cx-pad-tb20 ll-pad-tb10 cx-borbottom cx-bor-white-1 cx-pos-r bottom-hover\\\">{$rs[\'time_m\']}-{$rs[\'time_d\']}<\\/div>\\r\\n<div class=\\\"cx-text-black-5 cx-text-f16 cx-mag-tb20 ll-mag-tb10 index-mobile-shenglv1\\\">{$rs[\'title\']}<\\/div>\\r\\n<div class=\\\"cx-text-black-4 index-mobile-shenglv2\\\">{$rs[\'description\']}<\\/div>\\r\\n<div class=\\\"layout cx-mag-t20\\\" style=\\\"overflow: hidden;\\\">\\r\\n<div class=\\\"cx-bg-img4x3\\\">\\r\\n<div class=\\\"cx-bg-img\\\" style=\\\"background-image: url({$rs[\'picurl\']});\\\">\\r\\n\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('26', 'abbanner', 'abbanner', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"abbanner.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"36674\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/48e4a3e374.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img\\\" style=\\\"background-image: url({$rs[\'uri\']});height:100%;background-attachment:fixed;\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('27', 'abrightbanner', 'abrightbanner', 'labelimgs', 'default', 'home', 'index', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"abrightpic.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"222626\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/b3b3b0bbbf.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img cx-hidden-l\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\" data-scroll-reveal=\\\"enter right,move 50px,after 0.2s\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('28', 'xltext', 'xltext', 'labelcktext', 'default', 'home', 'index', 'index', '0', '\"<p>开发和推广循环流化床煤气化技术在化工、建材、冶金、环保等领域的应用，<\\/p><p>目标是为工业燃气客户提供经济适用的清洁能源解决方案。<\\/p>\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('29', 'newslist', 'newslist', 'partedit', 'default', 'home', 'index', 'index', '1', '{\"fid\":[\"5\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"2\",\"title_num\":\"20\",\"description_num\":\"100\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\\\"\\/home\\/article-{$rs[\'id\']}.html\\\" title=\'{$rs[\'title\']}\' class=\\\"cx-xs12 cx-xl24 cx-fex-l cx-pad-a10  ll-borbtmline tuandui\\\" data-scroll-reveal=\\\"enter bottom,move 50px,after 0.1s\\\">\\r\\n<div class=\\\"cx-xl10\\\">\\r\\n<div class=\\\"cx-bg-img1x1\\\" style=\\\"overflow: hidden\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white cx-bor-raall\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-xl14 cx-fex-column cx-fex-c cx-text-lh\\\">\\r\\n<span class=\\\"cx-text-f16 tuandui_title\\\">{$rs[\'title\']}<\\/span>\\r\\n<span class=\\\"cx-text-f12 cx-text-black-7\\\">产品经理<\\/span>\\r\\n<div class=\\\"cx-text-black-8 index-mobile-shenglv2\\\">{$rs[\'description\']}<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('30', 'newslist1', 'newslist1', 'partedit', 'default', 'home', 'index', 'index', '1', '{\"fid\":[\"5\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"2\",\"limit\":\"2\",\"title_num\":\"20\",\"description_num\":\"100\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\\\"\\/home\\/article-{$rs[\'id\']}.html\\\" title=\'{$rs[\'title\']}\' class=\\\"cx-xs12 cx-xl24 cx-fex-l cx-pad-a10 ll-borbtmline tuandui\\\" data-scroll-reveal=\\\"enter bottom,move 50px,after 0.1s\\\">\\r\\n<div class=\\\"cx-xl10\\\">\\r\\n<div class=\\\"cx-bg-img1x1\\\" style=\\\"overflow: hidden\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white cx-bor-raall\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-xl14 cx-fex-column cx-fex-c cx-text-lh\\\">\\r\\n<span class=\\\"cx-text-f16 tuandui_title\\\">{$rs[\'title\']}<\\/span>\\r\\n<span class=\\\"cx-text-f12 cx-text-black-7\\\">产品经理<\\/span>\\r\\n<div class=\\\"cx-text-black-8 index-mobile-shenglv2\\\">{$rs[\'description\']}<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('31', 'gongsi_name', 'gongsi_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"逸点清洁能源有限公司\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('32', 'youxiang_name', 'youxiang_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"admin@cxbs.net\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('33', 'wx_name', 'wx_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"18616399020\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('34', 'qq_name', 'qq_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"840712498\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('35', 'dizhi_name', 'dizhi_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"上海市浦东新区惠南镇绿地峰汇商务广场B座1710室\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('36', 'dianhua_name', 'dianhua_name', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"021-80158202\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('37', 'bannertitle', 'bannertitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"让我们让地球变得更干净！\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('38', 'bannerfutitle', 'bannerfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"探索环保新方式，追寻健康新生活。\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('39', 'news_banner', 'news_banner', 'labelimgs', 'default', 'home', 'cms.part', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"newsbanner.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"107872\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/1801e0d8df.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('40', 'news_banner', 'news_banner', 'labelimgs', 'default', 'home', 'cms.article', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"newsbanner.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"107872\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/46bee8346b.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('41', 'cps_banner', 'cps_banner', 'labelimgs', 'default', 'home', 'cms.article', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"newsbanner.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"107872\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/afa2af88aa.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc\\\" style=\\\"background-image: url({$rs[\'uri\']})\\\"><\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('42', 'tuijian', 'tuijian', 'partedit', 'default', 'home', 'cms.article', 'index', '1', '{\"fid\":[\"4\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"3\",\"title_num\":\"20\",\"description_num\":\"0\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\'\\/home\\/article-{$rs[\'id\']}.html\' title=\'{$rs[\'title\']}\' class=\\\"layout cx-pad-tb15 cx-borbottom cx-bor-white-1\\\">\\r\\n<div class=\\\"layout  cx-pos-r cp\\\">\\r\\n<div class=\\\"layout cx-bg-img1x1\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" class=\\\"cx-img-responsive\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-pos-a cx-fex-c cx-fex-itemsc item-cp cx-text-white cx-text-fbig\\\" style=\\\"top: 0;left: 0;bottom:0;right: 0;background-color: rgba(0,0,0,0.5)\\\">\\r\\n{$rs[\'title\']}\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('43', 'tuijian', 'tuijian', 'partedit', 'default', 'home', 'cms.part', 'index', '1', '{\"fid\":[\"1\",\"2\",\"3\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"5\",\"title_num\":\"10\",\"description_num\":\"0\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\'\\/home\\/article-{$rs[\'id\']}.html\' title=\'{$rs[\'title\']}\' class=\\\"layout cx-pad-tb15 cx-borbottom cx-bor-white-1\\\">\\r\\n<div class=\\\"layout  cx-pos-r cp\\\">\\r\\n<div class=\\\"layout cx-bg-img4x3\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" class=\\\"cx-img-responsive\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-pos-a cx-fex-c cx-fex-itemsc item-cp cx-text-white cx-text-fbig\\\" style=\\\"top: 0;left: 0;bottom:0;right: 0;background-color: rgba(0,0,0,0.5)\\\">\\r\\n{$rs[\'title\']}\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('44', 'cptuijian', 'cptuijian', 'partedit', 'default', 'home', 'cms.part', 'index', '1', '{\"fid\":[\"4\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\"],\"mid\":\"a\",\"jian\":\"a\",\"jian_lavel\":\"\",\"order\":\"jian desc,addtime desc\",\"limit_num\":\"1\",\"limit\":\"3\",\"title_num\":\"10\",\"description_num\":\"0\",\"conttemp_name\":\"\",\"conttemp\":\"<a href=\'\\/home\\/article-{$rs[\'id\']}.html\' title=\'{$rs[\'title\']}\' class=\\\"layout cx-pad-tb15 cx-borbottom cx-bor-white-1\\\">\\r\\n<div class=\\\"layout  cx-pos-r cp\\\">\\r\\n<div class=\\\"layout cx-bg-img1x1\\\">\\r\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white\\\">\\r\\n<img src=\\\"{$rs[\'picurl\']}\\\" class=\\\"cx-img-responsive\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" alt=\\\"\\\">\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<div class=\\\"cx-pos-a cx-fex-c cx-fex-itemsc item-cp cx-text-white cx-text-fbig\\\" style=\\\"top: 0;left: 0;bottom:0;right: 0;background-color: rgba(0,0,0,0.5)\\\">\\r\\n{$rs[\'title\']}\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/a>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('45', 'newnenpic', 'newnenpic', 'labelimgs', 'default', 'home', 'cms.part', 'index', '0', '{\"imglist\":\"[{\\\"title\\\":\\\"ne1.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"70684\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/9291243401.jpg\\\"},{\\\"title\\\":\\\"ne2.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"67574\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/30121f1212.jpg\\\"},{\\\"title\\\":\\\"ne3.jpg\\\",\\\"like\\\":\\\"\\\",\\\"sort\\\":\\\"0\\\",\\\"size\\\":\\\"78844\\\",\\\"uri\\\":\\\"http:\\\\\\/\\\\\\/www_cxbs_net\\\\\\/Ls_dir\\\\\\/2020-10\\\\\\/0f72d20d03.jpg\\\"}]\",\"conttemp\":\"<div class=\\\"swiper-slide\\\">\\n<div class=\\\"layout\\\">\\n<div class=\\\"cx-bg-img1x1\\\">\\n<div class=\\\"cx-bg-img cx-fex-c cx-fex-itemsc cx-bg-white-1\\\">\\n<img src=\\\"{$rs[\'uri\']}\\\" onerror=\\\"this.src=\'\\/public\\/wormcms\\/img\\/imgnone.jpg\'\\\" class=\\\"cx-img-responsive\\\" alt=\\\"\\\">\\n<\\/div>\\n<\\/div>\\n<\\/div>\\n<\\/div>\"}', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('46', 'gstitle', 'gstitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"公司简介\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('47', 'gsfutitle', 'gsfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"ABOUT\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('48', 'fengnengnum', 'fengnengnum', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"01\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('49', 'fengnengtitle', 'fengnengtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"Wind Energy\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('50', 'tianyangnum', 'tianyangnum', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"02\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('51', 'tianyangtitle', 'tianyangtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"Natural Gas\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('52', 'taiyangnum', 'taiyangnum', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"03\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('53', 'taiyangtitle', 'taiyangtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"Solar Energy\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('54', 'qjtitel', 'qjtitel', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"清洁能源\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('55', 'qjfutitel', 'qjfutitel', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"CLEAN ENEGY\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('56', 'tdfutitle', 'tdfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"TEAM\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('57', 'tdtitle', 'tdtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"研发团队\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('58', 'lxtitle', 'lxtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"联系我们\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('59', 'lxfutitle', 'lxfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"CONTACT US\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('60', 'newtitle', 'newtitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"新闻资讯\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('61', 'newfutitle', 'newfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"NEWS\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('62', 'hzfutitle', 'hzfutitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"PARTNERS\"', '1', '0');
INSERT INTO `cx_worm_edit` VALUES ('63', 'hztitle', 'hztitle', 'labeltext', 'default', 'home', 'index', 'index', '0', '\"合作伙伴\"', '1', '0');

-- ----------------------------
-- Table structure for cx_worm_tag
-- ----------------------------
DROP TABLE IF EXISTS `cx_worm_tag`;
CREATE TABLE `cx_worm_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(80) DEFAULT NULL COMMENT '标题',
  `TagName` varchar(80) DEFAULT NULL COMMENT '标签名',
  `TagModel` varchar(80) DEFAULT NULL COMMENT '模块',
  `TagMid` varchar(255) DEFAULT NULL COMMENT '模型',
  `TagFid` varchar(255) DEFAULT NULL COMMENT '栏目',
  `TagFiled` varchar(255) DEFAULT NULL COMMENT '字段',
  `TagLimit` int(11) DEFAULT '0' COMMENT '条数',
  `TagOrder` varchar(255) DEFAULT NULL COMMENT '排序',
  `type` varchar(255) DEFAULT NULL COMMENT '类型',
  `tempname` varchar(255) DEFAULT NULL COMMENT '所在模板名称',
  `model` varchar(255) DEFAULT NULL COMMENT '所在模板模块',
  `contr` varchar(255) DEFAULT NULL COMMENT '所在模板控制器',
  `action` varchar(255) DEFAULT NULL COMMENT '所在模板方法',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '所在模板方法',
  `latename` varchar(80) DEFAULT '0' COMMENT '调用模板名称',
  `template` mediumtext COMMENT '模板代码',
  `temptype` tinyint(4) DEFAULT '0' COMMENT '模板调用类型',
  `conf` mediumtext COMMENT '附加配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='新模板标签管理';

-- ----------------------------
-- Records of cx_worm_tag
-- ----------------------------
