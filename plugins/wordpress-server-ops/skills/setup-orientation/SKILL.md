---
name: setup-orientation
description: Use when performing the complete first setup for a WESEO WordPress/WST project over Cursor Remote-SSH: open the WordPress root, detect or create project context, configure Git access, verify WP-CLI/cache, install Cursor guidance, and prepare local-only MCP config.
---

# Setup Orientation

Use this Skill for first-run setup or re-orientation in a WESEO WordPress/WST project opened through Cursor Remote-SSH. The expected outcome is a usable SSH development workspace, not just a list of open questions.

Work in this order:

1. Discover what is already present.
2. Create or update `PROJECT-CONTEXT.md` with non-secret facts.
3. Configure only local machine or repository settings that are safe to apply.
4. Verify Git, Cursor guidance, WP-CLI, cache, temp policy, and optional MCP.
5. Ask the maintainer only for values that cannot be detected or safely generated.

Never ask the user to paste real tokens, application passwords, SSH keys, token-bearing URLs, private server coordinates, or credentials into tracked files, diagnostics, screenshots, shared chat, or commit messages.

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

## Step 4: Confirm Or Configure Local Git

Work from the WordPress root. If the Git repository already exists, use it. If no repository exists, do not `git init` unless the maintainer confirms the repository should be created in this WordPress root.

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
- If neither exists, record an open setup question and do not invent a command.

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

If `.cursor/` is ignored by Git, that is acceptable for existing SSH projects when the internal install flow expects local Cursor guidance. Record the local-only status in Project Context or setup notes. Do not copy private setup notes, real access values, database dumps, or operational scratch files into tracked plugin or template content.

## Step 8: Create Local-Only MCP Config If Needed

Create `.cursor/mcp.json` only as a local developer file. It must stay untracked.

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
- `PROJECT-CONTEXT.md` exists and contains non-secret setup coordinates.
- WP-CLI command is verified or recorded as an open setup question.
- Cache flush command is documented.
- Required Rules and Skills are installed or the install flow blocker is recorded.
- `.cursor/mcp.json`, if present, is local-only and untracked.
- Operational scratch policy is recorded and uses an outside-webroot path.

## Outputs

When setup orientation is complete, leave behind:

- A usable Remote-SSH Cursor workspace opened at the WordPress root.
- `PROJECT-CONTEXT.md` filled with detected non-secret facts and any remaining open questions.
- Working local Git access through the approved project method.
- Verified WP-CLI command and documented cache flush command.
- Verified Cursor Rules and Skills installation.
- A safe temp/scratch path outside the public webroot.
- Local-only MCP config only when needed, never tracked.

If a setup item cannot be completed automatically, record it as an explicit open question with the detected evidence and the exact next action needed.

## Stop Conditions

Stop and ask before:

- Creating a new Git repository or replacing an existing remote.
- Running commands that change live site behavior, except low-risk setup verification.
- Storing or displaying a credential.
- Choosing between multiple WordPress roots or multiple repositories.
- Using a temp path that may be publicly served.

## Scope Boundaries

Do not change the resolved Setup And Access Policy. Do not migrate final visual CSS, Chrome Local Overrides spikes, responsive QA, Playwright checks, WST Builder setup, or Frontend Design QA setup in this Skill. Do not store or request real secrets in tracked content.
