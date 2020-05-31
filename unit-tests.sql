CREATE USER unit_test@localhost identified by 'test';

CREATE DATABASE unit_tests;

USE unit_tests;

CREATE TABLE users
(
id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
first_name VARCHAR(255) NOT NULL,
last_name VARCHAR(255) NOT NULL,
PRIMARY KEY(id)
) Engine=INNODB;

INSERT INTO users (first_name, last_name) VALUES
('John','Doe'),
('Jane','Doe');

CREATE TABLE dump
(
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
value VARCHAR(255) NOT NULL,
PRIMARY KEY(id),
KEY(value)
) Engine=INNODB;

GRANT INSERT,SELECT,UPDATE,DELETE ON unit_tests.* TO unit_test@localhost;