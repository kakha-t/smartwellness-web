# Projekt Titel

**SmartWellness Web**  
(Mein Webprogrammierungsprojekt im Rahmen des Wirtschaftsinformatik-Studiums)

# Beschreibung

SmartWellness Web ist eine webbasierte Gesundheitsplattform für gesunde Ernährung, Fitness & Bewegung sowie individuelle Tagespläne. Die Anwendung wurde mit HTML, CSS, JavaScript, PHP und MySQL entwickelt und lokal über XAMPP umgesetzt.

## Ernährungsangebote

In diesem Bereich verlinkt SmartWellness auf externe Plattformen mit Rezepten, Ernährungstipps und gesundheitsbezogenen Informationen. Die Webseite dient als übersichtliche Sammlung vertrauenswürdiger Anbieter und unterstützt Nutzerinnen und Nutzer bei einer ausgewogenen Ernährung.

## Fitness & Bewegung

SmartWellness präsentiert verschiedene externe Anbieter aus den Bereichen Fitnessstudio, Yoga, Pilates, Muskeltraining und Aquatraining. Über eine kachelbasierte Benutzeroberfläche gelangen Nutzer direkt zu den jeweiligen Webseiten der Anbieter und Trainingsangebote.

## Personalisierte Ernährungspläne

Registrierte Nutzer können individuelle Tagespläne erstellen und verwalten. Lebensmittel werden aus einer Datenbank ausgewählt und automatisch hinsichtlich Kalorien, Fett, Eiweiß, Kohlenhydraten und glykämischem Index berechnet. Die Pläne können gespeichert, bearbeitet und erneut angezeigt werden.
# Features

- **Moderne Benutzeroberfläche mit HTML, CSS und JavaScript**
  - Responsive und übersichtliche Weboberfläche
  - Kachelbasierte Darstellung von Ernährungs- und Fitnessbereichen
  - Benutzerfreundliche Navigation zwischen den Hauptseiten

- **Benutzerregistrierung und Login**
  - Registrierung neuer Nutzer über PHP und MySQL
  - Login-System mit Sessions
  - Passwortspeicherung mit Hashing

- **Individuelle Ernährungspläne**
  - Auswahl von Lebensmitteln aus Datenbank
  - Dynamisches Hinzufügen und Entfernen von Produkten
  - Speicherung persönlicher Tagespläne

- **Automatische Nährwertberechnung**
  - Berechnung von:
    - Kalorien
    - Fett
    - Eiweiß
    - Kohlenhydraten
    - Glykämischem Index
  - Sofortige Aktualisierung ohne Seitenreload

- **Dynamische Datenverarbeitung**
  - Nutzung von JavaScript fetch()
  - FormData-Übertragung
  - JSON-basierte Antworten zwischen Frontend und Backend

- **PHP-Backend**
  - Verarbeitung von Formularen
  - Datenbankabfragen mit Prepared Statements
  - CRUD-Operationen für Tagespläne und Produkte

- **MySQL-Datenbank**
  - Speicherung von:
    - Benutzerdaten
    - Lebensmitteln
    - Tagesplänen
  - Flexible Speicherung von Produktlisten als JSON

- **Externe Gesundheits- und Fitnessplattformen**
  - Verlinkung vertrauenswürdiger Anbieter
  - Ernährungs- und Rezeptplattformen
  - Fitness-, Yoga- und Aquatraining-Angebote

- **Datenschutz und Sicherheit**
  - Passwort-Hashing
  - Schutz vor SQL-Injection
  - Datenschutzerklärung und Impressum integriert

---

# Architektur & Tech Stack

| Bereich | Technologie / Bibliothek | Zweck |
|---|---|---|
| Frontend | HTML5 | Struktur der Webseiten |
| Styling | CSS3 | Design und responsive Oberfläche |
| Interaktivität | JavaScript | Dynamische Benutzerinteraktionen |
| Datenübertragung | fetch(), FormData, JSON | Asynchrone Kommunikation |
| Backend | PHP | Verarbeitung von Formularen und Daten |
| Datenbank | MySQL | Speicherung von Nutzerdaten und Plänen |
| Entwicklungsumgebung | XAMPP | Lokaler Apache- und MySQL-Server |
| Sicherheit | password_hash(), Prepared Statements | Schutz von Benutzerdaten |
| Datenstruktur | JSON | Speicherung von Tagesplänen |
| Design & Planung | Figma, Canva | UI-Planung und Prototyping |
| Versionsverwaltung | Git & GitHub | Projektverwaltung und Veröffentlichung |
# Screenshots / Demo

Hier ein kurzer Einblick in die SmartWellness-Webanwendung:

| Home | Ernährung | Fitness | Plan |
|------|-----------|---------|------|
| HomeScreen | NutritionScreen | FitnessScreen | PlanScreen |

- **Home:** Startseite mit Begrüßung, Navigation und Einstieg in die Hauptbereiche.
- **Ernährungsangebote:** Übersicht externer Rezept- und Informationsplattformen.
- **Fitness & Bewegung:** Anbieter-Kacheln mit Fitnessstudios, Yoga, Pilates und Aquatraining.
- **Plan:** Interaktiver Tagesplaner mit Produktauswahl und automatischer Nährwertberechnung.
