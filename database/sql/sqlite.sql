/*
 Navicat Premium Data Transfer

 Source Server         : 94list2
 Source Server Type    : SQLite
 Source Server Version : 3035005 (3.35.5)
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005 (3.35.5)
 File Encoding         : 65001

 Date: 04/05/2024 12:03:22
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
  "updated_at" text NOT NULL,
  "deleted_at" text
);

-- ----------------------------
-- Table structure for inv_codes
-- ----------------------------
DROP TABLE IF EXISTS "inv_codes";
CREATE TABLE "inv_codes" (
  "id" integer NOT NULL,
  "name" text NOT NULL,
  "use_count" integer NOT NULL,
  "can_count" integer NOT NULL,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL,
  "deleted_at" text,
  PRIMARY KEY ("id")
);

-- ----------------------------
-- Table structure for ips
-- ----------------------------
DROP TABLE IF EXISTS "ips";
CREATE TABLE "ips" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "ip" text NOT NULL,
  "mode" integer NOT NULL,
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
  "fs_id" text NOT NULL,
  "filename" text NOT NULL,
  "size" integer NOT NULL,
  "url" text NOT NULL,
  "ua" text NOT NULL,
  "user_id" integer NOT NULL,
  "account_id" integer NOT NULL,
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
  "inv_code_id" integer NOT NULL,
  "username" text NOT NULL COLLATE BINARY,
  "password" text NOT NULL,
  "role" text NOT NULL DEFAULT user,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL,
  "deleted_at" text
);

-- ----------------------------
-- Auto increment value for accounts
-- ----------------------------
UPDATE "sqlite_sequence" SET seq = 4 WHERE name = 'accounts';

-- ----------------------------
-- Auto increment value for groups
-- ----------------------------

-- ----------------------------
-- Auto increment value for ips
-- ----------------------------

-- ----------------------------
-- Auto increment value for records
-- ----------------------------
UPDATE "sqlite_sequence" SET seq = 2 WHERE name = 'records';

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
UPDATE "sqlite_sequence" SET seq = 1 WHERE name = 'users';

PRAGMA foreign_keys = true;
