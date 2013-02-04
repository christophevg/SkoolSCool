-- abuse grant to create the user, even if it doesn't exist
GRANT USAGE ON *.* TO '%%USER%%'@'localhost';
-- drop the user. it will exist even on a previous blank db
DROP USER '%%USER%%'@'localhost';
-- actually create the user
CREATE USER '%%USER%%'@'localhost' IDENTIFIED BY '%%PASS%%';

-- drop the database if it exists
DROP DATABASE IF EXISTS %%DATABASE%%;
-- create the database
CREATE DATABASE %%DATABASE%%;
-- grant access to the database to the user
GRANT ALL PRIVILEGES ON %%DATABASE%%.* TO '%%USER%%'@'localhost';
