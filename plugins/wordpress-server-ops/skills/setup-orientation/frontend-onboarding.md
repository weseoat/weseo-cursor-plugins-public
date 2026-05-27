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
| `wst-builder` | Server / WST-Foundation | Produktiver Server-Implementierungs-Workflow mit Safety-Stops. Klassifiziert die Aufgabe (neue Section, Section-Remodel, neuer CPT, visual-only) und baut die Server-Grundlage: Flexible Content Sections, Custom Post Types, Taxonomien, ACF-Feldgruppen, WP Grid Builder Cards/Grids, optionale Single-Templates, CSS-Hooks und das Git-tracked Section-/CPT-Handoff für die Frontend-Phase. Stoppt nur bei konkreten Risiken (Live/Unknown-Schreibvorgänge, Reklassifizierung, Schreibversuche in den WST-Plugin-Ordner). |
| `frontend-design-qa` | Lokal / Frontend | Produktiver lokaler QA-/CSS-Implementierungs-Workflow mit Safety-Stops. Liest das Git-tracked Section-/CPT-Handoff, oder legt für visual-only Aufgaben ohne Handoff ein temporäres Mini-Handoff an. Beweist geplante CSS-Regeln zuerst per `CSS-Injection-Proof` gegen die echte Dev/Staging-Seite, schreibt sie dann in tracked Theme-Dateien, pusht über Git, wartet auf User-Bestätigung, dass der Server gepullt/deployed hat, und schließt erst nach erfolgreicher `Source-Served-Verification` ab. Browser-QA über `Playwright MCP` (einmalig lokal eingerichtet über `setup-playwright-mcp`), responsive Prüfung, optional projekt-eigene Playwright-Regression. Stoppt bei fehlendem Browser-Zugriff (Login, Cookie-Wall, IP-Allowlist) und bei Server-/ACF-/WST-Diskrepanzen, die zurück an `wst-builder` gehören. |

Cursor lädt Rules und Skills aus diesen Plugins automatisch, sobald sie im persönlichen Account aktiv sind. Der Projekt-Repository-Ordner enthält nur eine `.cursor/`-Skeleton-Struktur (`.cursor/rules/.gitkeep`, `.cursor/skills/.gitkeep`). Dort können später projektspezifische Hinweise ergänzt werden, ohne die Plugin-Inhalte zu kopieren.

## Wichtigste Skills auf einen Blick

Wegweiser nach Aufgabe und Phase. Die Skill-Dateien selbst bleiben die Quelle der Wahrheit; diese Liste sagt nur, wo Kollegen zuerst nachschauen sollen.

| Skill | Plugin | Phase | Wofür |
|---|---|---|---|
| `setup-orientation` | `wordpress-server-ops` | Server / Setup | Erstes Projekt-Setup, Wiederaufnahme eines unvollständigen Setups, `PROJECT-CONTEXT.md` und `.gitignore`-Allowlist für Handoffs konfigurieren. |
| `wp-media-import` | `wordpress-server-ops` | Server | Sicherer Import von Bildern und Dateien in die WordPress Media Library über `WP-CLI`. |
| `wst-section-workflow` | `wst-builder` | Server / WST | Neue Sections, Section-Remodels und serverseitige Section-Grundlage (FC-Layouts, ACF, Template, Hooks, Handoff). |
| `wst-new-post-type` | `wst-builder` | Server / WST | CPT-Grundlage: Custom Post Type, Taxonomien, ACF-CPT-Felder, WP Grid Builder Grid- und Card-Foundation, optionales Single-Template, CPT-Handoff. |
| `grill-me` | `wst-builder` | Vor der Implementierung | Plan oder Design vor Server-Implementierung stress-testen, bis das Vorgehen geklärt ist. |
| `frontend-section-qa` | `frontend-design-qa` | Lokal / Frontend | Lokale Section-CSS/SCSS-Implementierung und QA gegen das Section-Handoff oder ein Mini-Handoff für visual-only, mit `CSS-Injection-Proof` und `Source-Served-Verification`. |
| `cpt-frontend-qa` | `frontend-design-qa` | Lokal / Frontend | Lokale CSS/SCSS-Implementierung und QA für CPT-Card, Archive/Grid, WP Grid Builder Ausgabe und optional Single-Template, ebenfalls mit Mini-Handoff für visual-only und denselben Proof-/Verification-Stufen. |
| `setup-playwright-mcp` | `frontend-design-qa` | Lokal | Einmalige lokale Einrichtung von `Playwright MCP` im lokalen Cursor-Workspace; nie über Remote-SSH. |

## Server-Arbeit vs. lokale Frontend-Arbeit

Die Arbeit ist bewusst getrennt, aber bidirektional über Git verbunden. Auf dem Server entstehen Setup, WordPress-Struktur, WST-Grundlagen und der initiale Handoff. Lokal wird die sichtbare Frontend-Qualität implementiert, getestet und gegen die echte Server-Ausgabe verifiziert. Beide Workspaces teilen sich dasselbe Bitbucket-Repository; Handoffs und QA-Notizen reisen über `commit` / `push` / `pull` zwischen Server- und lokalem Workspace.

### Server-Workspace (Cursor + `Remote-SSH`)

- WordPress-Root öffnen und Setup mit `setup-orientation` führen.
- Git-Workflow: `git fetch`, Branch-Wechsel, Commits und Push gehen auf dem Server, weil WP Pusher dort Push-to-Deploy auslöst. Nach lokalen CSS-Pushes der Frontend-Phase pullt der Server, damit das CSS source-served ausgeliefert wird.
- WST-Foundation: neue Flexible Content Sections, Section-Remodels, Custom Post Types, Taxonomien, ACF-Felder, WST-Templates und CSS-Hooks (`wst-builder`). WST-Templates leben immer im Child Theme unter `wp-content/themes/<child-theme>/smart-template-builder/`; der Plugin-Ordner ist by default off-limits.
- Server-PHP, ACF-Konfiguration, WST-Templates, REST/ACF Flexible Content Updates (`wordpress-server-ops`).
- WordPress-Terminalbefehle (`WP-CLI`) und Cache leeren (`cache flush`).
- Media-Import in die WordPress Media Library (`wp-media-import`).
- Figma-Analyse für Sections und Post Types: Vor dem Erstellen einer Section oder eines CPTs muss der Agent die zugehörigen Figma-Designs sehen. So passen HTML-Struktur, ACF-Felder und WST-Layouts später besser zum Design. Die Cursor-Verbindung zu Figma (`Figma MCP`) ist deshalb auch im Server-Workspace aktiv (siehe `setup-orientation` Step 8).
- Handoff schreiben: Section- bzw. CPT-Handoff wird am projekt-konfigurierten Ablageort aus `PROJECT-CONTEXT.md` angelegt, committet und gepusht, damit der lokale Workspace ihn ziehen kann.

### Lokaler Frontend-Workspace (Cursor lokal, Browser + Playwright MCP)

- Einmalige Einrichtung von `Playwright MCP` über `frontend-design-qa` `setup-playwright-mcp` in der untracked lokalen `.cursor/mcp.json`. Diese Einrichtung passiert nur lokal, nie über `Remote-SSH`.
- Handoff ziehen: vor der Arbeit `git pull` (oder `git fetch` plus Branch-Check), damit der frische Handoff aus dem Server-Workspace verfügbar ist. Fehlt ein Handoff trotz Pull, wird je nach Aufgabe entweder zurück an `wst-builder` geroutet oder ein temporäres Mini-Handoff für visual-only Arbeit angelegt.
- Finale CSS/SCSS-Umsetzung gegen die Section-/CPT-Handoffs aus `wst-builder` (`frontend-design-qa`).
- Figma-zu-Code-Übersetzung: Farben/Tokens, Abstände, Typografie und Media-Verhalten in projekt-eigene CSS/SCSS-Dateien übertragen.
- Browser-QA über `Playwright MCP`: Navigation, Snapshot, Screenshot, Desktop/Tablet/Mobile-Viewports, Selektor- und Computed-Style-Checks gegen eine reale Dev-/Staging-URL.
- `CSS-Injection-Proof`: geplante CSS-Regeln werden zuerst in der echten Browser-Sitzung gegen die echte WordPress-Seite eingeschleust, um Spezifität und Wirkung zu bestätigen, bevor die Datei im Theme geschrieben wird.
- Nach dem Schreiben pusht der lokale Workspace CSS und aktualisierte Handoff-Notizen. Der Skill wartet auf User-Bestätigung, dass der Server gepullt/deployed hat (`server pull/deploy: user-confirmed`), bevor er die finale `Source-Served-Verification` gegen die wirklich ausgelieferten Styles fährt.
- Chrome Local Overrides nur als kurzlebige Spikes, nie als Ersatz für die source-served Verifikation.
- Responsive-Checks und optional projekt-eigene Playwright-Regression, wenn das Projekt einen echten Test-Harness mitbringt.
- QA-Notizen inkl. Statusfeldern (`frontend work mode`, `proof mode`, `injection proof`, `delivery path`, `server pull/deploy`, `source-served verification`, `final status`) und `Local Playwright MCP status` ins jeweilige Section- oder CPT-Handoff zurückschreiben, committen und pushen.

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

WST Sections, CPT-Cards, Archive/Grid-Integrationen und optionale Single-Templates leben **immer** im Child Theme unter `wp-content/themes/<child-theme>/smart-template-builder/`, nicht im Plugin-Verzeichnis.

`wp-content/plugins/weseo-smart-template-builder/` ist die WST-Runtime/-Library und standardmäßig tabu. Edits dort sind nur erlaubt, wenn `PROJECT-CONTEXT.md` für genau diesen Unterpfad eine projekt-eigene Quelle bestätigt. Andernfalls gilt das Plugin als Vendor-Plugin.

Alles andere (WordPress Core, andere Plugins, Uploads, Config) ist tabu. Falls eine Aufgabe Änderungen außerhalb erfordert, vorher Rücksprache mit dem Maintainer halten.

Details: `wordpress-server-ops` Rule `file-edit-boundary`.

## Praktischer Workflow

### 1. Aufgabe verstehen

Lies die Aufgabe und überlege, welche Phase und welches Plugin zuständig sind:

- Neue Section, bestehende Section umbauen, ACF-/Template-Änderung an einer Section → Remote, `wst-builder` (`wst-section-workflow`).
- Rein visuelle Section-Änderungen ohne Server-Touch → direkt lokal, `frontend-design-qa` (`frontend-section-qa`); ohne bestehenden Handoff wird ein Mini-Handoff angelegt.
- Neuer CPT, CPT-Remodel, Archiv-/WP-Grid-Builder-/Card-/Single-Template-Foundation → Remote, `wst-builder` (`wst-new-post-type`).
- Rein visuelle CPT-Änderungen (Card-, Archive-, Grid- oder Single-Styling) ohne Server-Touch → direkt lokal, `frontend-design-qa` (`cpt-frontend-qa`); ohne bestehenden Handoff wird auch hier ein Mini-Handoff angelegt.
- Bestehende Section visuell finalisieren → lokal, `frontend-design-qa` (`frontend-section-qa`).
- Bestehende CPT-Card, -Archive, -Grid oder -Single visuell finalisieren → lokal, `frontend-design-qa` (`cpt-frontend-qa`).
- Bilder in die Mediathek → Remote, `wordpress-server-ops` (`wp-media-import`).
- WP-CLI / Cache / REST-API-Pflege → Remote, `wordpress-server-ops`.

### 2. Figma vor dem Bauen anschauen

Auch im Server-Schritt: Bevor eine Section oder ein CPT erzeugt wird, soll der Agent die Figma-Designs öffnen und verstehen. So passen HTML-Struktur, ACF-Felder, WST-Layouts und stabile CSS-Hooks zum späteren Design. Die Cursor-Verbindung zu Figma (`Figma MCP`) ist dafür auch im Server-Workspace aktiv.

### 3. Handoffs als Live-Kontrakt über Git

Server-Phase und lokale Frontend-Phase sind über Handoffs gekoppelt. Ein Handoff ist kein einmaliges Startdokument, sondern ein fortlaufend gepflegter Live-Kontrakt zwischen `wst-builder` und `frontend-design-qa`.

- Git-tracked: Section- und CPT-Handoffs liegen in einer Allowlist-Position im deny-all `.gitignore` und reisen über `commit` / `push` / `pull` zwischen Remote-SSH-Server-Workspace und lokalem Frontend-Workspace. `setup-orientation` legt diese Allowlist-Einträge anhand der in `PROJECT-CONTEXT.md` dokumentierten Handoff-Ablageorte an.
- Vorlagen vs. ausgefüllte Handoffs: Die wiederverwendbaren Templates liegen im `wst-builder` Plugin (`plugins/wst-builder/handoffs/section-handoff.template.md`, `plugins/wst-builder/handoffs/cpt-handoff.template.md`). Die gefüllten Handoffs liegen am projekt-konfigurierten Ablageort aus `PROJECT-CONTEXT.md`, nie im Plugin-Paketordner.
- Section- und CPT-Handoffs haben getrennte Ablageorte, weil CPT-Arbeit zusätzlich Archiv, Taxonomie, WP Grid Builder, Cards, Carousel, Detailseite und optionale Single-Templates abdeckt.
- `wst-builder` produziert oder aktualisiert den Handoff am projekt-konfigurierten Ort, committet und pusht auf demselben Branch oder PR. Der Handoff nennt URL, Template-Datei (im Child Theme), ACF-Referenzen, CSS-Hooks, erwartetes Verhalten, Frontend-QA-Brief, QA-Hinweise und offene Punkte.
- `frontend-design-qa` pullt vor der Arbeit, liest den Handoff als Kontrakt, schreibt die Status-Felder (`frontend work mode`, `proof mode`, `injection proof`, `delivery path`, `server pull/deploy`, `source-served verification`, `final status`) fortlaufend fort, committet und pusht zurück.
- Mini-Handoff für visual-only ohne bestehenden Handoff: `frontend-section-qa` bzw. `cpt-frontend-qa` legt am gleichen projekt-konfigurierten Ablageort eine temporäre Datei wie `<slug>-visual-only-handoff.md` an, basierend auf demselben Template. Das Mini-Handoff dient als Arbeitsprotokoll und Übergabe, bis die Aufgabe abgeschlossen ist.
- Abschluss-Lifecycle: nach erfolgreicher Source-Served-Verification werden offene Punkte geschlossen, QA-Notizen committed/gepusht, und ein nicht mehr benötigtes Mini-Handoff oder erledigtes Handoff per `git rm <pfad> && git commit && git push` entfernt. Damit verschwindet es konsistent in beiden Workspaces.

### 4. Editieren - nur im erlaubten Bereich

- CSS/SCSS in den freigegebenen Theme-Pfaden, nie inline, nie `<style>`-Blöcke.
- Neue CSS-Dateien gemäß der projekt-eigenen Loader-/`styles.json`-Konvention registrieren.
- `functions.php` nie anfassen. Wenn `theme-functions.php` oder ein MU-Plugin nötig wirkt, muss der Agent den geplanten Eingriff vorher nennen und fragen, ob das ok ist.
- WST-Templates (Section-Templates, CPT-Cards, Archive-/Grid-Wrapper, optionale Single-Templates) leben immer im Child Theme unter `wp-content/themes/<child-theme>/smart-template-builder/`. Der Plugin-Ordner `wp-content/plugins/weseo-smart-template-builder/` ist by default off-limits und darf nur nach expliziter Maintainer-Freigabe in `PROJECT-CONTEXT.md` für genau diesen Unterpfad bearbeitet werden.

### 5. Testen, verifizieren und Cache-Themen richtig routen

Server-Seite: nach Template- oder DB-Änderungen den Cache flushen. Genauer Befehl steht in `PROJECT-CONTEXT.md`. WESEO-Default:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

Lokale Frontend-Phase verifiziert in zwei klar getrennten Stufen, weil WordPress mit Theme und WST on top zu viele Überschreibungen hat, um CSS blind zu schreiben:

1. `CSS-Injection-Proof`: Die geplanten Regeln werden in der echten Browser-Session über `Playwright MCP` gegen die echte Dev/Staging-URL injiziert. Damit prüft `frontend-design-qa` Selektor-Spezifität, Kaskade und visuelle Wirkung **vor** dem Schreiben in eine Theme-Datei. Bestanden → CSS wird in die freigegebene Theme-Datei geschrieben, committet und gepusht. Fail → Selektoren oder Werte korrigieren und nochmal injizieren.
2. `Source-Served-Verification`: Erst wenn der Server die neuen CSS-Dateien tatsächlich ausliefert, läuft die finale QA gegen die ausgelieferten Styles. Bis dahin gilt der Status `implementation-pass-pending-deploy`. Der Skill wartet aktiv auf User-Bestätigung, dass der Server gepullt bzw. WP Pusher deployed hat (`server pull/deploy: user-confirmed`), und prüft anschließend, dass die CSS-Datei mit dem erwarteten Inhalt vom Server geliefert wird, bevor er Viewports und Edge-Cases final abnimmt.

Wichtig:

- `frontend-design-qa` führt keinen Server- oder Cache-Eingriff selbst aus. Stale Markup, alte Server-Ausgabe oder Cache-Hinweise werden im Handoff notiert und an `wordpress-server-ops` bzw. den dokumentierten Server-Schritt zurückgeroutet.
- Browser-Access-Blocker (Login-Wall, Cookie-Banner, IP-Allowlist, Basic-Auth, Self-Signed-Cert) sind ein harter Safety-Stop. Der Skill fragt nach Zugangsdaten oder einer offenen Session und schreibt keine Credentials in Handoff, Mini-Handoff, tracked files, Diagnosen oder Screenshots.
- Chrome Local Overrides nur als kurzlebiger Spike. Sie ersetzen weder den Injection-Proof noch die Source-Served-Verification.

### 6. Git-Workflow

WP Pusher arbeitet auf dem Server mit Push-to-Deploy. Handoffs reisen bidirektional über Git: Server pusht den Handoff, lokal wird gepullt; lokal werden CSS und QA-Status committet und gepusht, der Server pullt vor dem Cache-Flush bzw. das Deploy übernimmt diese Aufgabe automatisch.

Verwende keine blinden `git pull origin master`-Schritte; stattdessen:

```sh
git fetch origin
git status --short
git add <approved-paths>
git commit -m "<KEYWORD> - <Beschreibung>"
git push origin "$(git branch --show-current)"
```

Commit-Keywords: `FEATURE`, `FIX`, `DEV`. Code und Commits auf Englisch.

Niemals uncommittete Änderungen auf dem Server liegen lassen. Erledigte Mini-Handoffs oder abgeschlossene Handoffs werden per `git rm <pfad>` plus Commit und Push entfernt, damit sie konsistent in beiden Workspaces verschwinden.

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
- Section- und CPT-Handoffs sind Git-tracked, liegen am projekt-konfigurierten Ablageort aus `PROJECT-CONTEXT.md` und werden über `commit` / `push` / `pull` zwischen Server- und lokalem Workspace synchronisiert; keine Handoffs in Plugin-Paketordner schreiben.
- Visual-only Section- oder CPT-Arbeit ohne bestehenden Handoff → temporäres Mini-Handoff am selben projekt-konfigurierten Ablageort anlegen und nach Abschluss per `git rm` entfernen.
- Drei Plugins, drei Phasen: `wordpress-server-ops` (Setup/Server), `wst-builder` (Server-WST-Workflow mit Safety-Stops), `frontend-design-qa` (lokales Frontend mit Injection-Proof und Source-Served-Verifikation).
- Figma sowohl Remote (für Section/CPT-Vorbereitung) als auch lokal (für finale Umsetzung) nutzen.
- Editieren nur im freigegebenen Theme-Bereich. WST-Templates leben immer im Child Theme unter `wp-content/themes/<child-theme>/smart-template-builder/`; der Plugin-Ordner `wp-content/plugins/weseo-smart-template-builder/` ist by default off-limits.
- Nie `functions.php` ändern. `theme-functions.php` oder MU-Plugin nur nach vorheriger Ankündigung und Bestätigung.
- Nie inline Styles → CSS-Klassen in den freigegebenen Style-Pfaden.
- Neue CSS-Dateien gemäß projekt-eigener Style-Loader-Konvention registrieren.
- CSS-Regeln zuerst per `CSS-Injection-Proof` gegen die echte Dev/Staging-Seite testen, dann in eine Theme-Datei schreiben.
- Finale Frontend-QA gilt erst als abgeschlossen, wenn die `Source-Served-Verification` gegen die wirklich ausgelieferten Styles bestanden ist. Bis dahin auf User-Bestätigung warten, dass der Server gepullt bzw. deployed hat.
- Browser-Access-Blocker (Login, Cookie-Wall, IP-Allowlist, Basic-Auth, Self-Signed-Cert) sind ein harter Stop. Zugangsdaten nicht in Handoff, Mini-Handoff, tracked files oder Diagnosen schreiben.
- Git: `git fetch` statt blindem `pull origin master`; Branch-Wechsel nur nach Bestätigung. Lokale Pushes brauchen einen Server-Pull bzw. Deploy, bevor sie source-served verifiziert werden.
- WP Pusher = Push-to-Deploy → immer committen und pushen, sowohl Server-Artefakte als auch CSS und Handoff-Updates.
- Stoppen und fragen, wenn Handoff-Ablage, Template-Pfad, ACF/WST-Referenzen, Cache-Zuständigkeit, Browser-Zugriff oder erlaubter Editierbereich unklar sind.
- Code und Commits auf Englisch, Commit-Keywords `FEATURE`, `FIX`, `DEV`.
- Keine temp/backup/sensiblen Dateien im öffentlichen Webroot - Safe-Temp-Path aus `PROJECT-CONTEXT.md` verwenden.
- Echte Tokens, Application Passwords, SSH Keys und tokenhaltige URLs nie in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen, Screenshots oder öffentliche Webroot-Artefakte schreiben.
- Learnings in `LEARNINGS.md`, projektspezifische Fakten in `PROJECT-CONTEXT.md`, allgemeingültige Standards in Plugin-Rules.
