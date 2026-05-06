---
name: setup-orientation
description: Use when performing the complete first setup for a WESEO WordPress/WST project over Cursor Remote-SSH: open the WordPress root, detect or create project context, configure Git access, verify WP-CLI/cache, install Cursor guidance, and prepare local-only MCP config.
---

# Setup Orientation

Use this Skill for first-run setup or re-orientation in a WESEO WordPress/WST project opened through Cursor Remote-SSH. The expected outcome is a usable SSH development workspace, not just a list of open questions.

This is a guided setup wizard. Do not run discovery, create a partial `PROJECT-CONTEXT.md`, and then stop with blockers unless the user explicitly chooses to stop. When a required setup item is missing, explain the next SmartFlow setup step, follow the prescribed setup path, ask only for the exact required input, perform the action, verify it, update Project Context, and continue to the next step.

Communicate with the user in German throughout the wizard. Keep commands, file names, placeholders, and external UI labels in their original language when that makes them easier to find, but explain what the user should do in German.

Work in this order:

1. Discover what is already present.
2. Guide the user through each missing first-setup step from the old SmartFlow setup guide, using the walkthroughs in `reference.md`.
3. Create or update `PROJECT-CONTEXT.md` with non-secret facts as each step is completed.
4. Configure only local machine or repository settings that are safe to apply.
5. Verify Git, Cursor guidance, WP-CLI, cache, temp policy, and optional MCP.
6. Finish only when all completion gates pass or the user explicitly records a conscious skip.

Never ask the user to paste real tokens, application passwords, SSH keys, token-bearing URLs, private server coordinates, or credentials into tracked files, diagnostics, screenshots, shared chat, or commit messages.

## Guided Wizard Contract

For every setup step:

1. State what was detected.
2. State why the next step matters.
3. Follow the prescribed path when the SmartFlow setup flow defines one.
4. Ask the user only for the exact missing value or confirmation needed before sensitive, destructive, credential, or live-site-affecting actions.
5. Execute the chosen safe action.
6. Verify the result.
7. Update `PROJECT-CONTEXT.md` or local-only setup state.
8. Continue to the next step.

Use structured choices only for genuinely optional or policy-dependent branches. Ask one decision at a time. Good prompts are:

- "Ich finde keinen WP-CLI-Befehl. Soll ich ein lokales `wp-cli.phar` im WordPress-Root installieren, ein globales `wp` verwenden nachdem du es bereitgestellt hast, oder WP-CLI bewusst als nicht verfügbar dokumentieren?"
- "Ich finde keine `.cursor/rules` oder `.cursor/skills`. Soll ich die freigegebene WESEO Plugin-Guidance in diesem SSH-Workspace installieren, nur die Marketplace-Guidance verwenden, oder lokale Cursor-Inhalte bewusst überspringen?"
- "MCP ist optional. Soll ich jetzt ein lokales `.cursor/mcp.json` Grundgerüst erstellen, MCP überspringen, oder warten bis Application Passwords/Tokens bereit sind?"

Do not treat these as terminal blockers by default. They are wizard branches. Missing Git in a verified WordPress root is not an open multiple-choice branch; it starts the prescribed Bitbucket setup flow in Step 4.

## WESEO SSH Defaults

Most WESEO Remote-SSH WordPress sites follow the same shape. Use these as defaults unless Project Context or the maintainer says otherwise:

- Cursor connects through Remote-SSH and opens the WordPress installation directly.
- The WordPress root contains `wp-content/`, `wp-admin/`, and `wp-includes/`.
- The editable theme is usually `wp-content/themes/astra-child/`.
- WST templates usually live under `wp-content/plugins/weseo-smart-template-builder/`.
- A local `wp-cli.phar` in the WordPress root is preferred when global `wp` is not available.
- Common cache flush shape: `php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"`.
- Bitbucket remotes may use token-bearing HTTPS URLs locally. Treat them as secrets and redact before reporting.
- `.cursor/` may be ignored by Git even when Rules and Skills are installed there.

## Step 1: Connect And Open The WordPress Root

Use Cursor Remote-SSH with the project-provided host alias or connection details. Treat SSH config as local machine setup, not tracked project content.

If the user has not opened a folder yet, connect to the host and open the project WordPress root. For WESEO hosting, prefer the concrete WordPress directory under the site's web area, for example a `wordpress-*` directory, not the broader account or `public_html` parent.

Verify the workspace:

```sh
pwd
ls -la
test -d wp-content && test -d wp-admin && test -d wp-includes
```

If the opened folder is not the WordPress root, locate candidate WordPress roots without scanning unrelated account data. Check the current folder and one parent level for directories containing `wp-content/`, `wp-admin/`, and `wp-includes/`, then ask before switching if more than one candidate exists.

If no valid WordPress root is open, guide the user through the old SmartFlow Remote-SSH flow:

1. Open Command Palette.
2. Select `Remote-SSH: Connect to Host`.
3. Add or choose the WESEO host alias.
4. Open the concrete WordPress folder.
5. Return to the Skill and re-run root verification.

Use the detailed German Remote-SSH walkthrough in `reference.md` when the user needs help adding the host or choosing the folder.

## Step 2: Discover Project Facts

Collect non-secret facts before asking questions:

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

Do not print `git remote -v` directly in chat or notes. If you need to inspect remotes, run commands locally and redact credentials before sharing:

```sh
git remote get-url origin
```

Record only the redacted repository host/name, the branch, and whether the remote access method appears to be token-in-URL, credential-helper, or SSH.

## Step 3: Create Or Update Project Context

If `PROJECT-CONTEXT.md` is missing in the WordPress root, create it from the project template shape and fill what was detected. If it exists, update only missing or stale non-secret setup values.

At minimum, fill:

- Project name, live URL, and staging/dev URL when detectable or provided.
- Server hostname and WordPress root.
- Theme path and WST template path.
- Repository host/name and default/current branch.
- Repository access method as a non-secret description.
- Local credential storage as a non-secret method, if known.
- WP-CLI command shape.
- Cache flush command shape.
- Approved temp path outside the public webroot.
- Editable path policy from `.gitignore`, existing Rules, or maintainer guidance.

Never store real tokens, application passwords, SSH private keys, database dumps, complete token-bearing URLs, or REST credentials in Project Context.

If required non-secret values are missing, guide the user through the Project Context walkthrough in `reference.md` instead of leaving generic placeholders unexplained.

## Step 4: Confirm Or Configure Local Git

Work from the verified WordPress root. If the Git repository already exists, use it.

If no repository exists in the WordPress root, do not present multiple setup choices. Start the prescribed SmartFlow Git setup automatically:

1. State that no Git repository was detected in the verified WordPress root.
2. State that SmartFlow projects use the project Bitbucket repository as the required Git source.
3. Ask the user for the Bitbucket repository name or URL if it was not detected from Project Context.
4. Guide the user step by step through creating a repository Access Token in Bitbucket, then open a terminal prompt where the user can enter the token locally and invisibly. Do not ask the user to paste the token into chat.
5. Run `git init` in the WordPress root only as part of this prescribed Bitbucket setup after explaining that the repository will be initialized locally and connected to Bitbucket.
6. Add or update `origin` with the token-bearing HTTPS URL only in local Git config.
7. Verify access with `git fetch origin`.
8. Create or update the WordPress-root `.gitignore` before any initial `git add`, commit, or push so WordPress core, uploads, caches, and unmanaged plugins are not staged.
9. Show `git status --short` and verify that only the `.gitignore`, non-secret setup docs, and approved source paths appear as trackable changes.
10. Perform the initial commit and push only after the ignore policy is in place.
11. Update Project Context only with the redacted repository host/name, branch, and non-secret access method.

Guide the user through creating or retrieving the approved Access Token in Bitbucket, storing it in the approved local credential storage, and configuring the local remote URL with the real token only in local Git config. Show tracked docs only the placeholder form:

When Bitbucket must be set up, guide the user step by step like the old SmartFlow setup guide:

1. Open Bitbucket in the browser.
2. Open the project workspace or the target repository.
3. If the user starts from the workspace overview, choose the exact project repository first.
4. Go to `Repository settings` -> `Access tokens` -> `Create`.
5. Name the token clearly, for example `cursor-remote-ssh-<project>`.
6. Grant repository permissions `Read` and `Write`.
7. Create the token.
8. Copy the token once and store it in the approved password manager or OS keychain.
9. Do not paste the token into chat, tracked docs, screenshots, commit messages, or `PROJECT-CONTEXT.md`.
10. Immediately open a terminal prompt for token entry after the user has the token. The user must enter the real token only into that terminal prompt, never into chat or tracked files.

Use this German prompt shape:

```md
Öffne jetzt Bitbucket und erstelle den Repository Access Token:

1. Öffne das Ziel-Repository in Bitbucket.
2. Öffne `Repository settings`.
3. Öffne `Access tokens`.
4. Klicke `Create`.
5. Name: `cursor-remote-ssh-<project>`.
6. Berechtigungen: `Read` und `Write`.
7. Erstelle den Token, kopiere ihn einmalig und speichere ihn im Passwortmanager.

Wichtig: Bitte poste den Token nicht hier in den Chat. Gib ihn im nächsten Schritt nur im lokalen Terminal ein, wenn die verdeckte Token-Abfrage erscheint.
```

After showing the guide, open a terminal immediately for the token entry and remote setup. Use the detected repo host/name to prefill `REPO_HOST` and `REPO_NAME`; ask only for the missing repo name if it is not known.

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

If `origin` already exists, run this terminal prompt:

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

If the terminal prompt cannot be opened or interactive terminal input is unavailable, give the user this placeholder shape and tell them to run it only in their local terminal:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Then verify with:

```sh
git branch --show-current
```

Before the first `git add`, commit, or push from the WordPress root, install or update a restrictive `.gitignore`. The goal is to avoid pushing the full WordPress installation. Start from this baseline unless Project Context defines a stricter project allowlist:

```gitignore
# Ignore everything by default.
/**

# Keep repository setup files.
!/.gitignore
!/PROJECT-CONTEXT.md
!/README.md

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
# Enable only when Project Context confirms the plugin is project source, not a vendor/runtime plugin.
# !/wp-content/plugins/
# /wp-content/plugins/**
# !/wp-content/plugins/<plugin-name>/
# !/wp-content/plugins/<plugin-name>/**

# Ignore system and IDE files.
.idea/
.vscode/
.DS_Store
```

If `.gitignore` already exists, do not overwrite project-specific rules blindly. Update it so it still starts from deny-all behavior and explicitly unignores only project-approved source paths.

After writing `.gitignore`, verify the staging scope before the first push:

```sh
git status --short
git add .gitignore
test -f PROJECT-CONTEXT.md && git add PROJECT-CONTEXT.md
test -f README.md && git add README.md
test -d wp-content/themes/betheme-child && git add wp-content/themes/betheme-child
test -d wp-content/themes/smarttheme-child && git add wp-content/themes/smarttheme-child
test -d wp-content/themes/astra-child && git add wp-content/themes/astra-child
git status --short
```

Stop and correct `.gitignore` before committing if `git status --short` shows WordPress core directories, uploads, cache directories, vendor plugins, database dumps, media files, token-bearing config, `.cursor/mcp.json`, or unrelated runtime artifacts.

For the normal initial push after the staging scope is verified:

```sh
git commit -m "Initial SmartFlow project setup"
git push -u origin "$(git branch --show-current)"
```

Check repository-local identity:

```sh
git config user.name
git config user.email
```

If either value is missing, configure the local repository identity with maintainer-approved values:

```sh
git config user.name "<developer-name>"
git config user.email "<developer-email>"
```

Do not change global Git config unless the maintainer explicitly asks.

If the user does not know which Git identity to use, guide them through the Git identity walkthrough in `reference.md`.

Use the approved repository access method from Project Context or detected existing config.

If the approved method is a token-bearing remote URL, keep it local-only and use placeholders in tracked docs:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Real token-bearing URLs must never be committed, copied into tracked notes, included in diagnostics, captured in screenshots, shared in chat, or used in commit messages. Store the real token only in the approved local credential storage.

Verify repository access without exposing secrets:

```sh
git fetch origin
```

If `git fetch origin` fails because credentials are missing, guide the user to create or retrieve the approved access credential, then have them paste only the command shape with placeholders into tracked docs. Never request the real token in chat.

## Step 5: Confirm WP-CLI And Cache Commands

Prefer the command shape already documented in Project Context or project Rules. If none is documented:

- Use `php wp-cli.phar <command>` when `wp-cli.phar` exists in the WordPress root.
- Otherwise use `wp <command>` only if `command -v wp` succeeds.
- If neither exists, ask whether to install a local `wp-cli.phar`, use a maintainer-provided global `wp`, or consciously skip WP-CLI for this workspace.

For the normal WESEO setup path, install local WP-CLI only after the user confirms:

```sh
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
php wp-cli.phar --info
```

If `curl` is unavailable, ask before using an alternate download method. Do not download tools into a public scratch path; the local `wp-cli.phar` belongs in the WordPress root only when the project accepts that command shape.

Verify WP-CLI without changing site state:

```sh
php wp-cli.phar --info
php wp-cli.phar core version
```

or:

```sh
wp --info
wp core version
```

Set the cache flush command in Project Context. For typical WESEO WordPress roots with local `wp-cli.phar`, use:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

Run cache flush only when the setup task requires verification or the maintainer approves it. It can affect a live site.

## Step 6: Set Safe Temp And Scratch Policy

Choose an approved temp path outside the public WordPress root and outside any public web-served directory.

For WESEO paths where the WordPress root is inside a `public_html` tree, prefer a path outside `public_html`, for example the account home or a hidden temp directory under it:

```sh
mkdir -p "$HOME/.weseo-tmp"
```

Use a parent path inside `public_html` only after verifying it is not publicly served. Record the final path in Project Context. Database dumps, exports, temporary PHP scripts, and setup scratch files must:

- Live under the approved temp path outside the public webroot.
- Stay untracked.
- Be removed as soon as the task is complete.
- Never be placed inside the WordPress root, plugin packages, project template, or any public web path.

## Step 7: Install Or Update Cursor Guidance

Install or update the released internal plugin content according to the project-approved release and install flow. The expected result is that project-appropriate Rules and Skills are available to Cursor, while project-specific values stay in Project Context.

Verify the installed guidance shape:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

If `.cursor/rules` or `.cursor/skills` are missing, do not finish with only a blocker. Ask which setup path the user wants:

- Install or refresh WESEO plugin guidance through the approved marketplace/plugin flow.
- Project local copy/projection for SSH workspaces that cannot use marketplace plugins.
- Skip project-local Cursor content because the team marketplace is already active for this workspace.

After the chosen path, verify the guidance shape again. If `.cursor/` is ignored by Git, that is acceptable for existing SSH projects when the internal install flow expects local Cursor guidance. Record the local-only status in Project Context or setup notes. Do not copy private setup notes, real access values, database dumps, or operational scratch files into tracked plugin or template content.

Use the Cursor guidance walkthrough in `reference.md` when the user needs a step-by-step explanation of marketplace install versus manual projection.

## Step 8: Create Local-Only MCP Config If Needed

Create `.cursor/mcp.json` only as a local developer file. It must stay untracked.

MCP setup is a guided optional step. Ask whether to:

- Create a local-only `.cursor/mcp.json` skeleton now.
- Configure WordPress MCP after the user creates a WordPress application password.
- Configure Figma MCP after the user creates a Figma token.
- Skip MCP for this project.

If the user chooses WordPress or Figma MCP, guide them through creating the credential in the external UI, storing it locally, and inserting it only into local `.cursor/mcp.json`. Never ask them to paste the real value into chat.

Use the WordPress MCP and Figma MCP walkthroughs in `reference.md` when those branches are selected.

Use placeholders in examples and keep real values in approved local storage:

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

Restart Cursor after changing MCP config and verify the tools in Cursor settings. Do not commit `.cursor/mcp.json`.

## Step 9: Final Verification

Use `reference.md` for the detailed verification checklist. At minimum, confirm:

- Cursor is connected to the expected Remote-SSH workspace.
- The opened folder is the WordPress root and contains `wp-content/`, `wp-admin/`, and `wp-includes/`.
- Git repository location, current branch, and redacted remote identity are known.
- Git identity and repository access work locally without exposing secrets.
- The WordPress-root `.gitignore` deny-all allowlist is in place before any initial commit or push, and `git status --short` does not show WordPress core, uploads, caches, unmanaged plugins, dumps, media, or local secret files.
- `PROJECT-CONTEXT.md` exists and contains non-secret setup coordinates.
- WP-CLI command is verified or recorded as an open setup question.
- Cache flush command is documented.
- Required Rules and Skills are installed or the install flow blocker is recorded.
- `.cursor/mcp.json`, if present, is local-only and untracked.
- Operational scratch policy is recorded and uses an outside-webroot path.

If any required gate is not satisfied, ask the user whether to fix it now, consciously skip it, or stop setup. Do not claim setup is complete while required gates are still unresolved.

Use the final verification walkthrough in `reference.md` to lead the user through the same checklist style as the old SmartFlow setup guide.

## Outputs

When setup orientation is complete, leave behind:

- A usable Remote-SSH Cursor workspace opened at the WordPress root.
- `PROJECT-CONTEXT.md` filled with detected non-secret facts and any remaining open questions.
- Working local Git access through the approved project method.
- Verified WP-CLI command and documented cache flush command.
- Verified Cursor Rules and Skills installation.
- A safe temp/scratch path outside the public webroot.
- Local-only MCP config only when needed, never tracked.

If the user consciously skips a setup item, record it as an explicit skipped item with the detected evidence, reason, and exact next action needed. Do not call it complete without marking the skip.

## Stop Conditions

Stop and ask before:

- Creating a new Git repository or replacing an existing remote.
- Running commands that change live site behavior, except low-risk setup verification.
- Storing or displaying a credential.
- Choosing between multiple WordPress roots or multiple repositories.
- Using a temp path that may be publicly served.

## Scope Boundaries

Do not change the resolved Setup And Access Policy. Do not migrate final visual CSS, Chrome Local Overrides spikes, responsive QA, Playwright checks, WST Builder setup, or Frontend Design QA setup in this Skill. Do not store or request real secrets in tracked content.
