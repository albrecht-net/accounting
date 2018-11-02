-- Datenbank Tabellen Vorlage für die Benutzer-Zieldatenbank
-- Gültig ab: Accounting v0.8.0-alpha
-- 

-- --------------------------------------------------------

--
-- Struktur des Views `journal view`
--

CREATE ALGORITHM = TEMPTABLE VIEW `journal view` AS SELECT
  `journal`.`buchungID`,
  `journal`.`datumErstellt`,
  `journal`.`datum`,
  `empfänger`.`bezeichnung` AS `empfänger`,
  `journal`.`reNummer`,
  `journal`.`buchungstext`,
  `journal`.`totalbetrag`,
  `kontoSoll`.`kontoID` AS `kontoSollID`,
  `kontoSoll`.`bezeichnung` AS `kontoSoll`,
  `kontoHaben`.`kontoID` AS `kontoHabenID`,
  `kontoHaben`.`bezeichnung` AS `kontoHaben`,
  `periode`.`bezeichnung` AS `periode`,
  `klassifikation1`.`bezeichnung` AS `klassifikation1`,
  `klassifikation2`.`bezeichnung` AS `klassifikation2`,
  `klassifikation3`.`bezeichnung` AS `klassifikation3`,
  `journal`.`buchungsreferenz`,
  `journal`.`abstimmung`
FROM
  `journal`
LEFT JOIN `empfänger` ON `journal`.`empfänger` = `empfänger`.`empfängerID`
LEFT JOIN `konto` AS `kontoSoll` ON `journal`.`kontoSoll` = `kontoSoll`.`kontoID`
LEFT JOIN `konto` AS `kontoHaben` ON `journal`.`kontoHaben` = `kontoHaben`.`kontoID`
LEFT JOIN `periode` ON `journal`.`periode` = `periode`.`periodeID`
LEFT JOIN `klassifikation` AS `klassifikation1` ON `journal`.`klassifikation1` = `klassifikation1`.`klassifikationID`
LEFT JOIN `klassifikation` AS `klassifikation2` ON `journal`.`klassifikation2` = `klassifikation2`.`klassifikationID`
LEFT JOIN `klassifikation` AS `klassifikation3` ON `journal`.`klassifikation3` = `klassifikation3`.`klassifikationID`