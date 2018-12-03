-- Datenbank Tabellen Updates für die Benutzer-Zieldatenbank
-- Achtung: Die Updates sind nur für die angegebene Versionserhöhung gültig und sollten nach aufsteigener Versionsnummer ausgeführt werden!
--

-- --------------------------------------------------------

--
-- Update von v0.9.1-alpha zu vx.x.x-beta
--
-- Diese Version ist aufgrund der Umstrukturierung der Tabelle "konto" nicht mehr rückwärzkompatibel.
-- Es ist nur die Tabelle "konto" vom Update betroffen.
-- Grund: Die kontoID wird nun automatisch basierend auf der "kontoKlasse" und "kontoKategorie" erstellt.
--
-- Vor dem Update ein Backup der gesamten Datenbank erstellen!
-- 
-- Um das Update manuell auszuführen muss die Tabelle "konto" gelöscht werden. Dabei muss die Fremdschlüsselprüfung kurzzeitig deaktiviert werden.

DROP TABLE `konto`;

-- Anschliessend die neuen Tabellen erstellen.

CREATE TABLE `konto` (
  `kontoID` varchar(5) NOT NULL,
  `bezeichnung` varchar(32) NOT NULL,
  `kategorie` varchar(3) NOT NULL,
  `kontoNR` varchar(2) NOT NULL,
  `aktiv` enum('Y','N') NOT NULL DEFAULT 'Y',
  `abstMöglich` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`kontoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `kontoKategorie` (
  `kategorieID` VARCHAR(3) NOT NULL,
  `bezeichnung` VARCHAR(32),
  `klasse` VARCHAR(1) NOT NULL,
  `kategorieNR` VARCHAR(2) NOT NULL,
  PRIMARY KEY (`kategorieID`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `kontoKlasse` (
  `klasseID` VARCHAR(1) NOT NULL,
  `bezeichnung` VARCHAR(32),
  `vorzeichen` TINYINT NOT NULL,
  PRIMARY KEY (`klasseID`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `kontoKategorie`
  ADD CONSTRAINT `kontoKategorie_ibfk_1` FOREIGN KEY (`klasse`) REFERENCES `kontoKlasse` (`klasseID`) ON UPDATE CASCADE;

ALTER TABLE `konto`
  ADD CONSTRAINT `konto_ibfk_1` FOREIGN KEY (`kategorie`) REFERENCES `kontoKategorie` (`kategorieID`) ON UPDATE CASCADE;

ALTER TABLE
  `journal`
DROP FOREIGN KEY
  `journal_ibfk_2`;
ALTER TABLE
  `journal` ADD CONSTRAINT `journal_ibfk_2` FOREIGN KEY(`kontoSoll`) REFERENCES `konto`(`kontoID`) ON UPDATE CASCADE;

ALTER TABLE
  `journal`
DROP FOREIGN KEY
  `journal_ibfk_3`;
ALTER TABLE
  `journal` ADD CONSTRAINT `journal_ibfk_3` FOREIGN KEY(`kontoHaben`) REFERENCES `konto`(`kontoID`) ON UPDATE CASCADE;

CREATE TRIGGER `update_child_kontoKategorie` AFTER UPDATE
ON
  `kontoKlasse` FOR EACH ROW
UPDATE
  kontoKategorie
SET
  kontoKategorie.kategorieID = CONCAT(kontoKategorie.klasse,kontoKategorie.kategorieNR)
WHERE
  kontoKategorie.klasse = NEW.klasseID;

CREATE TRIGGER `generate_kategorieID_insert` BEFORE INSERT
ON
  `kontoKategorie` FOR EACH ROW
SET
  NEW.kategorieID = CONCAT(NEW.klasse, NEW.kategorieNR);

CREATE TRIGGER `generate_kategorieID_update` BEFORE UPDATE
ON
  `kontoKategorie` FOR EACH ROW
SET
  NEW.kategorieID = CONCAT(NEW.klasse, NEW.kategorieNR);

CREATE TRIGGER `update_child_konto` AFTER UPDATE
ON
  `kontoKategorie` FOR EACH ROW
UPDATE
  konto
SET
  konto.kontoID = CONCAT(konto.kategorie,konto.kontoNR)
WHERE
  konto.kategorie = NEW.kategorieID;

CREATE TRIGGER `generate_kontoID_insert` BEFORE INSERT
ON
  `konto` FOR EACH ROW
SET
  NEW.kontoID = CONCAT(NEW.kategorie, NEW.kontoNR);

CREATE TRIGGER `generate_kontoID_update` BEFORE UPDATE
ON
  `konto` FOR EACH ROW
SET
  NEW.kontoID = CONCAT(NEW.kategorie, NEW.kontoNR);