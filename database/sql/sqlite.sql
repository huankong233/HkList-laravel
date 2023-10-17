/*
 Navicat Premium Data Transfer

 Source Server         : database
 Source Server Type    : SQLite
 Source Server Version : 3035005
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005
 File Encoding         : 65001

 Date: 17/10/2023 12:32:50
*/

PRAGMA
foreign_keys = false;

-- ----------------------------
-- Table structure for bd_users
-- ----------------------------
DROP TABLE IF EXISTS "bd_users";
CREATE TABLE "bd_users"
(
    "id"            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "baidu_name"    TEXT,
    "netdisk_name"  TEXT,
    "cookie"        TEXT,
    "add_time"      DATE,
    "svip_end_time" DATE,
    "use"           DATE,
    "state"         TEXT,
    "switch"        integer,
    "vip_type"      TEXT
);

-- ----------------------------
-- Records of bd_users
-- ----------------------------

-- ----------------------------
-- Table structure for sqlite_sequence
-- ----------------------------

-- ----------------------------
-- Records of sqlite_sequence
-- ----------------------------
INSERT INTO "sqlite_sequence"
VALUES ('users', 1);
INSERT INTO "sqlite_sequence"
VALUES ('bd_users', 1);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "users";
CREATE TABLE "users"
(
    "id"       INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "username" TEXT,
    "password" TEXT,
    "is_admin" integer
);

-- ----------------------------
-- Records of users
-- ----------------------------

-- ----------------------------
-- Auto increment value for bd_users
-- ----------------------------
UPDATE "sqlite_sequence"
SET seq = 1
WHERE name = 'bd_users';

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
UPDATE "sqlite_sequence"
SET seq = 1
WHERE name = 'users';

PRAGMA
foreign_keys = true;
