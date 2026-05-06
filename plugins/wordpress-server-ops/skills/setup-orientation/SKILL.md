---
name: setup-orientation
description: Guided wizard for the complete first setup of a WESEO WordPress/WST project over Cursor Remote-SSH. Use when starting a new project, re-orienting an existing SSH workspace, opening the WordPress root, creating or updating PROJECT-CONTEXT.md, configuring local Bitbucket Git access via hidden terminal prompt, verifying WP-CLI and cache flush, preparing the .cursor skeleton, and configuring local-only MCP for WordPress and Figma.
---

# Setup Orientation

Run this Skill as a guided wizard for the first run of a WESEO WordPress/WST project opened through Cursor Remote-SSH. The expected outcome is a usable SSH development workspace with `PROJECT-CONTEXT.md` filled, working local Git, verified WP-CLI/cache, a tracked `.cursor` skeleton, and local-only MCP for WordPress and Figma.

The wizard must work from any starting state. If Cursor is open without a Remote-SSH connection, lead the user through Remote-SSH first. If the WordPress root is already open and Git is already configured, skip those steps and continue.

Communicate with the user in German throughout the wizard. Keep commands, file names, placeholders, and external UI labels in their original language.

Never ask the user to paste real tokens, application passwords, SSH keys, token-bearing URLs, private server coordinates, or credentials into chat, tracked files, diagnostics, screenshots, or commit messages.

## Guided Wizard Contract

For every setup step:

1. State what was detected and why the next step matters.
2. Follow the prescribed safe path automatically when possible.
3. Ask only for the exact missing input or a short confirmation before sensitive, destructive, credential, or live-site-affecting actions.
4. Execute the chosen safe action.
5. Verify the result.
6. Update `PROJECT-CONTEXT.md` or local-only setup state.
7. Continue to the next step.

Run safe, reversible, or verifying steps automatically (`pwd`, root checks, `mkdir -p`, `git fetch origin`, writing `PROJECT-CONTEXT.md`, writing `.gitignore` and `.cursor` skeleton, MCP skeleton). Ask for short confirmation before: changing or adding a Git remote, the initial commit and push, executing `cache flush` against the live site, writing real Application Passwords or Figma tokens into local `.cursor/mcp.json`.

The detailed step-by-step walkthroughs, prompt templates, terminal flows, `.gitignore` baseline, MCP setup guides, completion gates, redaction rules, and the old-guide coverage mapping live in [reference.md](reference.md).

## WESEO SSH Defaults

Use these defaults unless Project Context or the maintainer says otherwise:

- Cursor connects through Remote-SSH and opens the WordPress installation directly.
- The WordPress root contains `wp-content/`, `wp-admin/`, and `wp-includes/`.
- The editable theme is usually `wp-content/themes/astra-child/`.
- The WST stack is Astra Child Theme, WST plugin (`weseo-smart-template-builder`), ACF PRO, ACF Extended, WP Grid Builder, CPT UI.
- A local `wp-cli.phar` in the WordPress root is preferred when global `wp` is not available.
- Default cache flush command: `php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"`.
- Bitbucket remotes use HTTPS with `x-token-auth`. The real token never leaves the local Git config.
- `.cursor/mcp.json` is always local-only and never tracked. `.cursor/rules/.gitkeep` and `.cursor/skills/.gitkeep` are tracked.

## Step 1: Connect And Open The WordPress Root

If Cursor has no Remote-SSH connection or the open folder is not a WordPress root, guide the user through `Remote-SSH: Connect to Host` and `Open Folder` using the Remote-SSH walkthrough in [reference.md](reference.md).

Verify the workspace:

```sh
pwd
ls -la
test -d wp-content && test -d wp-admin && test -d wp-includes
```

If the opened folder is not a WordPress root, look for `wordpress-*` candidates in the current folder and one parent level. Ask before switching if more than one candidate exists. Do not scan unrelated account data.

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

When WP-CLI is available, also verify the WST stack:

```sh
php wp-cli.phar theme list --status=active --field=name 2>/dev/null || wp theme list --status=active --field=name 2>/dev/null
php wp-cli.phar plugin list --status=active --field=name 2>/dev/null || wp plugin list --status=active --field=name 2>/dev/null
```

If WP-CLI is not yet available, fall back to filesystem checks for `astra-child`, `weseo-smart-template-builder`, ACF, ACF Extended, WP Grid Builder, and CPT UI. Record the result and any missing components in `PROJECT-CONTEXT.md` as an open WST stack question.

Do not print `git remote -v` directly in chat or notes. To inspect the remote, use `git remote get-url origin` locally and report only a redacted shape.

## Step 3: Project Context As Required Contract

`PROJECT-CONTEXT.md` is the project's non-secret context contract. Later WordPress, WST, and Frontend Skills must read it first to understand the project and update it when new non-secret facts are confirmed.

If `PROJECT-CONTEXT.md` is missing in the WordPress root, create it. If it exists, update only missing or stale non-secret values.

At minimum, fill:

- Project name, live URL, and staging/dev URL.
- Server hostname and WordPress root.
- Theme path and WST template path.
- Repository host/name and default/current branch.
- Repository access method as a non-secret description (`token-in-remote-url`, `credential-helper`, or `ssh`).
- WP-CLI command shape.
- Cache flush command shape.
- Approved temp path outside the public webroot.
- Editable path policy for the project.
- WST stack status (theme + plugins) or open question.
- Setup completion status per step (`done`, `pending: <reason>`, `skipped: <reason>`).

Never store real tokens, application passwords, SSH private keys, token-bearing URLs, REST credentials, dumps, or media inventories.

If required non-secret values are missing, follow the Project Context fill walkthrough in [reference.md](reference.md). The old SmartFlow placeholder categories (CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, button variants, container widths, clamp values, ACF IDs) are collected here, not by editing plugin Rules.

## Step 4: Git Through Bitbucket

If the WordPress root already has a working Git repository with `git fetch origin` succeeding, only verify identity and continue.

If no Git repository is present, follow the prescribed Bitbucket flow in [reference.md](reference.md):

1. Confirm the Bitbucket repository name or URL with the user.
2. Walk the user through creating a Repository Access Token in Bitbucket with `Read` and `Write` scope.
3. Open a terminal in Cursor and run the hidden token prompt to set up `origin` with `x-token-auth`. The user pastes the token only into that terminal.
4. Verify access with `git fetch origin`. Do not run `git pull origin master` blindly.
5. Configure repo-local `git config user.name` and `git config user.email` if missing.

Never request the real token in chat. Tracked docs only show the placeholder shape:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

The wizard must clearly explain how to open Cursor's integrated terminal (`Ctrl+Ö` or `Ctrl+Backtick`) and what to type when the hidden prompt appears, and provide a copy-paste fallback if the integrated terminal is unavailable. Detailed terminal guidance lives in [reference.md](reference.md).

## Step 5: Restrictive `.gitignore` And `.cursor` Skeleton

Before any initial `git add`, commit, or push, install or update a deny-all WordPress-root `.gitignore`. The full baseline is in [reference.md](reference.md).

The default allowlist covers:

- Setup files (`.gitignore`, `PROJECT-CONTEXT.md`, `README.md`).
- Detected child themes (`astra-child`, `betheme-child`, `smarttheme-child`).
- The `.cursor` skeleton: `.cursor/rules/.gitkeep` and `.cursor/skills/.gitkeep` plus any future project-specific Rules and Skills.
- `.cursor/mcp.json` stays ignored.

Project-owned plugins (including `weseo-smart-template-builder` when project policy makes it editable) are added to the allowlist only after explicit confirmation in `PROJECT-CONTEXT.md` or by the maintainer.

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

## Step 6: WP-CLI And Cache Flush

Confirm or install WP-CLI:

- Use `php wp-cli.phar <command>` when `wp-cli.phar` exists in the WordPress root.
- Otherwise use `wp <command>` if `command -v wp` succeeds.
- If neither is available, ask whether to install local `wp-cli.phar`, use a maintainer-provided global `wp`, or consciously skip with reason.

Verify without changing site state:

```sh
php wp-cli.phar --info && php wp-cli.phar core version
```

After WP-CLI is verified and the WordPress root is confirmed, run the cache flush as part of setup with a short confirmation:

```sh
php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"
```

Record the chosen command shape and the cache flush command in `PROJECT-CONTEXT.md`.

## Step 7: Cursor Guidance

The user runs Cursor with the WESEO plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) installed in their personal Cursor account. The wizard does not copy plugin Rules or Skills into the project.

The project repository keeps a `.cursor` skeleton (Step 5) so the team can add project-specific Rules and Skills later.

Verify and document:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

Record in `PROJECT-CONTEXT.md` whether the personal plugin guidance is active for this Remote-SSH workspace and whether project-specific Rules or Skills exist. If plugin guidance is not available in the SSH context, follow the Cursor Guidance fallback walkthrough in [reference.md](reference.md).

## Step 8: Local-Only MCP For WordPress And Figma

WordPress MCP and Figma MCP are required setup gates because the team uses both. Real values stay only in local `.cursor/mcp.json`. Tracked docs only show placeholder shapes.

The wizard must:

1. Walk the user through creating a WordPress Application Password under `Benutzer` → `Profil` → `Application Passwords`.
2. Walk the user through creating a Figma Personal Access Token under `Profile` → `Settings` → `Personal access tokens`.
3. Open a hidden-input terminal flow (or local-only file edit) so the real values land only in `.cursor/mcp.json`. Detailed steps live in [reference.md](reference.md).
4. Restart Cursor and verify both servers under `Settings` → `Tools & MCP`.

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

## Step 9: Safe Temp And Scratch Policy

Choose an approved temp path outside the public webroot. For WordPress roots inside a `public_html` tree, prefer `$HOME/.weseo-tmp`:

```sh
mkdir -p "$HOME/.weseo-tmp"
```

Database dumps, exports, temporary PHP scripts, and setup scratch files must live under that path, stay untracked, and be removed after use. Record the final path in `PROJECT-CONTEXT.md`.

## Step 10: Final Verification

Use the final verification walkthrough in [reference.md](reference.md). Confirm at minimum:

- Cursor is connected to the expected Remote-SSH workspace and the open folder is the WordPress root.
- `PROJECT-CONTEXT.md` exists and contains the detected non-secret setup coordinates with no secrets.
- Git is working through the Bitbucket remote with `git fetch origin` succeeding, identity is set, and the deny-all `.gitignore` produced a clean staging scope before the initial push.
- WST stack status is recorded.
- WP-CLI is verified and the cache flush command was executed during setup.
- The `.cursor` skeleton with `.gitkeep` files is in place; `.cursor/mcp.json` is local-only.
- WordPress MCP and Figma MCP are active or recorded as `pending: <reason>` with next action.
- Safe temp path exists outside the public webroot.
- No real tokens, application passwords, SSH keys, or token-bearing URLs were written to chat, tracked docs, or commits.

If any required gate is not satisfied, ask the user whether to fix it now, consciously skip with reason and next action, or stop. Do not claim setup complete while required gates are unresolved.

## Optional Handoff

After setup is complete or recorded as `pending`, offer the modern frontend onboarding resource [frontend-onboarding.md](frontend-onboarding.md) for users who are new to the WESEO three-plugin workflow (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`), Remote vs local work, Figma usage in WST preparation, and `PROJECT-CONTEXT.md` as the project facts source. Experienced users can skip it.

## Outputs

When the wizard finishes, leave behind:

- A usable Remote-SSH Cursor workspace at the WordPress root.
- `PROJECT-CONTEXT.md` filled with non-secret facts, recorded gates, and any `pending`/`skipped` notes with reason and next action.
- Working local Git through the approved Bitbucket access method.
- Verified WP-CLI and a documented, executed cache flush command.
- Tracked `.cursor` skeleton (`.gitkeep` files) and untracked `.cursor/mcp.json`.
- Local-only MCP config for WordPress and Figma, or recorded `pending: <reason>` for either.
- Safe temp/scratch path outside the public webroot.

## Stop Conditions

Stop and ask before:

- Creating a new Git repository or replacing an existing remote.
- Performing the initial commit or push.
- Running cache flush or any command that changes live site state.
- Storing or displaying a credential.
- Choosing between multiple WordPress roots or multiple repositories.
- Using a temp path that may be publicly served.

## Scope Boundaries

This Skill does not migrate final visual CSS, Chrome Local Overrides spikes, responsive QA, Playwright checks, WST Builder section creation, or Frontend Design QA work. Those belong to the `wst-builder` and `frontend-design-qa` plugins after setup is complete.
