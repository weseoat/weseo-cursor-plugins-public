# Frontend Onboarding (WESEO SmartFlow)

Optionale Orientierung für Entwickler, die ein WST-Projekt frisch eingerichtet haben (siehe [SKILL.md](SKILL.md)) und mit dem aktuellen Drei-Plugin-Workflow auf Remote-Server und lokal arbeiten möchten. Erfahrene SmartFlow-Entwickler können dieses Dokument überspringen.

Diese Anleitung ersetzt den alten `smartflow-frontend-guide.md`. Sie modernisiert ihn für das aktuelle Setup mit drei Cursor-Plugins, getrennter Remote/lokal-Arbeitsweise, und `PROJECT-CONTEXT.md` als zentraler nicht-geheimer Projektquelle.

## Voraussetzungen

- `setup-orientation` ist abgeschlossen oder offene Punkte sind als `pending: <reason>` mit nächstem Schritt dokumentiert.
- Cursor läuft mit den drei WESEO-Plugins im persönlichen Account: `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`.
- Cursor ist per Remote-SSH mit dem Server verbunden und der WordPress-Root ist geöffnet.
- `PROJECT-CONTEXT.md` existiert im WordPress-Root und enthält die nicht-geheimen Projektfakten.
- Lokale Entwicklungsumgebung (Browser, Figma-Zugang, optional Playwright) ist eingerichtet, falls Frontend-QA lokal stattfindet.

## Drei Plugins, drei Verantwortungen

Der Workflow ist auf drei Plugins aufgeteilt. Jedes Plugin ist für eine Phase zuständig und enthält die zugehörigen Rules und Skills.

| Plugin | Phase | Was es kann |
|---|---|---|
| `wordpress-server-ops` | Server / Setup | Setup-Wizard, WP-CLI/Cache, File Boundaries, Webroot Safety, WordPress Content Editing, Media Import. |
| `wst-builder` | Server / WST-Foundation | Neue Flexible Content Sections, neue Custom Post Types, ACF-Feldgruppen, WP Grid Builder Cards/Grids, CSS-Hooks, Section/CPT Handoffs. |
| `frontend-design-qa` | Lokal / Frontend | Finale CSS/SCSS-Umsetzung, Figma-zu-Code-Übersetzung, Chrome Local Overrides als Spike, Responsive- und Playwright-Checks. |

Cursor lädt Rules und Skills aus diesen Plugins automatisch, sobald sie im persönlichen Account aktiv sind. Der Projekt-Repository-Ordner enthält nur eine `.cursor/`-Skeleton-Struktur (`.cursor/rules/.gitkeep`, `.cursor/skills/.gitkeep`); projektspezifische Rules und Skills können dort später ergänzt werden, ohne mit den Plugin-Inhalten zu kollidieren.

## Remote vs. lokal arbeiten

Die Arbeit verteilt sich klar auf Remote und lokal. Beide Phasen verwenden Cursor, aber mit unterschiedlichen Tools.

### Remote (Cursor + Remote-SSH zum Server)

- WordPress-Root öffnen, Setup orientieren (`setup-orientation`).
- Git-Workflow: `git fetch`, Branch-Wechsel, Commits und Push gehen auf dem Server, weil WP Pusher dort Push-to-Deploy auslöst.
- WST-Foundation: neue Flexible Content Sections, Custom Post Types, ACF-Felder, WST-Templates, CSS-Hooks (`wst-builder`).
- Server-PHP, ACF-Konfiguration, WST-Templates, REST/ACF Flexible Content Updates (`wordpress-server-ops`).
- WP-CLI-Befehle und Cache Flush.
- Media-Import in die WordPress Media Library (`wp-media-import`).
- Figma-Analyse für Sections und Post Types: Vor dem Erstellen einer Section oder eines CPTs muss der Agent die zugehörigen Figma-Designs sehen, damit HTML-Struktur, ACF-Felder und WST-Layouts auf das Design vorbereitet werden. Gut vorbereitetes HTML ist die Grundlage für gutes Styling. Figma MCP ist deshalb auch im Remote-Workspace aktiv (siehe `setup-orientation` Step 8).

### Lokal (Cursor lokal, optional Browser + Playwright)

- Finale CSS/SCSS-Umsetzung gegen die Section-/CPT-Handoffs aus `wst-builder` (`frontend-design-qa`).
- Figma-zu-Code-Übersetzung: Tokens, Spacing, Typografie, Media-Verhalten in projekt-eigene CSS/SCSS-Dateien.
- Chrome Local Overrides als kurzlebige Spikes gegen eine reale Dev-/Staging-Seite.
- Responsive-Checks und Playwright-Acceptance gegen die Dev-/Staging-URL.
- QA-Notizen ins jeweilige Section- oder CPT-Handoff zurückschreiben.

## `PROJECT-CONTEXT.md` als Projektquelle

`PROJECT-CONTEXT.md` ist die zentrale, nicht-geheime Projektquelle für Skills und Rules. Skills lesen sie zuerst und aktualisieren sie bei neuen nicht-geheimen Erkenntnissen. Sie ersetzt den alten Ansatz, Platzhalter direkt in Rule-Dateien einzutragen.

Typischer Inhalt:

- Projekt, Live-/Staging-URL, Server, WordPress-Root, Theme-Pfad, WST-Template-Pfad.
- Repository-Host/Name, Branch, Access-Methode (`token-in-remote-url`, `credential-helper`, `ssh`).
- WP-CLI-Command, Cache-Flush-Command, Safe-Temp-Path.
- Editier-Policy: erlaubte Pfade, Plugin-Ausnahmen.
- WST-Stack-Status: aktives Theme + Plugins.
- Projekt-Spezifika: CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, Button-Varianten, Container-Widths, Clamp-Werte, ACF IDs.
- Setup-Status pro Schritt: `done`, `pending: <reason>`, `skipped: <reason>` mit nächstem Schritt.

Nicht in `PROJECT-CONTEXT.md`: Tokens, Application Passwords, SSH Private Keys, tokenhaltige URLs, REST-Credentials, Datenbank-Dumps, Medien-Inventare.

## Bearbeitungsgrenze

Standardmäßig sind nur Dateien unter `wp-content/themes/astra-child/` (oder dem in `PROJECT-CONTEXT.md` genannten Child Theme) bearbeitbar.

`wp-content/plugins/weseo-smart-template-builder/` ist nur dann bearbeitbar, wenn `PROJECT-CONTEXT.md` das Plugin als projekt-eigene Quelle bestätigt hat. Andernfalls gilt es als Vendor-Plugin.

Alles andere (WordPress Core, andere Plugins, Uploads, Config) ist tabu. Falls eine Aufgabe Änderungen außerhalb erfordert, vorher Rücksprache mit dem Maintainer halten.

Details: `wordpress-server-ops` Rule `file-edit-boundary`.

## Praktischer Workflow

### 1. Aufgabe verstehen

Lies die Aufgabe und überlege, welche Phase und welches Plugin zuständig sind:

- Neue Section / neuer CPT / neue ACF-Felder → Remote, `wst-builder` (`wst-new-fc-section`, `wst-new-post-type`).
- Bestehende Section visuell finalisieren → lokal, `frontend-design-qa` (`frontend-section-qa`).
- CPT-Cards, Archive, Single-Templates visuell finalisieren → lokal, `frontend-design-qa` (`cpt-frontend-qa`).
- Bilder in die Mediathek → Remote, `wordpress-server-ops` (`wp-media-import`).
- WP-CLI / Cache / REST-API-Pflege → Remote, `wordpress-server-ops`.

### 2. Figma vor dem Bauen anschauen

Auch im Remote-Schritt: Bevor eine Section oder ein CPT erzeugt wird, soll der Agent die Figma-Designs öffnen und verstehen. So passen HTML-Struktur, ACF-Felder, WST-Layouts und stabile CSS-Hooks zum späteren Design. Figma MCP ist dafür auch im Remote-Workspace aktiv.

### 3. Handoffs als Übergabe

Server-Phase und Lokal-Phase sind über Handoffs gekoppelt:

- `wst-builder` produziert Section- und CPT-Handoffs auf demselben Branch oder PR. Das Handoff nennt URL, Template-Datei, ACF-Referenzen, CSS-Hooks, erwartetes Verhalten, QA-Hinweise, offene Punkte.
- `frontend-design-qa` liest das Handoff vor der Umsetzung, ergänzt nach der Umsetzung Status, Responsive-/Playwright-Ergebnis und verbleibende Risiken.

### 4. Editieren - nur im erlaubten Bereich

- CSS/SCSS in den freigegebenen Theme-Pfaden, nie inline, nie `<style>`-Blöcke.
- Neue CSS-Dateien gemäß der projekt-eigenen Loader-/`styles.json`-Konvention registrieren.
- `functions.php` nicht anfassen - stattdessen `theme-functions.php` oder ein MU-Plugin, sofern `PROJECT-CONTEXT.md` das vorsieht.
- WST-Templates nur im freigegebenen WST-Pfad ändern.

### 5. Testen und Cache leeren

Nach Template- oder DB-Änderungen den Cache flushen. Genauer Befehl steht in `PROJECT-CONTEXT.md`. WESEO-Default:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

### 6. Git-Workflow

WP Pusher arbeitet auf dem Server mit Push-to-Deploy. Verwende keine blinden `git pull origin master`-Schritte; stattdessen:

```sh
git fetch origin
git status --short
git add <approved-paths>
git commit -m "<KEYWORD> - <Beschreibung>"
git push origin "$(git branch --show-current)"
```

Commit-Keywords: `FEATURE`, `FIX`, `DEV`. Code und Commits auf Englisch.

Niemals uncommittete Änderungen auf dem Server liegen lassen.

## Learnings dokumentieren

### Was ist ein Learning?

Eine praxisrelevante Erkenntnis, die erst beim Arbeiten entdeckt wurde - kein vorab bekannter Standard.

### Was gehört wohin?

| Typ | Ziel |
|---|---|
| Verbindliche Standards, Architektur, Do/Don'ts | In die zuständige Plugin-Rule (Pull Request gegen das Plugin-Repo) oder in projektlokale `.cursor/rules/`. |
| Projektspezifische Fakten | In `PROJECT-CONTEXT.md`. |
| Stolperfallen, Workarounds, Eigenheiten | In `LEARNINGS.md` im WordPress-Root. |

### Beispiele

Plugin-Rule:
> "Neue CSS-Dateien immer im projekt-eigenen Style-Loader registrieren."

`PROJECT-CONTEXT.md`:
> "FC Field Key: `field_xyz123`. Standard-Container-Width: `1320px`."

`LEARNINGS.md`:
> "Footer-Selector braucht höhere Spezifität als erwartet, weil Astra die Kaskade mit `!important` überschreibt."

### Format für `LEARNINGS.md`

```markdown
## [Thema/Kategorie]

### Kurzer Titel
- **Datum:** YYYY-MM-DD
- **Kontext:** Worum ging es?
- **Erkenntnis:** Was wurde gelernt?
```

Wenn ein Learning dauerhaft und allgemein gültig ist, sollte es in eine Plugin-Rule überführt werden.

## Wichtige Regeln auf einen Blick

- Setup-Status und Projektfakten in `PROJECT-CONTEXT.md` führen.
- Drei Plugins, drei Phasen: `wordpress-server-ops` (Setup/Server), `wst-builder` (WST-Foundation), `frontend-design-qa` (lokales Frontend).
- Figma sowohl Remote (für Section/CPT-Vorbereitung) als auch lokal (für finale Umsetzung) nutzen.
- Nur in `astra-child/` editieren - WST-Plugin nur, wenn `PROJECT-CONTEXT.md` es freigibt.
- Nie `functions.php` ändern → `theme-functions.php` oder MU-Plugin.
- Nie inline Styles → CSS-Klassen in den freigegebenen Style-Pfaden.
- Neue CSS-Dateien gemäß projekt-eigener Style-Loader-Konvention registrieren.
- Git: `git fetch` statt blindem `pull origin master`; Branch-Wechsel nur nach Bestätigung.
- WP Pusher = Push-to-Deploy → immer committen und pushen.
- Code und Commits auf Englisch, Commit-Keywords `FEATURE`, `FIX`, `DEV`.
- Keine temp/backup/sensiblen Dateien im öffentlichen Webroot - Safe-Temp-Path aus `PROJECT-CONTEXT.md` verwenden.
- Echte Tokens, Application Passwords, SSH Keys nie in Chat, getrackte Dateien oder Commits.
- Learnings in `LEARNINGS.md`, projektspezifische Fakten in `PROJECT-CONTEXT.md`, allgemeingültige Standards in Plugin-Rules.
