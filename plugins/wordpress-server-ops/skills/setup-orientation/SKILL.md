---
name: setup-orientation
description: Guided wizard for the complete first setup of a WESEO WordPress/WST project over Cursor Remote-SSH. Use when starting a new project, re-orienting an existing SSH workspace, opening the WordPress root, creating or updating PROJECT-CONTEXT.md, recording Section/CPT handoff storage and LEARNINGS.md status, configuring this project's Bitbucket Git access via hidden terminal prompt, verifying WP-CLI and cache flush, preparing the .cursor skeleton, and configuring untracked .cursor/mcp.json access for WordPress and Figma.
---

# Setup Orientation

Run this Skill as a guided wizard for the first run of a WESEO WordPress/WST project opened through Cursor Remote-SSH. The expected outcome is a usable SSH development workspace with `PROJECT-CONTEXT.md` filled, project-configured Section and CPT handoff storage recorded, `LEARNINGS.md` status recorded, working project Git, verified WP-CLI/cache, a tracked `.cursor` skeleton, and untracked `.cursor/mcp.json` access for WordPress and Figma.

The wizard must work from any starting state. If Cursor is open without a Remote-SSH connection, lead the user through Remote-SSH first. If the WordPress root is already open and Git is already configured, skip those steps and continue.

The target user is a non-backend-heavy frontend or design colleague. They can follow WordPress and Cursor instructions, but the wizard must not assume they understand backend setup vocabulary.

Communicate with the user in German throughout the wizard. Keep commands, file names, placeholders, and external UI labels in their original language, but introduce them through plain-language purpose and action first.

Never ask the user to paste real tokens, application passwords, SSH keys, token-bearing URLs, private server coordinates, or credentials into chat, tracked files, diagnostics, screenshots, or commit messages.

## Guided Wizard Contract

For every user-facing setup step, lead with a short plain-language frame before technical details:

- **Was passiert:** What the wizard is checking or changing, in everyday German.
- **Warum:** Why this matters for the project or later handoff.
- **Du musst:** The exact user action, or `Nichts tun` when the wizard can continue automatically.

Use equivalent short wording when the three labels would feel too heavy, but keep the same order: purpose, reason, exact user responsibility.
End each step with a one-line progress note: `Erledigt: <confirmed result>` or `Offen: <open setup point> - nächster Schritt: <action>`. Missing safe-to-continue values should be described as open setup points with a concrete next action, not as alarming failures.

For every setup step:

1. State what was detected in plain German and why the next step matters.
2. Follow the prescribed safe path automatically when the action is read-only, reversible, or a routine setup write covered by this Skill.
3. Ask only for a concrete missing input, a choice between ambiguous options, secret entry in the correct non-chat location, or a short confirmation before sensitive, destructive, credential, or live-site-affecting actions.
4. Execute the chosen safe action.
5. Verify the result.
6. Update `PROJECT-CONTEXT.md` or untracked setup state.
7. Continue to the next step.

Run safe, reversible, or verifying steps automatically (`pwd`, root checks, `mkdir -p`, `git fetch origin`, writing `PROJECT-CONTEXT.md`, writing `.gitignore` and `.cursor` skeleton, MCP skeleton). Explain when the wizard is only reading information. Do not ask broad questions like "Is this okay?" for checks the user cannot reasonably evaluate. Ask for short confirmation before: changing or adding a Git remote, the initial commit and push, executing `cache flush` against the live site, or writing real Application Passwords or Figma tokens into the untracked `.cursor/mcp.json` file in the opened Cursor workspace.

When secrets or access values are involved, name concrete storage and non-storage locations. Say whether a value belongs in `.cursor/mcp.json`, this project's Git configuration, the hidden terminal prompt, a browser UI, or a password manager. Also say when it must not go into chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, or screenshots. Avoid ambiguous "local" wording in Remote-SSH credential contexts because users may confuse their own computer with the opened server workspace.

When the wizard is invoked after an interruption (long pause, terminal action, chat reset, or partial run), do not claim setup is complete based on a vague memory. Re-read `PROJECT-CONTEXT.md`, find the first step whose status is missing, `pending`, or unverified, resume from there, and finish with the mandatory frontend onboarding handoff (Step 11). Setup is only complete when every gate from Step 1 to Step 11 has a recorded outcome in `PROJECT-CONTEXT.md`.

The detailed step-by-step walkthroughs, prompt templates, terminal flows, `.gitignore` baseline, MCP setup guides, completion gates, redaction rules, and the old-guide coverage mapping live in [reference.md](reference.md).

## Jargon Watchlist

Technical names may remain visible when they are command names, file names, or UI labels, but user-facing explanations should introduce them with these plain-language anchors:

- `Remote-SSH`: Cursor is connected to the server.
- `WordPress root`: the main folder of the WordPress installation.
- `PROJECT-CONTEXT.md`: the project note file for non-secret facts.
- `WP-CLI`: WordPress commands in the terminal.
- `cache flush`: clear the cache / Zwischenspeicher leeren.
- `Git remote/origin`: the connection to the Bitbucket repository.
- `MCP`: Cursor connection to WordPress or Figma; keep real UI labels such as `Settings` -> `Tools & MCP`.
- `Application Password`: an access key created in the WordPress user profile and entered only in the specific UI, hidden terminal prompt, or `.cursor/mcp.json` flow.
- `Access Token`: an access key created in Bitbucket or Figma and entered only in the specific UI, hidden terminal prompt, or `.cursor/mcp.json` flow.
- `tracked`: saved with Git and therefore able to end up in commits.
- `untracked`: not saved with Git unless someone explicitly adds it.

## WESEO SSH Defaults

Use these defaults unless Project Context or the maintainer says otherwise:

- Cursor connects through Remote-SSH and opens the WordPress installation directly.
- The WordPress root contains `wp-content/`, `wp-admin/`, and `wp-includes/`.
- The editable theme is usually `wp-content/themes/astra-child/`.
- The WST stack is Astra Child Theme, WST plugin (`weseo-smart-template-builder`), ACF PRO, ACF Extended, WP Grid Builder, CPT UI.
- A local `wp-cli.phar` in the WordPress root is preferred when global `wp` is not available.
- Default cache flush command: `php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"`.
- Bitbucket remotes use HTTPS with `x-token-auth`. The real token never leaves this project's Git configuration.
- `.cursor/mcp.json` exists only as an untracked file in the opened Cursor workspace. `.cursor/rules/.gitkeep` and `.cursor/skills/.gitkeep` are tracked.

## Step 1: Cursor mit dem Server verbinden und den WordPress-Hauptordner öffnen

**Was passiert:** Der Wizard prüft, ob Cursor über `Remote-SSH` mit dem richtigen Server verbunden ist und ob der geöffnete Ordner der Hauptordner der WordPress-Installation ist.
**Warum:** Nur im richtigen Ordner können spätere Checks, Git, WP-CLI und Projektdateien sicher arbeiten.
**Du musst:** Wenn Cursor noch nicht verbunden ist oder der falsche Ordner offen ist, folgst du der angezeigten `Remote-SSH: Connect to Host`- und `Open Folder`-Anleitung. Wenn alles passt, musst du nichts tun.

If Cursor has no Remote-SSH connection or the open folder is not a WordPress root, guide the user through `Remote-SSH: Connect to Host` and `Open Folder` using the Remote-SSH walkthrough in [reference.md](reference.md).

Background verification commands:

```sh
pwd
ls -la
test -d wp-content && test -d wp-admin && test -d wp-includes
```

If the opened folder is not a WordPress root, look for `wordpress-*` candidates in the current folder and one parent level. Ask before switching if more than one candidate exists. Do not scan unrelated account data.

## Step 2: Projektinformationen ohne Geheimnisse sammeln

**Was passiert:** Der Wizard liest harmlose technische Eckdaten wie Ordner, Hostname, PHP-Version, vorhandene Projektdateien und Git-Status.
**Warum:** Diese Infos füllen die Projektnotiz und verhindern spätere Rückfragen. Der Wizard ändert dabei nichts an der Website.
**Du musst:** Nichts tun. Der Wizard fragt erst nach, wenn eine nicht geheime Information fehlt, die er nicht selbst sicher erkennen kann.

Collect non-secret facts before asking questions. These are read-only checks and must be introduced as background verification, not as the first user-facing content:

```sh
pwd
hostname
php -v
test -f wp-config.php && echo "wp-config.php present"
test -d wp-content/themes/astra-child && echo "astra-child present"
test -d wp-content/plugins/weseo-smart-template-builder && echo "wst present"
test -f wp-cli.phar && echo "local wp-cli.phar present"
command -v wp || true
git rev-parse --show-toplevel 2>/dev/null || true
git branch --show-current 2>/dev/null || true
git status --short 2>/dev/null || true
git config user.name
git config user.email
```

When WP-CLI is available, also verify the WST stack:

```sh
php wp-cli.phar theme list --status=active --field=name 2>/dev/null || wp theme list --status=active --field=name 2>/dev/null
php wp-cli.phar plugin list --status=active --field=name 2>/dev/null || wp plugin list --status=active --field=name 2>/dev/null
```

If WP-CLI is not yet available, fall back to filesystem checks for `astra-child`, `weseo-smart-template-builder`, ACF, ACF Extended, WP Grid Builder, and CPT UI. Record the result and any missing components in `PROJECT-CONTEXT.md` as an open WST stack question.

Do not print `git remote -v` directly in chat or notes. To inspect the remote, use `git remote get-url origin` locally and report only a redacted shape.

## Step 3: Projektnotiz für spätere Arbeit vorbereiten

**Was passiert:** Der Wizard erstellt oder ergänzt `PROJECT-CONTEXT.md`, die Projektnotiz für nicht geheime Fakten.
**Warum:** Spätere WordPress-, WST- und Frontend-Aufgaben lesen diese Datei zuerst, damit alle mit denselben sicheren Projektinfos arbeiten.
**Du musst:** Nichts tun, solange die Werte erkannt werden können. Wenn etwas fehlt, gib nur die konkrete nicht geheime Information an oder entscheide, ob sie als offener Punkt mit nächstem Schritt notiert wird.

`PROJECT-CONTEXT.md` is the project's non-secret context contract. Later WordPress, WST, and Frontend Skills must read it first to understand the project and update it when new non-secret facts are confirmed.

If `PROJECT-CONTEXT.md` is missing in the WordPress root, create it. If it exists, update only missing or stale non-secret values.

At minimum, fill:

- Project name, live URL, and staging/dev URL.
- Server hostname and WordPress root.
- Theme path and WST template path.
- Project-configured Section handoff storage location.
- Project-configured CPT handoff storage location.
- `LEARNINGS.md` status (`exists`, `create when first learning appears`, or `pending: <reason>`).
- Repository host/name and default/current branch.
- Repository access method as a non-secret description (`token-in-remote-url`, `credential-helper`, or `ssh`).
- WP-CLI command shape.
- Cache flush command shape.
- Approved temp path outside the public webroot.
- Editable path policy for the project.
- WST stack status (theme + plugins) or open question.
- Setup completion status per step (`done`, `pending: <reason>`, `skipped: <reason>`).

Never store real tokens, application passwords, SSH private keys, token-bearing URLs, REST credentials, dumps, or media inventories.

For handoff storage, record project-configured locations rather than inventing a package path. Section handoff drafts should use WST Builder's bundled template at `plugins/wst-builder/handoffs/section-handoff.template.md` as the reusable contract and then be stored at the concrete project location recorded in `PROJECT-CONTEXT.md`. CPT handoffs should use WST Builder's bundled template at `plugins/wst-builder/handoffs/cpt-handoff.template.md` as the reusable contract and then be stored separately at the project-configured CPT handoff location recorded in `PROJECT-CONTEXT.md`.

Handoffs travel between the Remote-SSH WordPress workspace (server phase) and the local frontend workspace (frontend phase) through Git. Both workspaces clone the same Bitbucket repository, and the deny-all `.gitignore` baseline blocks any folder that is not on the allowlist. Once a project handoff storage location is recorded in this step, the wizard adds it to the `.gitignore` allowlist in Step 5 so the handoff files are trackable in both workspaces.

For `LEARNINGS.md`, check whether the file exists in the WordPress root. If it exists, record `learnings: exists`. If it does not, record that it should be created when the first real project learning appears. Missing learnings are not a setup failure.

If required non-secret values are missing, follow the Project Context fill walkthrough in [reference.md](reference.md). The old SmartFlow placeholder categories (CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, button variants, container widths, clamp values, ACF IDs) are collected here, not by editing plugin Rules.

## Step 4: Das Projekt mit Bitbucket verbinden

**Was passiert:** Der Wizard prüft, ob dieses Projekt schon mit dem Bitbucket-Repository verbunden ist. Falls nicht, führt er dich durch einen Zugangsschlüssel, den du in Bitbucket für genau dieses Repository erstellst (`Repository Access Token`).
**Warum:** Git sorgt dafür, dass Projektänderungen nachvollziehbar gespeichert und mit dem Team geteilt werden können.
**Du musst:** Den Repository-Namen auswählen oder bestätigen. Danach erstellst du den Zugangsschlüssel in Bitbucket, speicherst ihn im Passwortmanager oder OS-Keychain und gibst ihn nur in den verdeckten Terminal-Prompt ein. Der echte Wert darf nicht in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots landen.

If the WordPress root already has a working Git repository with `git fetch origin` succeeding, only verify identity and continue.

If no Git repository is present, follow this prescribed Bitbucket flow in order. Steps 1 and 2 are mandatory and must not be skipped or shortened. The wizard must never open a token terminal prompt before Step 2 has been displayed in chat and the user has confirmed they have a token.

1. Confirm the Bitbucket repository name or URL with the user. If `PROJECT-CONTEXT.md` already names the repo, only ask the user to confirm that concrete repo.
2. **Display the full Bitbucket Access Token creation guide in chat** (German, see template below) so the user knows exactly where the token comes from. Wait for explicit confirmation that the user has the token in their password manager or OS keychain before continuing.
3. Open a terminal in Cursor and explain how to use it (`Ctrl+Ö` or `Ctrl+Backtick`, integrated terminal already runs in the WordPress root via Remote-SSH). Then run the hidden token prompt to set up `origin` with `x-token-auth`. The user pastes the token only into that terminal.
4. Verify access with `git fetch origin`. Do not run `git pull origin master` blindly.
5. Configure repo-local `git config user.name` and `git config user.email` if missing.

Token creation guide that the wizard displays in chat before any terminal action:

```md
Bevor wir Git mit Bitbucket verbinden, brauchst du einen Zugangsschlüssel für genau dieses Bitbucket-Repository. Bitbucket nennt diesen Schlüssel `Repository Access Token`. So bekommst du ihn:

1. Öffne `https://bitbucket.org/` im Browser und logge dich ein.
2. Wähle den WESEO-/Projekt-Workspace, falls Bitbucket dich danach fragt.
3. Öffne `Repositories` und wähle das Ziel-Repository (`<repo-name>` aus PROJECT-CONTEXT.md).
4. Öffne links im Repository `Repository settings`.
5. Öffne dort `Access tokens`.
6. Klicke `Create` bzw. `Create repository access token`.
7. Vergib einen klaren Namen, z. B. `cursor-remote-ssh-<project>`.
8. Scrolle im Token-Dialog zu `Permissions` bzw. `Repository permissions`.
9. Hake bei den Repository-Berechtigungen `Read` und `Write` an. Vergib keine Admin-Rechte, außer der Maintainer verlangt sie ausdrücklich.
10. Klicke `Create` und kopiere den Token sofort - Bitbucket zeigt ihn nur ein einziges Mal.
11. Speichere den Token im Passwortmanager oder OS-Keychain.

Hinweis: Der Wizard trägt den Schlüssel nachher über eine verdeckte Terminal-Eingabe in die Git-Verbindung dieses Projekts ein. Technisch verwendet Git dabei HTTPS mit `x-token-auth` und speichert den Wert nur in der Git-Konfiguration dieses Projekts.

Wichtig: Bitte poste den Token nicht in den Chat und schreibe ihn nicht in `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots. Sobald du ihn hast, öffne ich gleich ein Terminal in Cursor; dort gibst du ihn bei einer verdeckten Eingabe ein.

Sag mir Bescheid, sobald du den Token im Passwortmanager oder OS-Keychain gespeichert hast.
```

Never request the real token in chat. Tracked docs only show the placeholder shape:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

The wizard must clearly explain how to open Cursor's integrated terminal (`Ctrl+Ö` or `Ctrl+Backtick`) and what to type when the hidden prompt appears, and provide a copy-paste fallback if the integrated terminal is unavailable. Detailed terminal guidance and the full Bitbucket walkthrough live in [reference.md](reference.md).

## Step 5: Git vor falschen Dateien schützen und Cursor-Projektordner anlegen

**Was passiert:** Der Wizard richtet eine vorsichtige `.gitignore` ein und legt die leeren `.cursor/rules`- und `.cursor/skills`-Ordner für spätere Projekthinweise an.
**Warum:** WordPress-Core, Uploads, Caches, Dumps, Zugangsdaten und `.cursor/mcp.json` dürfen nicht versehentlich in Git oder Commits landen.
**Du musst:** Nichts tun, bis der Wizard vor dem ersten Commit den sicheren Umfang zeigt. Bestätige den initialen Commit oder Push erst nach dieser Prüfung.

Before any initial `git add`, commit, or push, install or update a deny-all WordPress-root `.gitignore`. The full baseline is in [reference.md](reference.md).

The default allowlist covers:

- Setup files (`.gitignore`, `PROJECT-CONTEXT.md`, `README.md`).
- Detected child themes (`astra-child`, `betheme-child`, `smarttheme-child`).
- The `.cursor` skeleton: `.cursor/rules/.gitkeep` and `.cursor/skills/.gitkeep` plus any future project-specific Rules and Skills.
- The project-configured Section and CPT handoff storage paths from Step 3, so handoffs flow between Remote-SSH and local workspace through Git.
- `.cursor/mcp.json` stays ignored.

Project-owned plugins (including `weseo-smart-template-builder` when project policy makes it editable) are added to the allowlist only after explicit confirmation in `PROJECT-CONTEXT.md` or by the maintainer.

When Step 3 records concrete handoff storage paths, write the matching unignore entries into `.gitignore` here. Each handoff folder needs both the folder line and the recursive `**` line, and a shared parent folder (for example `handoffs/`) is unignored once so the children are reachable. The detailed allowlist shape and verification commands live in [reference.md](reference.md). If a handoff storage location is still `pending` in `PROJECT-CONTEXT.md`, record the matching `.gitignore` entry as `pending: <reason>` with the next action and add it as soon as the path is known.

Create the `.cursor` skeleton if missing:

```sh
mkdir -p .cursor/rules .cursor/skills
test -f .cursor/rules/.gitkeep || : > .cursor/rules/.gitkeep
test -f .cursor/skills/.gitkeep || : > .cursor/skills/.gitkeep
```

After writing `.gitignore`, verify staging scope before the first commit:

```sh
git status --short
```

Stop and fix `.gitignore` if WordPress core, uploads, caches, vendor plugins, dumps, media, token-bearing config, or `.cursor/mcp.json` appear. Continue with the initial commit and push only after confirming the staging scope and a short user confirmation.

## Step 6: WordPress-Terminalbefehle prüfen und Cache leeren

**Was passiert:** Der Wizard prüft, ob WordPress-Befehle im Terminal über `WP-CLI` funktionieren, und leert danach mit deiner kurzen Bestätigung den Cache.
**Warum:** Viele spätere Wartungs- und Prüfschritte brauchen zuverlässige WordPress-Terminalbefehle. Das Cache-Leeren stellt sicher, dass die Seite nicht mit alten Zwischenspeicher-Daten weiterläuft.
**Du musst:** Bei reinen Prüfungen nichts tun. Vor dem Cache-Leeren bestätigst du kurz, weil das die Live-Seite beeinflussen kann.

Confirm or install WP-CLI:

- Use `php wp-cli.phar <command>` when `wp-cli.phar` exists in the WordPress root.
- Otherwise use `wp <command>` if `command -v wp` succeeds.
- If neither is available, ask whether to install local `wp-cli.phar`, use a maintainer-provided global `wp`, or consciously skip with reason.

Background verification without changing site state:

```sh
php wp-cli.phar --info && php wp-cli.phar core version
```

After WP-CLI is verified and the WordPress root is confirmed, run the cache flush as part of setup with a short confirmation:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

Record the chosen command shape and the cache flush command in `PROJECT-CONTEXT.md`.

## Step 7: Cursor-Hinweise für dieses Projekt prüfen

**Was passiert:** Der Wizard prüft, ob die WESEO-Cursor-Plugins verfügbar sind und ob dieses Projekt eigene `.cursor`-Hinweise enthält.
**Warum:** So wissen spätere Agents, welche Regeln und Skills sie für WordPress-, WST- und Frontend-Arbeit verwenden sollen.
**Du musst:** Nichts tun. Der Wizard liest nur die vorhandene `.cursor`-Struktur und dokumentiert offene Punkte, falls Plugin-Hinweise im Remote-SSH-Kontext fehlen.

The user runs Cursor with the WESEO plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) installed in their personal Cursor account. The wizard does not copy plugin Rules or Skills into the project.

Verify the co-installed workflow skills that later phases require:

- `setup-orientation` and `wp-media-import` from `wordpress-server-ops`.
- `grill-me`, `wst-section-workflow`, and `wst-new-post-type` from `wst-builder`.
- `frontend-section-qa` and `cpt-frontend-qa` from `frontend-design-qa`.

The project repository keeps a `.cursor` skeleton (Step 5) so the team can add project-specific Rules and Skills later.

Background verification commands:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

Record in `PROJECT-CONTEXT.md` whether the personal plugin guidance is active for this Remote-SSH workspace, whether each required workflow skill above is available, and whether project-specific Rules or Skills exist. If plugin guidance is not available in the SSH context, follow the Cursor Guidance fallback walkthrough in [reference.md](reference.md).

## Step 8: Cursor mit WordPress und Figma verbinden

**Was passiert:** Der Wizard richtet die Cursor-Verbindungen zu WordPress und Figma ein und prüft sie danach unter `Settings` -> `Tools & MCP`.
**Warum:** Das Team nutzt diese Verbindungen, damit Agents später WordPress- und Figma-Kontext sicher abrufen können.
**Du musst:** In WordPress ein `Application Password` und in Figma einen `Personal Access Token` erstellen. Die echten Werte trägst du nur im vorgesehenen verdeckten Terminal-Prompt oder in `.cursor/mcp.json` im gerade geöffneten Cursor-Workspace ein; diese Datei bleibt untracked. Nicht in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots einfügen.

WordPress MCP and Figma MCP are required setup gates because the team uses both. Real values stay only in the untracked `.cursor/mcp.json` file in the opened Cursor workspace. Tracked docs only show placeholder shapes.

The wizard must:

1. Walk the user through creating a WordPress `Application Password` under `Benutzer` -> `Profil` -> `Application Passwords`. Explain that this value is for Cursor's WordPress connection only and must not be pasted into chat or `PROJECT-CONTEXT.md`.
2. Walk the user through creating a Figma `Personal Access Token` under `Profile` -> `Settings` -> `Personal access tokens`. Explain that this value is for Cursor's Figma connection only and must not be pasted into chat or `PROJECT-CONTEXT.md`.
3. Open a hidden-input terminal flow (or edit the untracked `.cursor/mcp.json` file in the opened Cursor workspace) so the real values land only there. Name this storage location explicitly before asking the user to continue. Detailed steps live in [reference.md](reference.md).
4. Restart Cursor and verify both servers under `Settings` -> `Tools & MCP`.

If the user cannot create one of the credentials right now, record the gate as `pending: <reason>` with the next concrete action in `PROJECT-CONTEXT.md`. Do not call setup complete while either MCP gate is unresolved.

The placeholder shape used in tracked examples:

```json
{
  "mcpServers": {
    "wordpress": {
      "command": "npx",
      "args": [
        "-y",
        "<wordpress-mcp-package>",
        "--url=https://<domain>",
        "--username=<user>",
        "--password=<app-password>"
      ]
    },
    "figma": {
      "command": "npx",
      "args": ["-y", "<figma-mcp-package>", "--stdio"],
      "env": {
        "FIGMA_API_KEY": "<figma-api-key>"
      }
    }
  }
}
```

## Step 9: Sicheren Ablageort für temporäre Dateien festlegen

**Was passiert:** Der Wizard legt einen temporären Arbeitsordner außerhalb des öffentlich erreichbaren WordPress-Bereichs fest.
**Warum:** Exporte, Dumps, Testskripte und andere Arbeitsdateien dürfen nicht im Webroot liegen und nicht aus Versehen veröffentlicht werden.
**Du musst:** Nichts tun, wenn ein sicherer Pfad eindeutig ist. Wenn mehrere Pfade möglich sind oder der Pfad öffentlich erreichbar sein könnte, fragt der Wizard gezielt nach.

Choose an approved temp path outside the public webroot. For WordPress roots inside a `public_html` tree, prefer `$HOME/.weseo-tmp`:

```sh
mkdir -p "$HOME/.weseo-tmp"
```

Database dumps, exports, temporary PHP scripts, and setup scratch files must live under that path, stay untracked, and be removed after use. Record the final path in `PROJECT-CONTEXT.md`.

## Step 10: Setup-Ergebnis gemeinsam prüfen

**Was passiert:** Der Wizard geht alle Setup-Punkte noch einmal durch und prüft, ob jeder Pflichtpunkt erledigt oder mit nächstem Schritt als offen dokumentiert ist.
**Warum:** Das Setup darf erst als fertig gelten, wenn die sichere Arbeitsbasis wirklich nachvollziehbar ist.
**Du musst:** Nur bei offenen Pflichtpunkten entscheiden, ob der Wizard sie jetzt beheben, bewusst mit Grund und nächstem Schritt notieren oder stoppen soll.

Use the final verification walkthrough in [reference.md](reference.md). Confirm at minimum:

- Cursor is connected to the expected Remote-SSH workspace and the open folder is the WordPress root.
- `PROJECT-CONTEXT.md` exists and contains the detected non-secret setup coordinates with no secrets.
- Section handoff storage and CPT handoff storage are recorded or marked `pending` with a next action, and their `.gitignore` allowlist entries are in place so handoffs can flow between Remote-SSH and local workspace through Git.
- `LEARNINGS.md` status is recorded without treating a missing file as a failure.
- Git is working through the Bitbucket remote with `git fetch origin` succeeding, identity is set, and the deny-all `.gitignore` produced a clean staging scope before the initial push.
- WST stack status is recorded.
- WP-CLI is verified and the cache flush command was executed during setup.
- The `.cursor` skeleton with `.gitkeep` files is in place; `.cursor/mcp.json` is untracked.
- Co-installed plugin guidance is verified for `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, and the workflow skills `grill-me`, `frontend-section-qa`, and `cpt-frontend-qa`.
- WordPress MCP and Figma MCP are active or recorded as `pending: <reason>` with next action.
- Safe temp path exists outside the public webroot.
- No real tokens, application passwords, SSH keys, or token-bearing URLs were written to chat, tracked docs, or commits.
- The frontend onboarding handoff (Step 11) has been performed and the decision is recorded in `PROJECT-CONTEXT.md`.

If any required gate is not satisfied, ask the user whether to fix it now, consciously skip with reason and next action, or stop. Do not claim setup complete while required gates are unresolved.

## Step 11: Frontend-Onboarding anzeigen oder bewusst zurückstellen

**Was passiert:** Der Wizard fragt zum Schluss, ob du das kurze Frontend-Onboarding jetzt lesen willst, es schon kennst oder später lesen möchtest.
**Warum:** Danach ist klar dokumentiert, ob die nächste Person den Remote-Server-vs.-Frontend-Workflow kennt.
**Du musst:** Eine der drei Antworten geben. Erst danach darf der Wizard das Setup als abgeschlossen melden.

This step is mandatory and must always run as the very last action of the wizard, even after long pauses, terminal interruptions, or chat resets. The wizard is not finished until `PROJECT-CONTEXT.md` records a `frontend_onboarding` decision.

Before declaring setup complete, the wizard must:

1. Check `PROJECT-CONTEXT.md` for a `frontend_onboarding` field. If it is missing or unset, this step still has to run.
2. Display the German handoff question shown below in chat. Do not paraphrase it; do not skip it; do not collapse it into the final summary.
3. Wait for an explicit user answer before saying setup is complete.
4. Record the answer in `PROJECT-CONTEXT.md` as `frontend_onboarding: read` or `frontend_onboarding: skipped (<reason>)`.
5. If the user chose to read it, open or display [frontend-onboarding.md](frontend-onboarding.md) inline so the user actually sees the content.

Mandatory German handoff prompt:

```md
Setup ist durch. Letzter Schritt: das Frontend-Onboarding.

`frontend-onboarding.md` erklärt kompakt:
- die drei WESEO-Plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`),
- was das Setup vorbereitet und wo `PROJECT-CONTEXT.md`, Section-/CPT-Handoffs und `LEARNINGS.md` stehen,
- was `wst-builder` vor der lokalen Frontend-Arbeit erzeugt,
- was `frontend-design-qa` lokal übernimmt,
- wann Cache-/Server-Themen zurück an `wordpress-server-ops` gehen,
- die wichtigsten Stop-Conditions.

Soll ich es dir jetzt zeigen, oder kennst du den Workflow schon?
- "Zeig es mir": ich öffne `frontend-onboarding.md` direkt im Chat.
- "Kenne ich schon, überspringen": ich dokumentiere `frontend_onboarding: skipped (already familiar)` in `PROJECT-CONTEXT.md`.
- "Später": ich dokumentiere `frontend_onboarding: pending` mit nächstem Schritt.
```

Only after this answer is recorded may the wizard say the setup is complete.

## Outputs

When the wizard finishes, leave behind:

- A usable Remote-SSH Cursor workspace at the WordPress root.
- `PROJECT-CONTEXT.md` filled with non-secret facts, recorded gates, and any `pending`/`skipped` notes with reason and next action.
- Project-configured Section and CPT handoff storage recorded in `PROJECT-CONTEXT.md`, with matching `.gitignore` allowlist entries so handoffs travel through Git between Remote-SSH and local workspace.
- `LEARNINGS.md` status recorded in `PROJECT-CONTEXT.md` without requiring the file to exist on day one.
- Working project Git through the approved Bitbucket access method.
- Verified WP-CLI and a documented, executed cache flush command.
- Tracked `.cursor` skeleton (`.gitkeep` files) and untracked `.cursor/mcp.json`.
- Untracked `.cursor/mcp.json` config for WordPress and Figma, or recorded `pending: <reason>` for either.
- Safe temp/scratch path outside the public webroot.
- A recorded `frontend_onboarding` decision in `PROJECT-CONTEXT.md` (`read`, `skipped (<reason>)`, or `pending`).

## Final Setup Summary

After the frontend onboarding decision is recorded, end with a short non-technical German overview first: what the project is now ready for, what the user can safely do next, and which setup points remain open if any. Put technical completion details after that overview, such as verified Git access, WP-CLI/cache status, MCP status, safe temp path, and recorded `PROJECT-CONTEXT.md` gates.

## Stop Conditions

Stop and ask before:

- Creating a new Git repository or replacing an existing remote.
- Performing the initial commit or push.
- Running cache flush or any command that changes live site state.
- Storing or displaying a credential.
- Choosing between multiple WordPress roots or multiple repositories.
- Using a temp path that may be publicly served.

## Scope Boundaries

This Skill does not migrate final visual CSS, Chrome Local Overrides spikes, responsive QA, Playwright MCP setup, project-local Playwright regression tests, WST Builder section creation, or Frontend Design QA work. Those belong to the `wst-builder` and `frontend-design-qa` plugins after setup is complete. Playwright MCP in particular is set up only in the local Cursor workspace through `frontend-design-qa` `setup-playwright-mcp`, never inside the Remote-SSH server workspace covered by this Skill.
