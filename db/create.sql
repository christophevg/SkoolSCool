-- abuse grant to create the user, even if it doesn't exist
GRANT USAGE ON *.* TO 'vbsg'@'localhost';
-- drop the user. it will exist even on a previous blank db
DROP USER 'vbsg'@'localhost';
-- actually create the user
CREATE USER 'vbsg'@'localhost' IDENTIFIED BY 'vbsg';

-- drop the database if it exists
DROP DATABASE IF EXISTS vbsg;
-- create the database
CREATE DATABASE vbsg;
-- grant access to the database to the user
GRANT ALL PRIVILEGES ON vbsg.* TO 'vbsg'@'localhost';
