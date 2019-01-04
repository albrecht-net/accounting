# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## v2.0.0-beta (2019.01.04)
### Added
- In der Ziel-Datenbank Tabelle "kontoKlasse" können die Konto-Klassen definiert werden.
- In der Ziel-Datenbank Tabelle "kontoKategorie" können die Konto-Kategorien definiert werden. Die Kategorie-ID wird automatisch aus der Klassen-ID und Kategorie-NR generiert.
- Bei einer Änderung der Klassen-, Kategorie- oder Konto-ID werden alle nachfolgenden ID-Nummern automatisch angepasst.
- Mit einem Dauerauftrag ist es möglich, wiederholend daran erinnert zu werden, eine neue Buchung basierend auf einer Vorlage abzuarbeiten.
- Durch die Datenbank-Tabellen-Vorlagen wird  der Zeichensatz auf utf8mb4 festgelegt.
### Changend
- In der Ziel-Datenbank Tabelle "konto" wird die Konto-ID automatisch aus der Kategorien-ID und der Konto-NR generiert.
- Die Auswahlfelder für Konto Haben und Soll in der Eingabemaske werden nach der Konto-Kategorie sortiert.
- Die Tabelle "Template" wurde von der Applikations- in die Ziel-Datenbank verschoben.
- Das Auswahlfeld der Periode auf der Seite "buchung.php" beachtet nun das Datenbankfeld "active" um die Auswahl einzuschränken.
### Fixed
- Bei den Auswahlfelder der Klassifikationen auf der Seite "buchung.php" wird nun das Datenbankfeld "active" beachtet.

---

## v1.4.0-beta (2018.11.18)
### Added
- Eine Buchung kann als Vorlage gespeichert werden oder mit einem generierten URL als Lesezeichen im Browser eingefügt werden.
- Verlinkte Ziel-Datenbanken können in den Einstellungen entfernt werden.
- Sitemap
### Changed
- Favorites zu Templates (Vorlage) umbenannt (Tabelle, Felder in Applikations-Datenbank von "favorites" zu "templates" umbenannt).

---

## v1.0.1-beta (2018.11.07)
### Fixed
- userDbConnect.php Sqlquery Fehlerkorrektur

---

## v1.0.0-beta (2018.11.07)
### Added
- Standard-Datenbank kann beim Login ausgewählt werden welche bei zukünftigen Anmeldungen automatisch ausgewählt wird.
- Die Standard-Datenbank wird in den Einstellungen angezeigt und kann geändert werden.
- Alle verlinkten Ziel-Datenbanken eines Benutzers werden in den Einstellungen aufgelistet.
- In Applikations-Datenbank Tabelle "favorites" hinzugefügt.
- In Applikations-Datenbank Tabelle "userconfig" hinzugefügt.
### Changed
- In Applikations-Datenbank Tabelle "users" die Feldgrösse bei "passwort" von 256 zu 255 geändert.
- Die Ziel-Datenbank für eine aktuelle Sitzung kann nurnoch während dem Loginvorgang geändert werden.

---

## v0.9.1-alpha (2018.11.02)
### Added
- Datenbank-View-Vorlage
### Changed
- In Tabelle "konto" das Feld "abstMoeglich" zu "abstMöglich" geändert.
### Note
- Diese Version ist aufgrund der Neubenennung der Datenbank-Tabellen nicht mehr rückwärzkompatibel. Ausserdem wird auf eine Erhöhung der Hauptversionsnummer verzichtet, da Accounting noch im Alpha Stadium ist.

---

## v0.8.0-alpha (2018.10.31)
### Added
- CSS-Styling über gesamte Applikation
- Datenbank-Tabellen-Vorlage für die Applikations- und Ziel-Datenbank
### Changed
- Tabelle von "buchungen" zu "journal" umbenannt
- Tabelle von "konten" zu "konto" umbenannt
### Note
- Diese Version ist aufgrund der Neubenennung der Datenbank-Tabellen nicht mehr rückwärzkompatibel. Ausserdem wird auf eine Erhöhung der Hauptversionsnummer verzichtet, da Accounting noch im Alpha Stadium ist.

---

## v0.5.0-alpha (2018.10.28)
### Added
- Benutzer Setup für Erstkonfiguration
- Zugriff auf Applikation nur mittels Passwort (Login-System)
- Einstellungsbereich für Passwortänderung und hinzufügen einer neuer Datenbank
- Mehrere Zieldatenbanken möglich pro Benutzer
- Eingabemaske um neue Buchung zu erfassen