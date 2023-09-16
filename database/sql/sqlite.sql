/*
 Navicat Premium Data Transfer

 Source Server         : database
 Source Server Type    : SQLite
 Source Server Version : 3035005
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005
 File Encoding         : 65001

 Date: 16/09/2023 23:39:36
*/

PRAGMA foreign_keys = false;

-- ----------------------------
-- Table structure for bd_users
-- ----------------------------
DROP TABLE IF EXISTS "bd_users";
CREATE TABLE "bd_users" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "baidu_name" TEXT,
  "netdisk_name" TEXT,
  "cookie" TEXT,
  "add_time" DATE,
  "use" DATE,
  "state" TEXT,
  "switch" integer,
  "vip_type" TEXT
);

-- ----------------------------
-- Records of bd_users
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "users";
CREATE TABLE "users" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "username" TEXT,
  "password" TEXT,
  "is_admin" integer
);

-- ----------------------------
-- Records of users
-- ----------------------------

-- ----------------------------
-- Auto increment value for users
-- ----------------------------

PRAGMA foreign_keys = true;
