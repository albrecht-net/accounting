-- Datenbank Tabellen Updates für die Applikations-Datenbank
-- Achtung: Die Updates sind nur für die angegebene Versionserhöhung gültig und sollten nach aufsteigener Versionsnummer ausgeführt werden!
--

-- --------------------------------------------------------

--
-- Update von v1.4.0-beta zu v2.0.0-beta
--
ALTER TABLE `databases` DEFAULT charset = utf8mb4 COLLATE = utf8mb4_general_ci;
ALTER TABLE `users` DEFAULT charset = utf8mb4 COLLATE = utf8mb4_general_ci
ALTER TABLE `userconfig` DEFAULT charset = utf8mb4 COLLATE = utf8mb4_general_ci
ALTER TABLE `databases` CHANGE `datumErstellt` `created` DATETIME NOT NULL;

-- --------------------------------------------------------

--
-- Update von v0.8.0-alpha zu v1.4.0-beta
--

RENAME TABLE `favorites` TO `templates`;
ALTER TABLE `templates` CHANGE `favoriteID` `templateID` INT(32) NOT NULL AUTO_INCREMENT;
ALTER TABLE `templates` ADD `datumErstellt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `templateID`;