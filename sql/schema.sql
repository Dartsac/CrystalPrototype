-- Crystal database schema.
-- Create everything from scratch:
--   mysql -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS Crystal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Crystal;

CREATE TABLE IF NOT EXISTS users (
	usersId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	usersName VARCHAR(64) NOT NULL,
	usersEmail VARCHAR(128) NOT NULL,
	usersPwd VARCHAR(255) NOT NULL,
	usersBirthDate DATE NOT NULL,
	usersBirthTime TIME NULL,
	usersPlace VARCHAR(255) NULL,
	-- Resolved birth place: coordinates and IANA timezone (e.g. America/New_York).
	usersLat DECIMAL(9,6) NULL,
	usersLng DECIMAL(9,6) NULL,
	usersTimezone VARCHAR(64) NULL,
	UNIQUE KEY uniq_users_name (usersName),
	UNIQUE KEY uniq_users_email (usersEmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
