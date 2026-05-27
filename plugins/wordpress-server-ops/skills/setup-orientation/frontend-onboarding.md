# Frontend Onboarding (WESEO SmartFlow)

Kurze Orientierung nach dem `setup-orientation` Wizard. Sie erklärt in normalem Deutsch, wie du nach dem ersten Setup sicher weiterarbeitest: was auf dem Server passiert, was später lokal im Frontend passiert, welche drei WESEO-Plugins helfen, und wo Projektinfos ohne Zugangswerte stehen.

Erfahrene SmartFlow-Entwickler können dieses Dokument überspringen. Für neue oder nicht-backend-heavy Kolleginnen und Kollegen ist es die Übergabe nach dem Setup.

Diese Anleitung ersetzt den alten `smartflow-frontend-guide.md`. Sie beschreibt das aktuelle Setup mit drei Cursor-Plugins, der klaren Aufteilung zwischen Server-Arbeit und lokaler Frontend-Arbeit, und `PROJECT-CONTEXT.md` als Projekt-Notizdatei für nicht geheime Fakten.

## Bevor du startest

- `setup-orientation` ist abgeschlossen oder offene Punkte sind als `pending: <reason>` mit nächstem Schritt dokumentiert.
- Cursor hat Zugriff auf die drei WESEO-Plugins im persönlichen Account: `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`.
- Cursor ist per `Remote-SSH` mit dem Server verbunden. Das bedeutet: Cursor arbeitet im Server-Workspace, nicht in einem lokalen Projektordner auf deinem Rechner.
- Der `WordPress root` ist geöffnet. Das ist der Hauptordner der WordPress-Installation mit `wp-content/`, `wp-admin/` und `wp-includes/`.
- `PROJECT-CONTEXT.md` existiert im WordPress-Root und enthält nicht geheime Projektfakten.
- Die Ablageorte für Section-Handoffs und CPT-Handoffs sind in `PROJECT-CONTEXT.md` dokumentiert oder als offener Punkt notiert.
- Browser und Figma-Zugang sind bereit. Falls du die finale Frontend-QA lokal machst, richtest du `Playwright MCP` einmalig in deinem lokalen Cursor-Workspace über `frontend-design-qa` `setup-playwright-mcp` ein. `Playwright MCP` wird nie im `Remote-SSH`-Server-Workspace eingerichtet.

## Drei Plugins, drei einfache Verantwortungen

Der Workflow ist auf drei Plugins aufgeteilt. Die Plugin-Namen bleiben wichtig, weil Cursor sie genau so anzeigt. Praktisch bedeutet es:

| Plugin | Phase | Was es kann |
|---|---|---|
| `wordpress-server-ops` | Server / Setup | Hilft beim sicheren Einrichten und Prüfen des WordPress-Server-Workspaces: Setup-Wizard, WordPress-Terminalbefehle (`WP-CLI`), Cache leeren, Bearbeitungsgrenzen, Webroot-Sicherheit, WordPress-Inhalte und Medien-Import. |
| `wst-builder` | Server / WST-Foundation | Baut die technische Grundlage für neue Inhalte: Flexible Content Sections, Custom Post Types, ACF-Feldgruppen, WP Grid Builder Cards/Grids, CSS-Hooks und Übergaben für die Frontend-Arbeit. |
| `frontend-design-qa` | Lokal / Frontend | Macht die sichtbare Frontend-Arbeit fertig: CSS/SCSS, Figma-zu-Code-Übersetzung, Browser-QA über `Playwright MCP` (einmalig lokal eingerichtet über `setup-playwright-mcp`), kurze Chrome Local Overrides Tests, responsive Prüfung und optional projekt-eigene Playwright-Tests als Regressions-Checks. |

Cursor lädt Rules und Skills aus diesen Plugins automatisch, sobald sie im persönlichen Account aktiv sind. Der Projekt-Repository-Ordner enthält nur eine `.cursor/`-Skeleton-Struktur (`.cursor/rules/.gitkeep`, `.cursor/skills/.gitkeep`). Dort können später projektspezifische Hinweise ergänzt werden, ohne die Plugin-Inhalte zu kopieren.

## Server-Arbeit vs. lokale Frontend-Arbeit

Die Arbeit ist bewusst getrennt. Auf dem Server entstehen Setup, WordPress-Struktur und WST-Grundlagen. Lokal wird die sichtbare Frontend-Qualität finalisiert.

### Server-Workspace (Cursor + `Remote-SSH`)

- WordPress-Root öffnen und Setup mit `setup-orientation` führen.
- Git-Workflow: `git fetch`, Branch-Wechsel, Commits und Push gehen auf dem Server, weil WP Pusher dort Push-to-Deploy auslöst.
- WST-Foundation: neue Flexible Content Sections, Custom Post Types, ACF-Felder, WST-Templates, CSS-Hooks (`wst-builder`).
- Server-PHP, ACF-Konfiguration, WST-Templates, REST/ACF Flexible Content Updates (`wordpress-server-ops`).
- WordPress-Terminalbefehle (`WP-CLI`) und Cache leeren (`cache flush`).
- Media-Import in die WordPress Media Library (`wp-media-import`).
- Figma-Analyse für Sections und Post Types: Vor dem Erstellen einer Section oder eines CPTs muss der Agent die zugehörigen Figma-Designs sehen. So passen HTML-Struktur, ACF-Felder und WST-Layouts später besser zum Design. Die Cursor-Verbindung zu Figma (`Figma MCP`) ist deshalb auch im Server-Workspace aktiv (siehe `setup-orientation` Step 8).

### Lokaler Frontend-Workspace (Cursor lokal, Browser + Playwright MCP)

- Einmalige Einrichtung von `Playwright MCP` über `frontend-design-qa` `setup-playwright-mcp` in der untracked lokalen `.cursor/mcp.json`. Diese Einrichtung passiert nur lokal, nie über `Remote-SSH`.
- Finale CSS/SCSS-Umsetzung gegen die Section-/CPT-Handoffs aus `wst-builder` (`frontend-design-qa`).
- Figma-zu-Code-Übersetzung: Farben/Tokens, Abstände, Typografie und Media-Verhalten in projekt-eigene CSS/SCSS-Dateien übertragen.
- Browser-QA über `Playwright MCP`: Navigation, Snapshot, Screenshot, Desktop/Tablet/Mobile-Viewports, Selektor- und Computed-Style-Checks gegen eine reale Dev-/Staging-URL.
- Chrome Local Overrides als kurzlebige Tests gegen eine reale Dev-/Staging-Seite.
- Responsive-Checks und optional projekt-eigene Playwright-Regression, wenn das Projekt einen echten Test-Harness mitbringt.
- QA-Notizen inkl. `Local Playwright MCP status` ins jeweilige Section- oder CPT-Handoff zurückschreiben.

## `PROJECT-CONTEXT.md` als Projektquelle

`PROJECT-CONTEXT.md` ist die zentrale Projekt-Notizdatei für nicht geheime Fakten. Skills lesen sie zuerst und aktualisieren sie bei neuen sicheren Erkenntnissen. Sie ersetzt den alten Ansatz, Platzhalter direkt in Rule-Dateien einzutragen.

Typischer Inhalt:

- Projekt, Live-/Staging-URL, Server, WordPress-Root, Theme-Pfad, WST-Template-Pfad.
- Repository-Host/Name, Branch, Access-Methode (`token-in-remote-url`, `credential-helper`, `ssh`).
- WP-CLI-Command, Cache-Flush-Command, Safe-Temp-Path.
- Section-Handoff-Ablage und CPT-Handoff-Ablage für die Übergabe zwischen `wst-builder` und `frontend-design-qa`.
- `LEARNINGS.md` Status: vorhanden oder beim ersten echten Learning anzulegen.
- Editier-Policy: erlaubte Pfade, Plugin-Ausnahmen.
- WST-Stack-Status: aktives Theme + Plugins.
- Projekt-Spezifika: CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, Button-Varianten, Container-Widths, Clamp-Werte, ACF IDs.
- Setup-Status pro Schritt: `done`, `pending: <reason>`, `skipped: <reason>` mit nächstem Schritt.

Nicht in `PROJECT-CONTEXT.md`, Chat, Git, Commits, tracked files, Diagnosen, Screenshots oder öffentliche Webroot-Artefakte gehören: Tokens, Application Passwords, SSH Private Keys, tokenhaltige URLs, REST-Credentials, Datenbank-Dumps und Medien-Inventare.

## Bearbeitungsgrenze: was du ändern darfst

Standardmäßig sind nur Dateien unter `wp-content/themes/astra-child/` (oder dem in `PROJECT-CONTEXT.md` genannten Child Theme) bearbeitbar.

`wp-content/plugins/weseo-smart-template-builder/` ist nur dann bearbeitbar, wenn `PROJECT-CONTEXT.md` das Plugin als projekt-eigene Quelle bestätigt hat. Andernfalls gilt es als Vendor-Plugin.

Alles andere (WordPress Core, andere Plugins, Uploads, Config) ist tabu. Falls eine Aufgabe Änderungen außerhalb erfordert, vorher Rücksprache mit dem Maintainer halten.

Details: `wordpress-server-ops` Rule `file-edit-boundary`.

## Praktischer Workflow

### 1. Aufgabe verstehen

Lies die Aufgabe und überlege, welche Phase und welches Plugin zuständig sind:

- Neue Section, bestehende Section umbauen, neuer CPT, neue ACF-Felder → Remote, `wst-builder` (`wst-section-workflow`, `wst-new-post-type`). Rein visuelle Section-Änderungen gehen direkt an `frontend-design-qa` (`frontend-section-qa`).
- Bestehende Section visuell finalisieren → lokal, `frontend-design-qa` (`frontend-section-qa`).
- CPT-Cards, Archive, Single-Templates visuell finalisieren → lokal, `frontend-design-qa` (`cpt-frontend-qa`).
- Bilder in die Mediathek → Remote, `wordpress-server-ops` (`wp-media-import`).
- WP-CLI / Cache / REST-API-Pflege → Remote, `wordpress-server-ops`.

### 2. Figma vor dem Bauen anschauen

Auch im Server-Schritt: Bevor eine Section oder ein CPT erzeugt wird, soll der Agent die Figma-Designs öffnen und verstehen. So passen HTML-Struktur, ACF-Felder, WST-Layouts und stabile CSS-Hooks zum späteren Design. Die Cursor-Verbindung zu Figma (`Figma MCP`) ist dafür auch im Server-Workspace aktiv.

### 3. Handoffs als Übergabe

Server-Phase und lokale Frontend-Phase sind über Handoffs gekoppelt. Ein Handoff ist die schriftliche Übergabe zwischen technischer Grundlage und finaler Frontend-Umsetzung:

- `wst-builder` produziert Section- und CPT-Handoffs auf demselben Branch oder PR. Das Handoff nennt URL, Template-Datei, ACF-Referenzen, CSS-Hooks, erwartetes Verhalten, QA-Hinweise, offene Punkte.
- Die wiederverwendbare Section-Vorlage liegt im `wst-builder` Plugin unter `plugins/wst-builder/handoffs/section-handoff.template.md`. Das ausgefüllte Handoff liegt aber am projekt-konfigurierten Ablageort aus `PROJECT-CONTEXT.md`.
- CPT-Handoffs haben einen eigenen Ablageort, weil sie zusätzlich Archiv, Taxonomie, WP Grid Builder, Cards, Carousel, Detailseite und optionale Single-Templates betreffen können.
- `frontend-design-qa` liest das Handoff vor der Umsetzung, ergänzt nach der Umsetzung Status, Responsive-/Playwright-Ergebnis und verbleibende Risiken.

### 4. Editieren - nur im erlaubten Bereich

- CSS/SCSS in den freigegebenen Theme-Pfaden, nie inline, nie `<style>`-Blöcke.
- Neue CSS-Dateien gemäß der projekt-eigenen Loader-/`styles.json`-Konvention registrieren.
- `functions.php` nie anfassen. Wenn `theme-functions.php` oder ein MU-Plugin nötig wirkt, muss der Agent den geplanten Eingriff vorher nennen und fragen, ob das ok ist.
- WST-Templates nur im freigegebenen WST-Pfad ändern.

### 5. Testen und Cache-Themen richtig routen

Nach Template- oder DB-Änderungen den Cache flushen. Genauer Befehl steht in `PROJECT-CONTEXT.md`. WESEO-Default:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

Wichtig für die lokale Frontend-Phase: `frontend-design-qa` führt keinen Server- oder Cache-Eingriff selbst aus. Wenn eine lokale QA nach stale Markup, alter Server-Ausgabe oder Cache aussieht, notiert der Agent das Symptom im Handoff und routet die Aktion zurück an `wordpress-server-ops` oder an den in `PROJECT-CONTEXT.md` dokumentierten Server-Schritt.

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
- Section- und CPT-Handoff-Ablagen aus `PROJECT-CONTEXT.md` verwenden; keine Handoffs in Plugin-Paketordner schreiben.
- Drei Plugins, drei Phasen: `wordpress-server-ops` (Setup/Server), `wst-builder` (WST-Foundation), `frontend-design-qa` (lokales Frontend).
- Figma sowohl Remote (für Section/CPT-Vorbereitung) als auch lokal (für finale Umsetzung) nutzen.
- Nur in `astra-child/` editieren - WST-Plugin nur, wenn `PROJECT-CONTEXT.md` es freigibt.
- Nie `functions.php` ändern. `theme-functions.php` oder MU-Plugin nur nach vorheriger Ankündigung und Bestätigung.
- Nie inline Styles → CSS-Klassen in den freigegebenen Style-Pfaden.
- Neue CSS-Dateien gemäß projekt-eigener Style-Loader-Konvention registrieren.
- Git: `git fetch` statt blindem `pull origin master`; Branch-Wechsel nur nach Bestätigung.
- WP Pusher = Push-to-Deploy → immer committen und pushen.
- Stoppen und fragen, wenn Handoff-Ablage, Template-Pfad, ACF/WST-Referenzen, Cache-Zuständigkeit oder erlaubter Editierbereich unklar sind.
- Code und Commits auf Englisch, Commit-Keywords `FEATURE`, `FIX`, `DEV`.
- Keine temp/backup/sensiblen Dateien im öffentlichen Webroot - Safe-Temp-Path aus `PROJECT-CONTEXT.md` verwenden.
- Echte Tokens, Application Passwords, SSH Keys und tokenhaltige URLs nie in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen, Screenshots oder öffentliche Webroot-Artefakte schreiben.
- Learnings in `LEARNINGS.md`, projektspezifische Fakten in `PROJECT-CONTEXT.md`, allgemeingültige Standards in Plugin-Rules.
