-- Datenbank Tabellen Vorlage für die Benutzer-Zieldatenbank
-- Gültig ab: Accounting vx.x.x-beta
-- 

-- --------------------------------------------------------

--
-- Struktur des Views `journal view`
--

CREATE ALGORITHM = TEMPTABLE VIEW `journal view` AS SELECT
  `journal`.`entryID`,
  `journal`.`created`,
  `journal`.`date`,
  `recipient`.`label` AS `recipient`,
  `journal`.`invoiceNo`,
  `journal`.`entryText`,
  `journal`.`grandTotal`,
  `debitAccount`.`accountID` AS `debitAccountID`,
  `debitAccount`.`label` AS `debitAccount`,
  `creditAccount`.`accountID` AS `creditAccountID`,
  `creditAccount`.`label` AS `creditAccount`,
  `period`.`label` AS `period`,
  `classification1`.`label` AS `classification1`,
  `classification2`.`label` AS `classification2`,
  `classification3`.`label` AS `classification3`,
  `journal`.`entryReference`,
  `journal`.`reconcilation`
FROM
  `journal`
LEFT JOIN `recipient` ON `journal`.`recipient` = `recipient`.`recipientID`
LEFT JOIN `account` AS `debitAccount` ON `journal`.`debitAccount` = `debitAccount`.`accountID`
LEFT JOIN `account` AS `creditAccount` ON `journal`.`creditAccount` = `creditAccount`.`accountID`
LEFT JOIN `period` ON `journal`.`period` = `period`.`periodID`
LEFT JOIN `classification` AS `classification1` ON `journal`.`classification1` = `classification1`.`classificationID`
LEFT JOIN `classification` AS `classification2` ON `journal`.`classification2` = `classification2`.`classificationID`
LEFT JOIN `classification` AS `classification3` ON `journal`.`classification3` = `classification3`.`classificationID`