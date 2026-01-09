-- Upgrade an existing Crystal database (created before birth locations were stored).
-- Run once:
--   mysql -u root Crystal < sql/migrate-001-birth-location.sql
--
-- Existing users keep working: their birth city is looked up again the next
-- time they open their profile, and they can fix missing birth details on
-- the "Update birth details" page.

ALTER TABLE users
	ADD COLUMN usersLat DECIMAL(9,6) NULL,
	ADD COLUMN usersLng DECIMAL(9,6) NULL,
	ADD COLUMN usersTimezone VARCHAR(64) NULL;
