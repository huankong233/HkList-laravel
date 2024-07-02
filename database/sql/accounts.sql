/*
 Navicat Premium Data Transfer

 Source Server         : 94list-public
 Source Server Type    : SQLite
 Source Server Version : 3035005 (3.35.5)
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005 (3.35.5)
 File Encoding         : 65001

 Date: 01/07/2024 18:47:13
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
  "prov" text DEFAULT NULL,
  "svip_end_at" text NOT NULL,
  "last_use_at" text NOT NULL,
  "created_at" text NOT NULL,
  "updated_at" text NOT NULL,
  "deleted_at" text
);

PRAGMA foreign_keys = true;
