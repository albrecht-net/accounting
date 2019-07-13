# Changelog (accounting application)
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## v2.32.0-beta (2019.xx.xx)
### Added
- Beim Eingabeformular einer neuen Buchung (entry.php) werden in einer eigenen Karte die Vorlagen verlinkt. Diese Karte ist auf Mobilgeräten zur besseren Übersicht ausgeblendet.

---

## v2.31.1-beta (2019.06.22)
### Added
- In der Karte "Saldo Übersicht" auf der Startseite (index.php) werden die aktuellen Saldos aller aktiven Konten angezeigt.
- Es wird die Summe aller ausgewählten Einträge der Buchungsreferenz, sowie die daraus resultierende Differenz zum Betrags-Eingabefeld, angezeigt [IS #42](https://github.com/albrecht-net/accounting/issues/42).
### Changed
- Design der Karte "Daueraufträge" (entry.php#standingOrder) verbessert.
### Fixed
- Die Tabellen "Erfasste Empfänger" (recipient.php) und "Erfasste Klassifikationen" (classification.php) überlaufen auf schmalen Displays (zB. Smartphone) rechtsseitig nicht mehr [IS #40](https://github.com/albrecht-net/accounting/issues/40).

---

## v2.28.0-beta (2019.05.28)
### Changed
- Alle Abschnitte der Applikation sind optisch durch Karten hervorgehoben.
- Das Auswahlfeld für die Buchungsreferenz (entry.php) wird neu nach dem Buchungsdatum, und nicht mehr nach dem Erstelldatum sortiert.

---

## v2.26.0-beta (2019.05.21)
### Added
- Die Einträge der Tabelle "Zuletzt erfasste Buchungen" (index.php) können mit Filter für das Konto und den Zeitraum eingeschränkt werden. Standard ist weiterhin die 10 Letzten Buchungen aller Konten.

---

## v2.25.0-beta (2019.05.08)
### Changed
- Die Tabelle der Gespeicherten Vorlagen (templates.php) wird nun standardmässig nach der Spalte "Name" sortiert.
- Es kann nicht mehr nach der Spalte "Definierte Werte" in der Tabelle der Gespeicherten Vorlagen (templates.php) sortiert werden, da diese Daten nicht sortierbar sind.

---

## v2.23.2-beta (2019.04.28)
### Added
- Daueraufträge können in der Übersicht (standingOrder.php) manuell ausgewählt werden, auch wenn das Fälligkeitsdatum noch nicht erreicht ist. Das Datum der nächsten Ausführung wird dabei nicht verändert.
- Die Aktuelle Versionsbezeichnung von Accounting wird auf der Loginseite (login.php) angezeigt.
- In der Navigation sind Informationen sowie das Changelog der genutzten Version von Accounting verfügbar.
- Ein Hinweis auf der Seite settings/database.php verlinkt zur Vorlage der Zieldatenbank.
### Fixed
- Anpassungen um PHP-Warnungen beim Speichern einer neuen Buchung zu verhindern.
### Removed
- Obsolete Checkbox "Abstimmung" im Eingabgeformular einer neue Buchung (entry.php#newEntry) wurde entfernt.
- Die Seite "buchung.php" wurde entfernt und dient nun nicht mehr als Weiterleitung zu "entry.php".
### Note
- In der hinzugefügten Versionsübersicht ([versionOverview.xlsx](versionOverview.xlsx)) sind alle für Accounting benötigten Bibliotheken und Datenbanken mit deren Version aufgelistet. Die Kompabilität der unterschiedlichen Softwareversionen kann daraus gelesen werden. Da es sich bei dieser Übersicht um eine Repository übergreifende Liste handelt, ist diese kein Bestandteil der Veröffentlichungen der [albrecht-net/accounting](https://github.com/albrecht-net/accounting) Repository.

---

## v2.19.0-beta (2019.03.29)
### Added
- Grundlegendes Dashboard auf der Hauptseite (index.php) in welchem die 10 zuletzt erstellten Buchungen angezeigt werden.
- [Datatables](https://datatables.net/) implementiert. Dies erlaubt erlaubt erweiterte Funktionen in ausgewählten Tabellen.
- Das Löschen einer Datenbank-Verknüpfung muss nun bestätigt werden.
### Changed
- Daten der Tabelle "Erfasste Daueraufträge" (standingOrder.php) können durchsucht und sortiert werden.
- Daten der Tabelle "Erfasste Empfänger" (recipient.php) können durchsucht und sortiert werden.
- Daten der Tabelle "Erfasste Klassifikationen" (classification.php) können durchsucht und sortiert werden.
- Daten der Tabelle "Gespeicherte Vorlagen" (templates.php) können durchsucht und sortiert werden.
- Die Ausgewählte Standard Datenbank wird neu in der Tabelle (settings/database.php#linkedDatabase) als solche gekennzeichnet.

---

## v2.11.1-beta (2019.03.21)
### Added
- Für Daueraufträge ist nun der Wiederholungstyp Montag - Freitag möglich.
- Daueraufträge können übersprungen werden, sodass für das aktuell fällige Datum keine Buchung erstellt werden muss. Die Anzahl der ausgeführten Buchungen eines Dauerauftrags wird aber trotzdem erhöht.
### Changed
- Die Seite "buchung.php" wurde ins Englische umbenannt und heisst neu "entry.php". 
### Fixed
- Der Wert für das Feld "Rechnungsnummer" kann nun mittels Vorlagen definiert werden.
### Deprecated
- Die Seite "buchung.php" leitet nun noch vorübergehend alle Anfragen automatisch weiter zu "entry.php". Diese Funktion wird in einer zukünftigen Version aus Accounting entfernt.
---

## v2.8.0-beta (2019.02.26)
### Changed
- Im Eingabeformular um eine neue Buchung zu erfassen wird nun automatisch das letzte Jahr, welches im Feld "active" der Tabelle "period" auf Y gesetzt ist, ausgewählt.

---

## v2.7.2-beta (2019.02.25)
### Fixed
- Nach erfolgreichem Anmelden wird kein Leerer Alarm mehr angezeigt.

---

## v2.7.1-beta (2019.02.25)
### Added
- Eingabeformular um neue Empfänger zu erfassen.
- Eingabeformular um neue Klassifikation zu erfassen.
### Changed
- Alle Eingabeformulare werden nun direkt aufgerufen, es wird damit verhindert, dass durch ein Neuladen der Seite das Formular erneut gesendet wird.
- Systemdateien (includes/*.inc.php) können nurnoch durch eine Anfrage der Applikation aufgerufen werden und geben sonst einen Fehler aus.
- Das Auswahlfeld der Buchungsreferenz auf der Seite "buchung.php" zeigt nun nur noch Buchungen an bei welchen das Haben-Konto zur Abstimmung markiert ist.
- Verhinderung von Cross Site Scripting (XSS).
- Alle Datenbank-Vorlagen (/sources) wurden in eine eigene Repository ([albrecht-net/accounting-database](https://github.com/albrecht-net/accounting-database)) verschoben. Dies erleichtert die Versionierung der Datenbank.
### Fixed
- Das rechte Auswahlfeld in der Navigationsleiste ist nun rechtsbündig und sommit immer vollständig sichtbar.

---

## v2.0.5-beta (2019.01.24)
### Fixed
- Fehler behoben welcher das speichern einer Vorlage verhindert hat.
- URL der Navigation bei settings/account.php korrigiert.
- Neue Buchungen (nicht Dauerauftrag) können wieder gespeichert werden.

---

## v2.0.2-beta (2019.01.17)
### Fixed
- Das Feld "customerNumber" in der Tabelle "recipient" muss keinen Wert mehr enthalten.

---

## v2.0.1-beta (2019.01.05)
### Fixed
- Es wird nur bei ausgewählter Datenbank auf fällige Daueraufträge geprüft.

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