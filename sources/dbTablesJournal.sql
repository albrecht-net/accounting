-- Datenbank Tabellen Vorlage für die Benutzer-Zieldatenbank
-- Gültig ab: Accounting v0.8.0-alpha
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
  `aktiv` enum('Y','N') NOT NULL DEFAULT 'Y',
  `abstMöglich` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`kontoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Constraints der Tabelle `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`empfänger`) REFERENCES `empfänger` (`empfängerID`),
  ADD CONSTRAINT `journal_ibfk_2` FOREIGN KEY (`kontoSoll`) REFERENCES `konto` (`kontoID`),
  ADD CONSTRAINT `journal_ibfk_3` FOREIGN KEY (`kontoHaben`) REFERENCES `konto` (`kontoID`),
  ADD CONSTRAINT `journal_ibfk_4` FOREIGN KEY (`periode`) REFERENCES `periode` (`periodeID`),
  ADD CONSTRAINT `journal_ibfk_5` FOREIGN KEY (`klassifikation1`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_6` FOREIGN KEY (`klassifikation2`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_7` FOREIGN KEY (`klassifikation3`) REFERENCES `klassifikation` (`klassifikationID`),
  ADD CONSTRAINT `journal_ibfk_8` FOREIGN KEY (`buchungsreferenz`) REFERENCES `journal` (`buchungID`);