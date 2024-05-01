/*
 Navicat Premium Data Transfer

 Source Server         : 94list2
 Source Server Type    : SQLite
 Source Server Version : 3035005 (3.35.5)
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005 (3.35.5)
 File Encoding         : 65001

 Date: 02/05/2024 03:17:36
*/

PRAGMA foreign_keys = false;

-- ----------------------------
-- Table structure for accounts
-- ----------------------------
DROP TABLE IF EXISTS "accounts";
CREATE TABLE "accounts" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "baidu_name" text NOT NULL,
  "netdisk_name" text NOT NULL,
  "cookie" text NOT NULL,
  "vip_type" text NOT NULL,
  "switch" integer NOT NULL,
  "reason" text,
  "svip_end_at" text NOT NULL,
  "last_use_at" text NOT NULL,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL,
  "deleted_at" text
);

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS "groups";
CREATE TABLE "groups" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL COLLATE BINARY,
  "count" integer NOT NULL DEFAULT 0,
  "size" integer NOT NULL DEFAULT 0,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL
);

-- ----------------------------
-- Table structure for records
-- ----------------------------
DROP TABLE IF EXISTS "records";
CREATE TABLE "records" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "ip" text NOT NULL,
  "action_name" text NOT NULL,
  "link" text NOT NULL,
  "md5" text,
  "size" integer,
  "ua" text,
  "user_id" integer,
  "account_id" integer,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL
);

-- ----------------------------
-- Table structure for sqlite_sequence
-- ----------------------------
DROP TABLE IF EXISTS "sqlite_sequence";
CREATE TABLE "sqlite_sequence" (
  "name",
  "seq"
);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "users";
CREATE TABLE "users" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "group_id" integer NOT NULL DEFAULT 0,
  "username" text NOT NULL COLLATE BINARY,
  "password" text NOT NULL,
  "role" text NOT NULL DEFAULT user,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL
);

-- ----------------------------
-- Auto increment value for accounts
-- ----------------------------
UPDATE "sqlite_sequence" SET seq = 3 WHERE name = 'accounts';

-- ----------------------------
-- Auto increment value for groups
-- ----------------------------

-- ----------------------------
-- Auto increment value for records
-- ----------------------------

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
UPDATE "sqlite_sequence" SET seq = 1 WHERE name = 'users';

PRAGMA foreign_keys = true;
