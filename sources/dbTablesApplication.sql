-- Datenbank Tabellen Vorlage für die Applikations-Datenbank
-- Gültig ab: Accounting v0.8.0-alpha
-- 

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `databases`
--

CREATE TABLE `databases` (
  `dbID` int(32) NOT NULL AUTO_INCREMENT,
  `datumErstellt` datetime NOT NULL,
  `userID` int(32) NOT NULL,
  `dbHost` varchar(32) NOT NULL,
  `dbPort` int(16) NOT NULL,
  `dbUsername` varchar(64) NOT NULL,
  `dbPassword` varchar(64) NOT NULL,
  `dbName` varchar(64) NOT NULL,
  PRIMARY KEY (`dbID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorites`
--

CREATE TABLE `favorites` (
  `favoriteID` int(32) NOT NULL AUTO_INCREMENT,
  `userID` int(32) NOT NULL,
  `dbID` int(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text() NOT NULL,
  PRIMARY KEY (`favoriteID`),
  KEY `userID` (`userID`),
  KEY `dbID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `userID` int(32) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `activation` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Constraints der Tabelle `databases`
--
ALTER TABLE `databases`
  ADD CONSTRAINT `databases_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

-- --------------------------------------------------------

--
-- Constraints der Tabelle `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`dbD`) REFERENCES `db` (`dbD`);