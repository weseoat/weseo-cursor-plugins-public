# Setup Orientation Reference

Supporting checks for the `setup-orientation` Skill. Keep real project values in Project Context or local developer storage. The Skill should complete all safe first-setup work automatically and leave only credential or policy decisions as explicit open questions.

## Communication Language

Communicate with the user in German throughout setup. Keep command names, file paths, placeholders, and external UI labels such as `Repository settings`, `Access tokens`, and `Remote-SSH: Connect to Host` unchanged when useful, but explain every user-facing instruction in German.

## Guided Wizard Flow

The Skill follows the old SmartFlow setup guide as an interactive wizard. Missing setup items are not passive blockers by default. Follow the prescribed setup path, ask only for the exact missing input, verify, and continue.

| Step | If present | If missing |
|---|---|---|
| Remote-SSH WordPress root | Verify `wp-content/`, `wp-admin/`, `wp-includes/`. | Guide the user to connect with `Remote-SSH: Connect to Host` and open the concrete WordPress directory. |
| Git repository | Record repo root, branch, redacted remote, identity. | Start the prescribed Bitbucket setup flow automatically: ask for repo name/URL if needed, ask the user to provide the Bitbucket token only through local terminal input, initialize locally, connect `origin`, verify with `git fetch origin`. |
| Git credentials | Verify `git fetch origin`. | Guide user through Bitbucket Access Token creation/storage and local-only remote configuration. |
| Project Context | Update missing non-secret facts. | Create `PROJECT-CONTEXT.md` and fill detected facts as setup progresses. |
| WP-CLI | Verify `--info` and `core version`. | Ask whether to install local `wp-cli.phar`, use maintainer-provided global `wp`, or consciously skip. |
| Cache flush | Document command. | Fill WESEO default after WP-CLI path is known, then ask before executing. |
| Cursor guidance | Verify `.cursor/rules` and `.cursor/skills` or marketplace availability. | Ask whether to install/refresh plugin guidance, use manual projection, or skip because marketplace is active. |
| Safe temp path | Create/verify outside webroot path. | Ask before using any path that may be publicly served. |
| MCP | Verify local-only `.cursor/mcp.json` or recorded skip. | Ask whether to create skeleton, configure WordPress MCP, configure Figma MCP, or skip. |

## SmartFlow Walkthrough Coverage

The old `smartflow-setup-guide.md` contains several places where the user needs more than a yes/no choice. Use a step-by-step walkthrough for these areas:

- Remote-SSH connection and opening the correct WordPress root.
- Git identity setup.
- Bitbucket Access Token and local remote URL setup.
- Cursor guidance / SmartFlow content installation.
- Project Context placeholder filling.
- WordPress MCP setup.
- Figma MCP setup.
- Final setup verification.

WP-CLI and safe temp path are newer server-ops additions, so keep them guided as well even though they were not full sections in the old guide.

## Wizard Question Templates

Ask one decision at a time. Use concrete choices only for optional or policy-dependent branches. Missing Git in a verified WordPress root is not a multiple-choice branch; use the prescribed Bitbucket setup prompt instead:

```md
Ich habe kein Git-Repository in `<wp-root>` gefunden.
SmartFlow-Projekte werden über das Bitbucket-Repository versioniert. Ich richte Git jetzt lokal in diesem WordPress-Root ein und verbinde es mit Bitbucket.

Bitte nenne mir den Repository-Namen oder die Repository-URL, falls sie noch nicht in `PROJECT-CONTEXT.md` steht. Danach führe ich dich Schritt für Schritt durch die Erstellung des Bitbucket Access Tokens und öffne dir direkt ein Terminal, in dem du den Token verdeckt eingibst. Den Token bitte nicht in den Chat schreiben.
```

```md
Ich finde keinen WP-CLI-Befehl: kein `wp-cli.phar` und kein globales `wp`.
Was soll ich als Nächstes tun?
- Lokales `wp-cli.phar` im WordPress-Root installieren.
- Ein globales `wp` verwenden, nachdem du es installiert/bereitgestellt hast.
- WP-CLI vorerst überspringen und den fehlenden Befehl dokumentieren.
```

```md
Ich finde keine projektlokalen `.cursor/rules` oder `.cursor/skills`.
Was soll ich als Nächstes tun?
- WESEO Plugin-Guidance für diesen Workspace installieren oder aktualisieren.
- Manuelle Projektion verwenden, weil Marketplace-Installation hier nicht verfügbar ist.
- Nur Marketplace-Guidance verwenden und dokumentieren, dass kein lokaler `.cursor/` Content erwartet wird.
- Cursor-Guidance vorerst überspringen.
```

```md
MCP ist optional.
Was soll ich als Nächstes tun?
- Ein lokales `.cursor/mcp.json` Grundgerüst erstellen.
- WordPress MCP konfigurieren, nachdem du ein Application Password erstellt hast.
- Figma MCP konfigurieren, nachdem du ein Figma Token erstellt hast.
- MCP vorerst überspringen.
```

## Bitbucket Setup Guide

When Git must be connected to Bitbucket, guide the user in German through the same sequence as the old SmartFlow setup guide:

1. Öffne Bitbucket im Browser.
2. Öffne das Ziel-Repository. Wenn du im Workspace startest, wähle zuerst das konkrete Projekt-Repository.
3. Gehe zu `Repository settings` -> `Access tokens` -> `Create`.
4. Verwende einen klaren Namen, z. B. `cursor-remote-ssh-<project>`.
5. Erteile Repository-Berechtigungen `Read` und `Write`.
6. Erstelle den Token.
7. Kopiere den Token nur einmal und speichere ihn im freigegebenen Passwortmanager oder OS-Keychain.
8. Füge den echten Token nicht in Chat, Doku, Screenshots, Commits oder `PROJECT-CONTEXT.md` ein.
9. Öffne direkt danach ein Terminal für die verdeckte Token-Eingabe. Der echte Token wird nur dort eingegeben, nie im Chat.

Use this German prompt before token entry:

```md
Öffne jetzt Bitbucket und erstelle den Repository Access Token:

1. Öffne das Ziel-Repository in Bitbucket.
2. Öffne `Repository settings`.
3. Öffne `Access tokens`.
4. Klicke `Create`.
5. Name: `cursor-remote-ssh-<project>`.
6. Berechtigungen: `Read` und `Write`.
7. Erstelle den Token, kopiere ihn einmalig und speichere ihn im Passwortmanager.

Wichtig: Bitte poste den Token nicht hier in den Chat. Ich öffne dir jetzt ein Terminal. Gib den Token dort ein, wenn die verdeckte Token-Abfrage erscheint.
```

When no Git repository exists in the verified WordPress root, initialize Git as part of the prescribed setup flow:

```sh
git init
```

After showing the guide, open a terminal immediately for token entry and remote setup.

For a new `origin`, run this terminal prompt:

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

Show the command shape with placeholders:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

If the repository already has `origin`, run this terminal prompt:

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

If the terminal prompt cannot be opened or interactive terminal input is unavailable, give the user this fallback placeholder shape and tell them to run it only in their local terminal:

```sh
git remote add origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Then verify:

```sh
git branch --show-current
```

If the branch does not exist locally yet, ask only for the required branch name before creating or checking out a branch. Existing WESEO projects commonly use `master`, but do not assume it for a new repository without confirmation.

## Remote-SSH Setup Guide

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

## Git Identity Setup Guide

When Git identity is missing, guide the user:

1. Erkläre, dass die Git Identity repository-lokal auf dem Server gesetzt wird.
2. Frage nach dem freigegebenen Namen und der WESEO E-Mail-Adresse, aber nicht nach Zugangsdaten.
3. Setze die Werte nur lokal im Repository:

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

## Cursor Guidance Setup Guide

The old guide installed SmartFlow content into the WordPress root. In the plugin flow, first prefer the approved marketplace/plugin install. If that is unavailable in the SSH workspace, use the documented manual projection fallback.

Guide the user:

1. Prüfe zuerst, ob die Team Marketplace Plugins aktiv sind.
2. Wenn Marketplace funktioniert, dokumentiere in `PROJECT-CONTEXT.md`, dass keine projektlokale `.cursor/` Kopie nötig ist.
3. Wenn Marketplace im SSH-Kontext nicht verfügbar ist, frage, ob die manuelle Projektion verwendet werden soll.
4. Installiere nur freigegebene Rules/Skills, keine privaten Setup-Notizen, Dumps oder Credentials.
5. Verifiziere:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

Expected guidance may include WordPress Server Ops, WST Builder, and Frontend Design QA content depending on the project role. Do not recreate the legacy SmartFlow repo layout blindly if marketplace plugins are the approved source.

## Project Context Fill Guide

When placeholders or missing project facts remain, guide the user through the fields instead of leaving generic placeholders:

1. Erkläre, dass `PROJECT-CONTEXT.md` nur nicht-geheime Projektdaten enthält.
2. Fülle automatisch erkannte Werte zuerst.
3. Frage dann nacheinander nach fehlenden nicht-geheimen Werten:
   - Projektname.
   - Live URL.
   - Staging/dev URL, falls vorhanden.
   - Repository-Name und Default Branch.
   - Bestätigte editierbare Pfade.
   - WP-CLI Command.
   - Cache Flush Command.
   - Safe Temp Path außerhalb des Public Webroot.
4. Lasse geheime Werte aus:
   - Tokens.
   - Application Passwords.
   - SSH private keys.
   - Token-bearing URLs.
   - Datenbank-Zugangsdaten.
   - Dumps.
5. Verifiziere, dass keine offensichtlichen Secrets in `PROJECT-CONTEXT.md` stehen.

## WordPress MCP Setup Guide

When the user chooses WordPress MCP, guide them:

1. Öffne WordPress im Browser.
2. Gehe zu `Benutzer` -> `Profil` -> `Application Passwords`.
3. Erstelle ein neues Application Password für Cursor/MCP.
4. Kopiere das Passwort nur einmal und speichere es lokal im Passwortmanager.
5. Erstelle oder aktualisiere `.cursor/mcp.json` lokal und untracked.
6. Trage echte Werte nur lokal in `.cursor/mcp.json` ein, nie in Chat oder getrackte Dateien.
7. Nutze in Doku/Chat nur die Platzhalterform:

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

8. Starte Cursor neu und prüfe `Settings` -> `Tools & MCP`.

## Figma MCP Setup Guide

When the user chooses Figma MCP, guide them:

1. Öffne Figma im Browser.
2. Gehe zu `Profile` -> `Settings` -> `Personal Access Tokens`.
3. Erstelle ein neues Token für Cursor/MCP.
4. Kopiere das Token nur einmal und speichere es lokal im Passwortmanager.
5. Ergänze `.cursor/mcp.json` lokal und untracked.
6. Nutze in Doku/Chat nur die Platzhalterform:

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

7. Starte Cursor neu und prüfe `Settings` -> `Tools & MCP`.

## Final Verification Walkthrough

Lead the user through the final checklist in German before saying setup is complete:

1. Cursor ist per Remote-SSH mit dem richtigen Host verbunden.
2. Der geöffnete Ordner ist der WordPress-Root.
3. Git Identity ist gesetzt.
4. Git Remote ist lokal eingerichtet und `git fetch origin` funktioniert.
5. `PROJECT-CONTEXT.md` existiert und enthält keine Secrets.
6. WP-CLI ist verfügbar oder bewusst geskippt.
7. Cache Flush Command ist dokumentiert.
8. Cursor Guidance ist verfügbar oder bewusst geskippt.
9. `.cursor/mcp.json` ist lokal-only oder MCP bewusst geskippt.
10. Safe Temp Path liegt außerhalb des Public Webroot.
11. Keine echten Tokens, Application Passwords, SSH Keys oder tokenhaltigen URLs wurden in Chat, Docs oder Commits geschrieben.

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

Inspect Git remotes only for setup, and never paste token-bearing URLs into notes or chat:

```sh
git remote get-url origin
git fetch origin
```

Report a redacted remote as `https://x-token-auth:<redacted>@<repo-host>/<repo-name>.git`, `https://<repo-host>/<repo-name>.git`, or `git@<repo-host>:<repo-name>.git`.

## Project Context Auto-Fill

If `PROJECT-CONTEXT.md` is missing, create it in the WordPress root from the project template shape. If it exists, fill missing setup values only.

| Context field | Detection source | Notes |
|---|---|---|
| WordPress root | `pwd` after WordPress root verification | Must contain `wp-content/`, `wp-admin/`, `wp-includes/`. |
| Server hostname | `hostname` or approved host alias | Do not include private coordinates in shared diagnostics unless approved. |
| Theme path | `wp-content/themes/astra-child/` existence | Use project exception if theme differs. |
| WST template path | `wp-content/plugins/weseo-smart-template-builder/` existence | Record missing plugin as an open question if WST work is expected. |
| Repository | Redacted `origin` URL | Store repo host/name only, never credentials. |
| Default/current branch | `git branch --show-current` or `.git/HEAD` | Existing WESEO projects often use `master`. |
| Repository access method | Remote URL shape | Values like `token-in-remote-url`, `credential-helper`, or `ssh`; no secrets. |
| WP-CLI command | `wp-cli.phar` or `command -v wp` | Prefer `php wp-cli.phar <command>` when local file exists. |
| Cache flush command | Existing Rules, handoff, or WESEO default | Do not run against live site unless approved. |
| Approved temp path | `$HOME/.weseo-tmp` or maintainer-approved outside-webroot path | Avoid `public_html` unless verified not publicly served. |
| Cursor guidance | `.cursor/rules`, `.cursor/skills` | `.cursor/` may be local-only and ignored by Git. |

## WESEO Defaults

Use these defaults when they match the detected project:

```sh
php wp-cli.phar <command>
```

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

For scratch files, prefer:

```sh
mkdir -p "$HOME/.weseo-tmp"
```

If the WordPress root is `/usr/home/<account>/public_html/wordpress-<id>`, a stricter safe temp path is usually under `/usr/home/<account>/`, not under `/usr/home/<account>/public_html/`.

## Verification Checklist

| Area | Check |
|---|---|
| Cursor Remote-SSH | Cursor is connected to the expected project host alias or approved connection target. |
| Workspace root | The opened folder matches `<wp-root>` and contains `wp-content/`, `wp-admin/`, and project-approved source paths. |
| Git repository | The repo root is known. If missing in a verified WordPress root, the prescribed Bitbucket setup flow has been started and either completed or stopped with an explicit reason. |
| Project Context | `PROJECT-CONTEXT.md` exists and `<server-hostname>`, `<wp-root>`, `<theme-path>`, `<wst-template-path>`, `<repo-name>`, `<branch-name>`, `<wp-cli-command>`, `<cache-flush-command>`, and `<path-outside-webroot>` are filled with non-secret values. |
| Git identity | `git config user.name` and `git config user.email` return maintainer-approved values for the local repository. |
| Repository access | `git fetch origin` succeeds using the approved local access method. |
| Secret handling | No real token, application password, SSH private key, complete token-bearing URL, or REST auth value is present in tracked docs. |
| Plugin content | Project-appropriate `.cursor/rules/`, `.cursor/skills/`, and release snapshot content are installed according to the internal release flow. |
| WP-CLI | `wp --info` or `php wp-cli.phar --info` succeeds, and the chosen command shape is documented. |
| Cache flush | The cache flush command is documented and only executed when approved. |
| Local MCP config | `.cursor/mcp.json`, if needed, exists only locally and is not tracked. |
| Scratch policy | Temporary scripts, dumps, and exports use `<path-outside-webroot>`, stay untracked, and are removed after use. |

## Completion Gates

Do not say "setup orientation is complete" unless every required gate is either verified or explicitly skipped by the user:

- Valid WordPress root opened over Remote-SSH.
- `PROJECT-CONTEXT.md` exists and contains the detected non-secret setup facts.
- Git is working through the Bitbucket remote, or setup was stopped with an explicit reason and next action. Do not mark Git as a normal optional skip for SmartFlow projects.
- WP-CLI is working, or WP-CLI was consciously skipped with reason and next action.
- Cache flush command is documented, or blocked because WP-CLI is intentionally unavailable.
- Cursor guidance is installed/available, or consciously skipped with reason and next action.
- Safe temp path exists outside the public webroot.
- MCP is configured locally or consciously skipped.
- No secrets were written to tracked files or chat.

## Open Question And Skip Format

When setup cannot complete automatically, ask the user what to do next. If the user chooses to stop or skip, record it with evidence and next action:

```md
- Skipped Git setup: no `.git` found in `<wp-root>`. User chose to skip because `<reason>`. Next action: initialize here, connect existing Bitbucket repo, or open the real repo root.
- Skipped WP-CLI setup: neither `wp-cli.phar` nor global `wp` found. User chose to skip because `<reason>`. Next action: install local `wp-cli.phar` or provide global `wp`.
- Temp path unresolved: candidate path is under `public_html`. User chose to stop. Next action: approve `$HOME/.weseo-tmp` or confirm the candidate path is not publicly served.
```

## Redaction Rules

Before sharing setup diagnostics, redact:

- Token-bearing remote URLs.
- Application passwords and REST auth strings.
- SSH identities, private-key paths when they identify private team setup, and passphrases.
- Private database dump names or storage locations.
- Private server coordinates when the maintainer has not approved sharing them.

Safe examples use placeholders such as `<token>`, `<repo-host>`, `<repo-name>`, `<domain>`, `<user>`, `<app-password>`, `<figma-api-key>`, `<wp-root>`, and `<path-outside-webroot>`.

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

The real `.cursor/mcp.json` is a local-only file. Do not commit it.

## Repository Access Notes

Token-in-remote-URL setup is allowed only when Project Context names it as the approved local access method. Tracked docs may show only this placeholder shape:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Real token-bearing URLs stay in local Git config and approved local storage only. If a diagnostic command prints the URL, redact it before sharing.
