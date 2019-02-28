CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
USE doingsdone;
CREATE TABLE project (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name CHAR(128),
                       user_id INT UNSIGNED
);
CREATE TABLE task (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    date_create DATETIME,
                    date_done DATETIME,
                    state TINYINT DEFAULT 0,
                    name CHAR(128),
                    file CHAR(128),
                    deadline DATETIME,
                    user_id INT UNSIGNED,
                    project_id INT UNSIGNED
);
CREATE TABLE user (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    date_regist DATETIME,
                    email CHAR(128) NOT NULL UNIQUE,
                    name CHAR(128),
                    password CHAR(64)
);
CREATE INDEX p ON project(name);
CREATE INDEX u ON user(name);
CREATE INDEX t ON task(name);
