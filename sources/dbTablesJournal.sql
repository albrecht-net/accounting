-- Datenbank Tabellen Vorlage für die Benutzer-Zieldatenbank
-- Gültig ab: Accounting vx.x.x-beta
-- 

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `empfänger`
--

CREATE TABLE `empfänger` (
  `empfängerID` int(32) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `aktiv` enum('Y','N') NOT NULL DEFAULT 'Y',
  `kundennummer` varchar(64) NOT NULL,
  PRIMARY KEY (`empfängerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `journal`
--

CREATE TABLE `journal` (
  `buchungID` int(32) NOT NULL AUTO_INCREMENT,
  `datumErstellt` datetime NOT NULL DEFAULT current_timestamp(),
  `datum` date DEFAULT NULL,
  `empfänger` int(32) DEFAULT NULL,
  `reNummer` varchar(64) DEFAULT NULL,
  `buchungstext` text DEFAULT NULL,
  `totalbetrag` float(12,2) NOT NULL DEFAULT 0.00,
  `kontoSoll` varchar(5) DEFAULT NULL,
  `kontoHaben` varchar(5) DEFAULT NULL,
  `periode` int(32) DEFAULT NULL,
  `klassifikation1` int(32) DEFAULT NULL,
  `klassifikation2` int(32) DEFAULT NULL,
  `klassifikation3` int(32) DEFAULT NULL,
  `buchungsreferenz` int(32) DEFAULT NULL,
  `abstimmung` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`buchungID`),
  KEY `empfänger` (`empfänger`),
  KEY `kontoSoll` (`kontoSoll`),
  KEY `kontoHaben` (`kontoHaben`),
  KEY `periode` (`periode`),
  KEY `klassifikation1` (`klassifikation1`),
  KEY `klassifikation2` (`klassifikation2`),
  KEY `klassifikation3` (`klassifikation3`),
  KEY `buchungsreferenz` (`buchungsreferenz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klassifikation`
--

CREATE TABLE `klassifikation` (
  `klassifikationID` int(32) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `aktiv` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`klassifikationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `konto`
--

CREATE TABLE `konto` (
  `kontoID` varchar(5) NOT NULL,
  `bezeichnung` varchar(32) NOT NULL,
  `kategorie` varchar(3) NOT NULL,
  `kontoNR` varchar(2) NOT NULL,
  `aktiv` enum('Y','N') NOT NULL DEFAULT 'Y',
  `abstMöglich` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`kontoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontoKategorie`
--

CREATE TABLE `kontoKategorie` (
  `kategorieID` VARCHAR(3) NOT NULL,
  `bezeichnung` VARCHAR(32),
  `klasse` VARCHAR(1) NOT NULL,
  `kategorieNR` VARCHAR(2) NOT NULL,
  PRIMARY KEY (`kategorieID`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontoKlasse`
--

CREATE TABLE `kontoKlasse` (
  `klasseID` VARCHAR(1) NOT NULL,
  `bezeichnung` VARCHAR(32),
  `vorzeichen` TINYINT NOT NULL,
  PRIMARY KEY (`klasseID`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `periode`
--

CREATE TABLE `periode` (
  `periodeID` int(32) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(32) NOT NULL,
  PRIMARY KEY (`periodeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `template`
--

CREATE TABLE `template` (
  `templateID` int(32) NOT NULL AUTO_INCREMENT,
  `datumErstellt` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(32) NOT NULL,
  `empfänger` int(32) DEFAULT NULL,
  `reNummer` varchar(64) DEFAULT NULL,
  `buchungstext` text DEFAULT NULL,
  `totalbetrag` float(12,2) NOT NULL DEFAULT 0.00,
  `kontoSoll` varchar(5) DEFAULT NULL,
  `kontoHaben` varchar(5) DEFAULT NULL,
  `periode` int(32) DEFAULT NULL,
  `klassifikation1` int(32) DEFAULT NULL,
  `klassifikation2` int(32) DEFAULT NULL,
  `klassifikation3` int(32) DEFAULT NULL,
  PRIMARY KEY (`templateID`),
  KEY `empfänger` (`empfänger`),
  KEY `kontoSoll` (`kontoSoll`),
  KEY `kontoHaben` (`kontoHaben`),
  KEY `periode` (`periode`),
  KEY `klassifikation1` (`klassifikation1`),
  KEY `klassifikation2` (`klassifikation2`),
  KEY `klassifikation3` (`klassifikation3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `standingOrder`
--

CREATE TABLE `standingOrder` (
  `standingOrderID` INT(11) NOT NULL AUTO_INCREMENT,
  `template` INT(11) NOT NULL COMMENT 'sad',
  `created` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `label` VARCHAR(32) DEFAULT NULL,
  `validFromType` INT(11) NOT NULL,
  `validFrom` DATE NOT NULL,
  `periodicityType` INT(11) NOT NULL,
  `periodicityValue` INT(11) NOT NULL,
  `validToType` INT(11) NOT NULL,
  `validTo` DATE DEFAULT NULL,
  `initialEvents` INT(11) NULL DEFAULT NULL,
  `handledEvents` INT(11) NULL DEFAULT NULL,
  `remainingEvents` INT(11) NULL DEFAULT NULL,
  `nextExecutionDate` DATE NOT NULL,
  PRIMARY KEY(`standingOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Dauerauftrag';

-- --------------------------------------------------------

--
-- Constraints der Tabelle `journal`
--

ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`empfänger`) REFERENCES `empfänger` (`empfängerID`),
  ADD CONSTRAINT `journal_ibfk_2` FOREIGN KEY (`kontoSoll`) REFERENCES `konto` (`kontoID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_ibfk_3` FOREIGN KEY (`kontoHaben`) REFERENCES `konto` (`kontoID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_ibfk_4` FOREIGN KEY (`periode`) REFERENCES `periode` (`periodeID`),
  ADD CONSTRAINT `journal_ibfk_5` FOREIGN KEY (`klassifikation1`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_6` FOREIGN KEY (`klassifikation2`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_7` FOREIGN KEY (`klassifikation3`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_8` FOREIGN KEY (`buchungsreferenz`) REFERENCES `journal` (`buchungID`);

-- --------------------------------------------------------

--
-- Constraints der Tabelle `kontoKategorie`
--

ALTER TABLE `kontoKategorie`
  ADD CONSTRAINT `kontoKategorie_ibfk_1` FOREIGN KEY (`klasse`) REFERENCES `kontoKlasse` (`klasseID`) ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Constraints der Tabelle `konto`
--

ALTER TABLE `konto`
  ADD CONSTRAINT `konto_ibfk_1` FOREIGN KEY (`kategorie`) REFERENCES `kontoKategorie` (`kategorieID`) ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Constraints der Tabelle `template`
--

ALTER TABLE `template`
  ADD CONSTRAINT `template_ibfk_1` FOREIGN KEY(`empfänger`) REFERENCES `empfänger`(`empfängerID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_2` FOREIGN KEY(`kontoSoll`) REFERENCES `konto`(`kontoID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_3` FOREIGN KEY(`kontoHaben`) REFERENCES `konto`(`kontoID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_4` FOREIGN KEY(`periode`) REFERENCES `periode`(`periodeID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_5` FOREIGN KEY(`klassifikation1`) REFERENCES `klassifikation`(`klassifikationID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_6` FOREIGN KEY(`klassifikation2`) REFERENCES `klassifikation`(`klassifikationID`) ON UPDATE CASCADE;
  ADD CONSTRAINT `template_ibfk_7` FOREIGN KEY(`klassifikation3`) REFERENCES `klassifikation`(`klassifikationID`) ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Constraints der Tabelle `standingOrder`
--

ALTER TABLE `standingOrder`
  ADD CONSTRAINT `standingOrder_ibfk_1` FOREIGN KEY (`template`) REFERENCES `template` (`templateID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Trigger der Tabelle `kontoKlasse`
--

CREATE TRIGGER `update_child_kontoKategorie` AFTER UPDATE
ON
  `kontoKlasse` FOR EACH ROW
UPDATE
  kontoKategorie
SET
  kontoKategorie.kategorieID = CONCAT(kontoKategorie.klasse,kontoKategorie.kategorieNR)
WHERE
  kontoKategorie.klasse = NEW.klasseID;

-- --------------------------------------------------------

--
-- Trigger der Tabelle `kontoKategorie`
--

-- generate_kategorieID_insert
CREATE TRIGGER `generate_kategorieID_insert` BEFORE INSERT
ON
  `kontoKategorie` FOR EACH ROW
SET
  NEW.kategorieID = CONCAT(NEW.klasse, NEW.kategorieNR);

-- generate_kategorieID_update
CREATE TRIGGER `generate_kategorieID_update` BEFORE UPDATE
ON
  `kontoKategorie` FOR EACH ROW
SET
  NEW.kategorieID = CONCAT(NEW.klasse, NEW.kategorieNR);

-- update_child_konto
CREATE TRIGGER `update_child_konto` AFTER UPDATE
ON
  `kontoKategorie` FOR EACH ROW
UPDATE
  konto
SET
  konto.kontoID = CONCAT(konto.kategorie,konto.kontoNR)
WHERE
  konto.kategorie = NEW.kategorieID;
  
-- --------------------------------------------------------

--
-- Trigger der Tabelle `konto`
--

-- generate_kontoID_insert
CREATE TRIGGER `generate_kontoID_insert` BEFORE INSERT
ON
  `konto` FOR EACH ROW
SET
  NEW.kontoID = CONCAT(NEW.kategorie, NEW.kontoNR);

-- generate_kontoID_update
CREATE TRIGGER `generate_kontoID_update` BEFORE UPDATE
ON
  `konto` FOR EACH ROW
SET
  NEW.kontoID = CONCAT(NEW.kategorie, NEW.kontoNR);