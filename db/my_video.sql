/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : my_video

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 12/12/2018 21:05:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tag
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag`  (
  `tag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `video_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `search_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`tag_id`) USING BTREE,
  UNIQUE INDEX `tag_name`(`tag_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for video
-- ----------------------------
DROP TABLE IF EXISTS `video`;
CREATE TABLE `video`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_index` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `path` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `file_name` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `file_size` decimal(6, 2) NOT NULL DEFAULT 0.00,
  `duration` decimal(6, 2) UNSIGNED NOT NULL DEFAULT 0.00,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_mod_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_view_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `add_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `file_index`(`file_index`) USING BTREE,
  INDEX `last_view_time`(`last_view_time`) USING BTREE,
  INDEX `view_count`(`view_count`) USING BTREE,
  INDEX `tags`(`tags`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 365 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
