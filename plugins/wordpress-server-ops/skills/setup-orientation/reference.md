# Setup Orientation Reference

Detailed walkthroughs, prompt templates, terminal flows, and coverage mapping for the `setup-orientation` Skill. The Skill should complete safe first-setup work automatically and ask only for the exact missing input or short confirmations before sensitive actions.

## Communication Language

Communicate with the user in German throughout setup. Keep command names, file paths, placeholders, and external UI labels (`Repository settings`, `Access tokens`, `Remote-SSH: Connect to Host`, `Settings → Tools & MCP`) unchanged when useful, but explain every user-facing instruction in German.

## Wizard Flow Summary

| Step | Detected | Missing |
|---|---|---|
| Remote-SSH WordPress root | Verify `wp-content/`, `wp-admin/`, `wp-includes/`. | Guide through `Remote-SSH: Connect to Host` and `Open Folder`. |
| Project facts | Record non-secret detections. | Run discovery commands automatically. |
| WST stack | Record active theme + WST/ACF/ACFE/WPGB/CPT UI plugins. | Note missing components as open question. |
| `PROJECT-CONTEXT.md` | Update missing non-secret values. | Create from template, fill detected facts. |
| Section/CPT handoff storage | Record project-configured storage locations for both handoff types. | Ask for the intended project locations or record `pending` with next action. |
| `LEARNINGS.md` | Record `exists` or `create when first learning appears`. | Missing file is safe to continue; do not fail setup. |
| Git repository | Verify `git fetch origin` and identity. | Run prescribed Bitbucket setup with hidden token prompt. |
| Restrictive `.gitignore` and `.cursor` skeleton | Confirm deny-all behavior and skeleton tracked. | Install baseline `.gitignore`, create `.gitkeep` files. |
| WP-CLI | Verify `--info`. | Install local `wp-cli.phar`, use global `wp`, or skip with reason. |
| Cache flush | Document and execute once after WP-CLI is verified. | Block until WP-CLI is decided. |
| Cursor guidance | Verify personal plugin guidance is loaded; document. | Walk through plugin verification, fall back to manual projection if SSH plugin guidance is unavailable. |
| MCP (WordPress + Figma) | Verify both servers under `Settings → Tools & MCP`. | Walk through credential creation, write `.cursor/mcp.json` only in the opened Cursor workspace as an untracked file, or record `pending: <reason>`. |
| Safe temp path | Verify path exists outside public webroot. | Create `$HOME/.weseo-tmp` after confirmation. |
| Frontend onboarding handoff | `frontend_onboarding: read`, `skipped (<reason>)`, or `pending` is recorded in `PROJECT-CONTEXT.md`. | Mandatory final step. Display the German handoff prompt, wait for an explicit answer, and record it before claiming setup complete. |

## Wizard Question Templates

Ask one decision at a time. Use concrete choices only for optional or policy-dependent branches.

```md
Ich finde keinen WP-CLI-Befehl: kein `wp-cli.phar` und kein globales `wp`.
Was soll ich als Nächstes tun?
- Lokales `wp-cli.phar` im WordPress-Root installieren.
- Ein globales `wp` verwenden, nachdem du es bereitgestellt hast.
- WP-CLI vorerst überspringen und den fehlenden Befehl in `PROJECT-CONTEXT.md` dokumentieren.
```

```md
Ich finde keine projektlokalen `.cursor/rules` oder `.cursor/skills`.
Sind die WESEO Cursor Plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) in deinem persönlichen Cursor-Account aktiv?
- Ja, Plugins laden hier auch über Remote-SSH.
- Plugins sind aktiv, aber im Remote-SSH-Workspace nicht verfügbar - manuelle Projektion verwenden.
- Plugins sind nicht aktiv - Setup pausieren, bis Plugins installiert sind.
```

```md
Wir richten jetzt die Cursor-Verbindungen zu WordPress und Figma ein.

Du erstellst die Zugangswerte in den jeweiligen Browser-Oberflächen. Die echten Werte kommen danach nur in den verdeckten Terminal-Prompt oder in `.cursor/mcp.json` im geöffneten Cursor-Workspace. Diese Datei bleibt untracked. Bitte nicht in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots schreiben.

Womit sollen wir beginnen?
- WordPress Application Password jetzt in WordPress erstellen.
- Figma Personal Access Token jetzt in Figma erstellen.
- Einen der beiden Werte noch nicht möglich: als `pending: <reason>` mit nächstem Schritt in `PROJECT-CONTEXT.md` dokumentieren.
```

## Discovery Commands

Run discovery from the opened Remote-SSH workspace. Redact secret-bearing output before reporting it.

```sh
pwd
hostname
ls -la
test -d wp-content && test -d wp-admin && test -d wp-includes && echo "wordpress-root"
test -d wp-content/themes/astra-child && echo "astra-child"
test -d wp-content/plugins/weseo-smart-template-builder && echo "wst"
test -f wp-cli.phar && echo "local-wp-cli"
command -v wp || true
git rev-parse --show-toplevel 2>/dev/null || true
git branch --show-current 2>/dev/null || true
git config user.name
git config user.email
git status --short 2>/dev/null || true
```

For the WST stack with WP-CLI:

```sh
php wp-cli.phar theme list --fields=name,status 2>/dev/null
php wp-cli.phar plugin list --fields=name,status,version 2>/dev/null
```

Look for the active theme `astra-child` (or another approved child theme), and active plugins for WST (`weseo-smart-template-builder`), ACF PRO (`advanced-custom-fields-pro`), ACF Extended (`acf-extended-pro` or `acf-extended`), WP Grid Builder (`wp-grid-builder`), and CPT UI (`custom-post-type-ui`). Plugin slug names may differ across projects; treat any missing component as an open WST stack question rather than a blocker.

Inspect Git remotes only for setup, never paste token-bearing URLs into notes or chat:

```sh
git remote get-url origin
git fetch origin
```

Report a redacted remote as `https://x-token-auth:<redacted>@<repo-host>/<repo-name>.git`, `https://<repo-host>/<repo-name>.git`, or `git@<repo-host>:<repo-name>.git`.

## Remote-SSH Setup Walkthrough

When the user has not connected to the server or has opened the wrong folder, guide them in German:

1. Öffne in Cursor die Command Palette mit `F1`.
2. Wähle `Remote-SSH: Connect to Host`.
3. Wenn der Host noch nicht existiert, wähle `Add New SSH Host`.
4. Lege den Host mit dem freigegebenen WESEO SSH-Alias an. Zeige nur Platzhalter:

```sshconfig
Host <ssh-host-alias>
  HostName <server-hostname>
  User <ssh-user>
  Port <ssh-port>
  IdentityFile <local-private-key-path>
  AddKeysToAgent yes
```

5. Speichere die SSH-Config lokal, nicht im Projekt.
6. Verbinde dich mit dem Host.
7. Wähle `Open Folder` und öffne den konkreten WordPress-Root, z. B. einen `wordpress-*` Ordner.
8. Verifiziere danach:

```sh
pwd
test -d wp-content && test -d wp-admin && test -d wp-includes
```

Do not ask the user to paste private SSH keys, passphrases, or private server coordinates into tracked files or chat.

## Project Context Fill Walkthrough

When `PROJECT-CONTEXT.md` is missing or incomplete, lead the user step by step instead of leaving generic placeholders. The old SmartFlow placeholder categories are collected here, not by editing plugin Rules.

1. Erkläre, dass `PROJECT-CONTEXT.md` der zentrale, nicht-geheime Projektkontext für spätere Skills ist.
2. Fülle automatisch erkannte Werte zuerst.
3. Frage dann nacheinander nach den fehlenden nicht-geheimen Werten.

Required non-secret fields:

| Field | Detection or source |
|---|---|
| Project name | User input. |
| Live URL | User input or detection from `wp-config.php`/`siteurl` if approved. |
| Staging/dev URL | User input. |
| Server hostname | `hostname` or approved alias. |
| WordPress root | `pwd` after root verification. |
| Theme path | `wp-content/themes/astra-child/` or detected child theme. |
| WST template path | `wp-content/plugins/weseo-smart-template-builder/`. |
| Section handoff storage | Project-configured storage location for filled Section handoffs. |
| CPT handoff storage | Project-configured storage location for filled CPT handoffs. |
| `LEARNINGS.md` status | `exists`, `create when first learning appears`, or `pending: <reason>`. |
| Repository host/name | Redacted `origin`. |
| Default/current branch | `git branch --show-current`. |
| Repository access method | `token-in-remote-url`, `credential-helper`, `ssh`. |
| WP-CLI command shape | `php wp-cli.phar <command>` or `wp <command>`. |
| Cache flush command | WESEO default unless project overrides. |
| Approved temp path | `$HOME/.weseo-tmp` or maintainer-approved outside-webroot path. |
| Editable path policy | Allowlist for `astra-child`, optional plugin allowlist. |
| WST stack status | Active theme + plugins detected. |
| Setup completion status | Per step: `done`, `pending: <reason>`, `skipped: <reason>`. |

Old SmartFlow placeholder categories (CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, ACF IDs, button variants, container widths, clamp values) belong in `PROJECT-CONTEXT.md` under a `Project specifics` section. The wizard collects them here so plugin Rules stay generic.

Never store: tokens, application passwords, SSH private keys, token-bearing URLs, REST credentials, database dumps, complete media inventories.

## Handoff And Learnings Storage Walkthrough

Section and CPT handoffs are project-local working contracts. The setup wizard records where the project wants filled handoffs to live; it does not store project-specific handoffs inside reusable plugin package folders.

For Section handoffs:

1. Explain in German that `wst-builder` owns the server-side Section foundation and emits the filled handoff before local frontend work starts.
2. Name the bundled reusable Section template: `plugins/wst-builder/handoffs/section-handoff.template.md`.
3. Ask for or record the concrete project storage location for filled Section handoffs.
4. If the storage location is not known yet, record `section_handoff_storage: pending: <reason> - next action: Maintainer confirms project handoff location before first Section handoff`.

For CPT handoffs:

1. Explain in German that CPT handoffs are separate from Section handoffs because they can include archive, taxonomy, WP Grid Builder, card, carousel, detail-page, and optional single-template decisions.
2. Ask for or record the concrete project storage location for filled CPT handoffs.
3. Name the bundled reusable CPT template: `plugins/wst-builder/handoffs/cpt-handoff.template.md`.
4. If the storage location is not known yet, record `cpt_handoff_storage: pending: <reason> - next action: Maintainer confirms project handoff location before first CPT handoff`.

For `LEARNINGS.md`:

1. Check the WordPress root for `LEARNINGS.md`.
2. If present, record `learnings: exists`.
3. If missing, record `learnings: create when first learning appears`.
4. Do not block setup only because `LEARNINGS.md` is missing.

## Bitbucket Git Setup Walkthrough

When Git must be connected to Bitbucket, the wizard runs in this strict order:

1. Confirm the repository name with the user. Ask for the concrete repo if it is missing; if `PROJECT-CONTEXT.md` already names one, ask the user to confirm that exact repo.
2. **Display the German token creation guide in chat and wait until the user confirms they created and stored the token in a password manager or OS keychain.** This step is non-skippable. Do not open a terminal or ask for the token before the user explicitly confirms the token is created and stored.
3. Open and explain the integrated terminal (`Ctrl+Ö` / `Ctrl+Backtick`) and run the hidden token prompt.
4. Verify with `git fetch origin`.

Never collapse steps 1-2 into "paste your token" or "confirm credentials". A user who does not yet have a token must always see the creation steps before any token prompt.

Guide the user in German through token creation:

1. Erkläre zuerst: Für Git brauchen wir einen Zugangsschlüssel für genau dieses Bitbucket-Repository. Bitbucket nennt ihn `Repository Access Token`.
2. Öffne `https://bitbucket.org/` im Browser.
3. Wähle den WESEO-/Projekt-Workspace, falls Bitbucket dich danach fragt.
4. Öffne `Repositories` und wähle das Ziel-Repository. Prüfe den Repo-Namen gegen `PROJECT-CONTEXT.md`.
5. Öffne links im Repository `Repository settings`.
6. Öffne `Access tokens`.
7. Klicke `Create` bzw. `Create repository access token`.
8. Verwende einen klaren Namen, z. B. `cursor-remote-ssh-<project>`.
9. Scrolle im Token-Dialog zu `Permissions` bzw. `Repository permissions`.
10. Hake `Read` und `Write` an. Vergib keine Admin-Rechte, außer der Maintainer verlangt sie explizit.
11. Erstelle den Token.
12. Kopiere den Token sofort, weil Bitbucket ihn nur einmal anzeigt.
13. Speichere ihn im Passwortmanager oder OS-Keychain.
14. Hinweis: Der Wizard trägt den Schlüssel nachher über eine verdeckte Terminal-Eingabe in die Git-Verbindung dieses Projekts ein. Technisch verwendet Git dabei HTTPS mit `x-token-auth` und speichert den Wert nur in der Git-Konfiguration dieses Projekts.
15. Füge den echten Token nicht in Chat, Doku, Screenshots, Git, Commits, tracked files oder `PROJECT-CONTEXT.md` ein.

Use this German prompt before token entry:

```md
Öffne jetzt Bitbucket und erstelle den Zugangsschlüssel für genau dieses Repository. Bitbucket nennt ihn `Repository Access Token`:

1. Öffne `https://bitbucket.org/`.
2. Wähle den WESEO-/Projekt-Workspace, falls Bitbucket dich danach fragt.
3. Öffne `Repositories` und wähle das Ziel-Repository. Prüfe den Repo-Namen gegen `<repo-name>`.
4. Öffne links im Repository `Repository settings`.
5. Öffne dort `Access tokens`.
6. Klicke `Create` bzw. `Create repository access token`.
7. Name: `cursor-remote-ssh-<project>`.
8. Scrolle im Token-Dialog zu `Permissions` bzw. `Repository permissions`.
9. Hake dort bei den Repository-Berechtigungen `Read` und `Write` an.
10. Keine Admin-Rechte vergeben.
11. Erstelle den Token.
12. Kopiere den Token sofort, weil Bitbucket ihn nur einmal anzeigt.
13. Speichere den Token im Passwortmanager oder OS-Keychain.

Hinweis: Der Wizard trägt den Schlüssel nachher über eine verdeckte Terminal-Eingabe in die Git-Verbindung dieses Projekts ein. Technisch verwendet Git dabei HTTPS mit `x-token-auth` und speichert den Wert nur in der Git-Konfiguration dieses Projekts.

Wichtig: Bitte poste den Token nicht in den Chat und schreibe ihn nicht in `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots. Ich öffne dir gleich ein Terminal in Cursor. Gib den Token dort ein, wenn die verdeckte Token-Abfrage erscheint.
```

## Terminal Handling For Token Entry

Not every team member is comfortable with the terminal. The wizard must explain how to open and use Cursor's integrated terminal step by step:

1. Erkläre: Das integrierte Terminal in Cursor läuft automatisch im geöffneten WordPress-Root, weil wir per Remote-SSH verbunden sind.
2. Öffne das Terminal mit `Ctrl+Ö` oder `Ctrl+Backtick`. Alternativ über das Menü `Terminal → New Terminal`.
3. Prüfe in der ersten Zeile, dass das Terminal im richtigen Ordner steht (`pwd` zeigt den WordPress-Root).
4. Erkläre: Beim Token-Prompt wird der Cursor scheinbar nicht reagieren, während du tippst. Das ist Absicht - die verdeckte Eingabe versteckt den Token.
5. Erkläre: Nach `Enter` wird der Token nicht angezeigt und nur in der Git-Konfiguration dieses Projekts gespeichert.

When no Git repository exists in the verified WordPress root, initialize Git first:

```sh
git init
```

For a new `origin`, the wizard should paste this script into the terminal so the user only enters the token at the hidden prompt:

```sh
REPO_HOST="<repo-host>"
REPO_NAME="<repo-name>"
printf "Bitbucket Access Token eingeben (Eingabe ist unsichtbar): "
read -s BITBUCKET_TOKEN
printf "\n"
AUTH_USER="x-token-auth"
REMOTE_URL="https://${AUTH_USER}:${BITBUCKET_TOKEN}@${REPO_HOST}/${REPO_NAME}.git"
git remote add origin "$REMOTE_URL"
git fetch origin
unset BITBUCKET_TOKEN
unset REMOTE_URL
```

If `origin` already exists:

```sh
REPO_HOST="<repo-host>"
REPO_NAME="<repo-name>"
printf "Bitbucket Access Token eingeben (Eingabe ist unsichtbar): "
read -s BITBUCKET_TOKEN
printf "\n"
AUTH_USER="x-token-auth"
REMOTE_URL="https://${AUTH_USER}:${BITBUCKET_TOKEN}@${REPO_HOST}/${REPO_NAME}.git"
git remote set-url origin "$REMOTE_URL"
git fetch origin
unset BITBUCKET_TOKEN
unset REMOTE_URL
```

If interactive terminal input is unavailable, give the user this fallback shape and tell them to run it only in the integrated terminal for the opened Remote-SSH workspace. Remind them that the real token must still not appear in chat, `PROJECT-CONTEXT.md`, tracked files, commits, diagnostics, or screenshots:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Verify access without exposing secrets:

```sh
git fetch origin
git branch --show-current
```

Do not run `git pull origin master` or any pull/checkout blindly. If the user wants to pull or check out a specific branch, ask for the branch name first and confirm before executing.

## Git Identity Walkthrough

When `git config user.name` or `git config user.email` is empty:

1. Erkläre, dass die Git Identity repository-lokal auf dem Server gesetzt wird.
2. Frage nach dem freigegebenen Namen und der WESEO E-Mail-Adresse.
3. Setze die Werte nur lokal:

```sh
git config user.name "<developer-name>"
git config user.email "<developer-email>"
```

4. Verifiziere:

```sh
git config user.name
git config user.email
```

Do not change global Git config unless the maintainer explicitly requests it.

## Restrictive `.gitignore` Baseline

Use this baseline unless `PROJECT-CONTEXT.md` defines a stricter allowlist:

```gitignore
# Ignore everything by default.
/**

# Keep repository setup files.
!/.gitignore
!/PROJECT-CONTEXT.md
!/README.md

# Keep the .cursor skeleton; mcp.json stays ignored.
!/.cursor/
/.cursor/**
!/.cursor/rules/
!/.cursor/rules/.gitkeep
!/.cursor/rules/**
!/.cursor/skills/
!/.cursor/skills/.gitkeep
!/.cursor/skills/**
/.cursor/mcp.json

# Allow wp-content as a parent only; contents stay ignored unless explicitly unignored below.
!/wp-content/
/wp-content/**

# Allow approved theme source paths.
!/wp-content/themes/
/wp-content/themes/**
!/wp-content/themes/ReadMe.md
!/wp-content/themes/betheme-child/
!/wp-content/themes/betheme-child/**
!/wp-content/themes/smarttheme-child/
!/wp-content/themes/smarttheme-child/**
!/wp-content/themes/astra-child/
!/wp-content/themes/astra-child/**

# Optional project-owned plugin allowlist.
# Enable only when PROJECT-CONTEXT.md confirms the plugin is project source, not a vendor/runtime plugin.
# !/wp-content/plugins/
# /wp-content/plugins/**
# !/wp-content/plugins/weseo-smart-template-builder/
# !/wp-content/plugins/weseo-smart-template-builder/**

# Ignore system and IDE files.
.idea/
.vscode/
.DS_Store
```

If `.gitignore` already exists, do not overwrite project-specific rules blindly. Update so it preserves deny-all behavior and explicitly unignores only project-approved source paths.

After writing `.gitignore`, verify what will be staged:

```sh
git status --short
git add .gitignore
test -f PROJECT-CONTEXT.md && git add PROJECT-CONTEXT.md
test -f README.md && git add README.md
test -d .cursor && git add .cursor
test -d wp-content/themes/betheme-child && git add wp-content/themes/betheme-child
test -d wp-content/themes/smarttheme-child && git add wp-content/themes/smarttheme-child
test -d wp-content/themes/astra-child && git add wp-content/themes/astra-child
git status --short
```

Stop and fix `.gitignore` before committing if `git status --short` shows WordPress core directories, uploads, cache directories, vendor plugins, dumps, media files, token-bearing config, `.cursor/mcp.json`, or unrelated runtime artifacts.

After scope verification and a short user confirmation, perform the initial commit and push:

```sh
git commit -m "Initial SmartFlow project setup"
git push -u origin "$(git branch --show-current)"
```

## `.cursor` Skeleton Walkthrough

Create the skeleton if missing:

```sh
mkdir -p .cursor/rules .cursor/skills
test -f .cursor/rules/.gitkeep || : > .cursor/rules/.gitkeep
test -f .cursor/skills/.gitkeep || : > .cursor/skills/.gitkeep
```

Confirm:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

Tracked content in `.cursor/rules/` and `.cursor/skills/` is reserved for project-specific Rules and Skills the team adds later. The shared WESEO guidance lives in the personal Cursor plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) and is not copied into the project.

`.cursor/mcp.json` always stays ignored. The wizard creates and updates it only in the opened Cursor workspace as an untracked file.

## Cursor Guidance Walkthrough

Verify whether the personal plugin guidance is loaded for this Remote-SSH workspace:

1. Frage den User, ob die Plugins `wordpress-server-ops`, `wst-builder`, `frontend-design-qa` in seinem persönlichen Cursor-Account aktiviert sind.
2. Frage, ob die Plugin-Skills im Remote-SSH-Workspace verfügbar sind: `setup-orientation`, `wp-media-import`, `grill-me`, `wst-section-workflow`, `wst-new-post-type`, `frontend-section-qa`, `cpt-frontend-qa`.
3. Wenn ja, dokumentiere in `PROJECT-CONTEXT.md`, dass die Plugin-Guidance aktiv ist und diese Workflow-Skills verfügbar sind; keine projektlokale Kopie nötig.
4. Wenn die Plugin-Guidance im SSH-Kontext nicht verfügbar ist, schlage manuelle Projektion vor: Plugin-Inhalte werden lokal in `.cursor/rules/` und `.cursor/skills/` projiziert, aber nur als Workaround. Dokumentiere die Abweichung.
5. Installiere niemals private Setup-Notizen, Dumps oder Credentials in `.cursor/`.

## WordPress MCP Walkthrough

When configuring WordPress MCP:

1. Erkläre zuerst: Cursor braucht für die WordPress-Verbindung einen eigenen Zugangsschlüssel aus deinem WordPress-Profil. WordPress nennt ihn `Application Password`.
2. Öffne WordPress im Browser unter der bekannten Live- oder Staging-URL.
3. Gehe zu `Benutzer` → `Profil` → `Application Passwords` (auf Deutsch ggf. `Anwendungs-Passwörter`).
4. Vergib einen klaren Namen, z. B. `cursor-mcp-<project>`.
5. Erstelle das Application Password und kopiere es sofort. WordPress zeigt es nur einmal an.
6. Speichere es im Passwortmanager oder OS-Keychain.
7. Hinweis: Die Site muss über HTTPS erreichbar sein.
8. Trage den echten Wert nur über den verdeckten Terminal-Prompt oder in `.cursor/mcp.json` im geöffneten Cursor-Workspace ein. Diese Datei bleibt untracked und darf nicht in Git oder Commits landen.
9. Schreibe den echten Wert nicht in Chat, `PROJECT-CONTEXT.md`, tracked files, Diagnosen oder Screenshots.
10. Tracked docs zeigen nur die Platzhalterform:

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
    }
  }
}
```

11. Starte Cursor neu und prüfe `Settings` → `Tools & MCP`, dass der `wordpress` Server grün/aktiv ist.

If the user cannot create an Application Password right now, record the gate as `pending: WordPress Application Password fehlt - next action: Benutzer -> Profil -> Application Passwords öffnen, neues Passwort erstellen, dann Step 8 erneut ausführen` in `PROJECT-CONTEXT.md`.

## Figma MCP Walkthrough

When configuring Figma MCP:

1. Erkläre zuerst: Cursor braucht für die Figma-Verbindung einen eigenen Zugangsschlüssel aus deinem Figma-Profil. Figma nennt ihn `Personal Access Token`.
2. Öffne Figma im Browser.
3. Gehe zu `Profile` → `Settings` → `Personal access tokens`.
4. Erstelle einen neuen Token mit klarem Namen, z. B. `cursor-mcp-<project>`.
5. Kopiere den Token sofort und speichere ihn im Passwortmanager oder OS-Keychain.
6. Trage den echten Wert nur über den verdeckten Terminal-Prompt oder in `.cursor/mcp.json` im geöffneten Cursor-Workspace ein. Diese Datei bleibt untracked und darf nicht in Git oder Commits landen. Nicht in Chat, `PROJECT-CONTEXT.md`, tracked files, Diagnosen oder Screenshots schreiben.
7. Tracked docs zeigen nur die Platzhalterform:

```json
{
  "mcpServers": {
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

8. Starte Cursor neu und prüfe `Settings` → `Tools & MCP`.

If the user cannot create a token right now, record the gate as `pending: Figma Personal Access Token fehlt - next action: Profile -> Settings -> Personal access tokens öffnen, neuen Token erstellen, dann Step 8 erneut ausführen` in `PROJECT-CONTEXT.md`.

## Frontend Onboarding Handoff Walkthrough

The mandatory final step (Step 11) must run even when the wizard resumes after a long pause, terminal interruption, or chat reset. Before declaring setup complete:

1. Read `PROJECT-CONTEXT.md` and look for a `frontend_onboarding` field. If it is missing or unset, the handoff still has to happen.
2. Display the German handoff prompt from `SKILL.md` Step 11 verbatim. Do not paraphrase it, do not collapse it into a generic summary, and do not embed it inside another question.
3. Wait for an explicit user answer. The three accepted shapes are:
   - "Zeig es mir": display [frontend-onboarding.md](frontend-onboarding.md) inline in chat. Record `frontend_onboarding: read` in `PROJECT-CONTEXT.md`.
   - "Kenne ich schon, überspringen": record `frontend_onboarding: skipped (already familiar)` in `PROJECT-CONTEXT.md`.
   - "Später": record `frontend_onboarding: pending` and the next concrete action (for example: `re-run setup-orientation Step 11`).
4. Only after the answer is recorded may the wizard say "Setup ist abgeschlossen".

If the wizard is invoked again later and `frontend_onboarding` is missing, run Step 11 immediately even if all other gates are already done.

## WESEO Defaults

```sh
php wp-cli.phar <command>
```

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

For scratch files:

```sh
mkdir -p "$HOME/.weseo-tmp"
```

If the WordPress root is `/usr/home/<account>/public_html/wordpress-<id>`, prefer a temp path under `/usr/home/<account>/`, not under `/usr/home/<account>/public_html/`.

## Final Verification Walkthrough

Lead the user through the final checklist in German before saying setup is complete:

1. Cursor ist per Remote-SSH mit dem richtigen Host verbunden.
2. Der geöffnete Ordner ist der WordPress-Root und enthält `wp-content/`, `wp-admin/`, `wp-includes/`.
3. WST Stack ist dokumentiert (Astra Child Theme, WST Plugin, ACF PRO, ACF Extended, WP Grid Builder, CPT UI) - oder fehlende Komponenten sind als offene Frage notiert.
4. `PROJECT-CONTEXT.md` existiert und enthält keine Secrets.
5. Section handoff storage und CPT handoff storage sind dokumentiert oder mit nächstem Schritt als `pending` markiert.
6. `LEARNINGS.md` Status ist dokumentiert; fehlende Datei blockiert nicht.
7. Git Identity ist gesetzt.
8. Git Remote ist lokal eingerichtet, `git fetch origin` funktioniert, redaktierte Remote-Form ist dokumentiert.
9. `.gitignore` verwendet Deny-all mit explizitem Allowlist; `git status --short` zeigt nur erlaubte Pfade.
10. Erstcommit und Erst-Push sind erfolgt oder bewusst pausiert.
11. WP-CLI ist verfügbar oder bewusst geskippt mit Grund/nächstem Schritt.
12. Cache Flush wurde ausgeführt oder bewusst pausiert.
13. `.cursor` Skeleton mit `.gitkeep` ist vorhanden; `.cursor/mcp.json` existiert nur im geöffneten Cursor-Workspace und ist untracked.
14. Co-installierte Plugin-Guidance ist für `wordpress-server-ops`, `wst-builder`, `frontend-design-qa` und die Workflow-Skills `grill-me`, `frontend-section-qa`, `cpt-frontend-qa` bestätigt oder mit Fallback dokumentiert.
15. WordPress MCP ist aktiv oder als `pending: <reason>` dokumentiert.
16. Figma MCP ist aktiv oder als `pending: <reason>` dokumentiert.
17. Safe Temp Path liegt außerhalb des Public Webroot.
18. Keine echten Tokens, Application Passwords, SSH Keys oder tokenhaltigen URLs wurden in Chat, `PROJECT-CONTEXT.md`, Git, Commits, tracked files, Diagnosen oder Screenshots geschrieben.
19. Der Frontend-Onboarding-Handoff (Schritt 11) wurde durchgeführt und das Ergebnis ist in `PROJECT-CONTEXT.md` als `frontend_onboarding: read`, `skipped (<reason>)` oder `pending` dokumentiert.

After this checklist and the Step 11 answer are recorded, present the final setup result in this order:

1. Non-technical overview in German: what is ready now, what the user can do next, and which setup points remain open.
2. Technical completion details: verified Git access, WP-CLI/cache status, MCP status, safe temp path, and `PROJECT-CONTEXT.md` gate records.

## Completion Gates

Do not say "setup orientation is complete" unless every required gate is verified or explicitly recorded as `pending: <reason>` / `skipped: <reason>` with the next concrete action:

- Valid WordPress root opened over Remote-SSH.
- `PROJECT-CONTEXT.md` exists with detected non-secret setup facts and recorded gates.
- Section handoff storage and CPT handoff storage are recorded or pending with next action.
- `LEARNINGS.md` status is recorded; missing `LEARNINGS.md` is not a hard failure.
- WST stack status is recorded.
- Git is working through the Bitbucket remote and identity is set, or stopped with explicit reason and next action. Git is not a normal optional skip for SmartFlow projects.
- Restrictive WordPress-root `.gitignore` and tracked `.cursor` skeleton are installed before any initial commit/push.
- Initial commit and push are done or explicitly pending.
- WP-CLI is working or pending/skipped with reason.
- Cache flush command is documented and was executed during setup, or pending because WP-CLI is unavailable.
- Cursor plugin guidance and required workflow skills (`grill-me`, `frontend-section-qa`, `cpt-frontend-qa`) are verified or fallback is documented.
- WordPress MCP is configured locally or pending with reason.
- Figma MCP is configured locally or pending with reason.
- Safe temp path exists outside the public webroot.
- No secrets were written to tracked files or chat.
- Frontend onboarding handoff (Step 11) is recorded in `PROJECT-CONTEXT.md` as `frontend_onboarding: read`, `skipped (<reason>)`, or `pending`.

## Open Question And Skip Format

When setup cannot complete automatically, ask the user what to do next. If the user chooses to stop or skip, record it with evidence and next action:

```md
- Skipped Git setup: no `.git` found in `<wp-root>`. User chose to skip because `<reason>`. Next action: initialize here, connect existing Bitbucket repo, or open the real repo root.
- Pending WordPress MCP: Application Password not yet available. Next action: create Application Password under Benutzer → Profil → Application Passwords and re-run MCP step.
- Pending Figma MCP: Figma token not yet available. Next action: create Personal Access Token under Profile → Settings → Personal access tokens and re-run MCP step.
- Pending cache flush: WP-CLI not yet installed. Next action: install local `wp-cli.phar` and re-run `cache flush`.
```

## Redaction Rules

Before sharing setup diagnostics, redact:

- Token-bearing remote URLs.
- Application passwords and REST auth strings.
- SSH identities, private-key paths when they identify private team setup, and passphrases.
- Private database dump names or storage locations.
- Private server coordinates when the maintainer has not approved sharing them.

Safe placeholders: `<token>`, `<repo-host>`, `<repo-name>`, `<domain>`, `<user>`, `<app-password>`, `<figma-api-key>`, `<wp-root>`, `<path-outside-webroot>`.

## MCP Example Shape

Tracked examples may show required fields with placeholders:

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

The real `.cursor/mcp.json` exists only in the opened Cursor workspace and stays untracked. Do not commit it.

## Coverage Mapping: Old SmartFlow Setup Guide

This mapping shows that every section of `weseo-smartflow-frontend-guide/anleitungen/smartflow-setup-guide.md` is covered by the wizard, modernized where the workflow has changed.

| Old guide section | Wizard coverage |
|---|---|
| Voraussetzungen (Cursor, SSH, Bitbucket, WST stack) | Step 1 (Remote-SSH), Step 2 (project facts + WST stack verification), Step 4 (Bitbucket Git). |
| 1. SSH-Verbindung herstellen | Step 1 + Remote-SSH walkthrough above. |
| 2. Git Identity konfigurieren | Step 4 + Git identity walkthrough. |
| 3. Bitbucket Repository Access Token erstellen | Step 4 + Bitbucket Git setup walkthrough. |
| 4. Remote URL auf HTTPS mit Token umstellen | Step 4 + Terminal handling for token entry (hidden prompt; verify with `git fetch origin` instead of `git pull origin master`). |
| 5. SmartFlow Content Repository herunterladen | Step 7 (Cursor guidance via personal plugins) + `.cursor` skeleton walkthrough. The legacy repo-copy approach is replaced by personal Cursor plugins; project-specific Rules/Skills can still be added under the tracked `.cursor` skeleton. |
| 6. Platzhalter ausfüllen (CPTs, Page IDs, FC Field Keys, Clone Group Keys, ACF IDs, button variants, container widths, clamp values) | Step 3 + Project Context fill walkthrough. Values are collected in `PROJECT-CONTEXT.md` instead of being edited into plugin Rules. |
| 7. MCP-Server einrichten (WordPress + Figma) | Step 8 + WordPress MCP walkthrough + Figma MCP walkthrough. Both servers are required gates (with `pending: <reason>` allowed). |
| 8. Überprüfen ob alles funktioniert | Step 10 + Final verification walkthrough + completion gates. |
| Nächster Schritt: Frontend-Dev Guide | Step 11 (mandatory frontend onboarding handoff) + `frontend-onboarding.md` as the resource displayed during that step. |

Additions not in the old guide that the wizard now covers:

- WST stack verification (Step 2).
- Restrictive deny-all `.gitignore` baseline before initial push (Step 5).
- Tracked `.cursor` skeleton with `.gitkeep` (Step 5).
- Cache flush execution as part of setup (Step 6).
- Safe temp path outside the public webroot (Step 9).
- Project-configured Section and CPT handoff storage locations (Step 3).
- `LEARNINGS.md` status as an optional project learning file (Step 3).
- Explicit `pending: <reason>` and `skipped: <reason>` records with next action.
