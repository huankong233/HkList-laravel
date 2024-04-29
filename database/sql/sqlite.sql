/*
 Navicat Premium Data Transfer

 Source Server         : 94list2
 Source Server Type    : SQLite
 Source Server Version : 3035005 (3.35.5)
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3035005 (3.35.5)
 File Encoding         : 65001

 Date: 29/04/2024 17:11:35
*/

PRAGMA foreign_keys = false;

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

PRAGMA foreign_keys = true;
