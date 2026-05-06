# Setup Orientation Reference

Supporting checks for the `setup-orientation` Skill. Keep real project values in Project Context or local developer storage. The Skill should complete all safe first-setup work automatically and leave only credential or policy decisions as explicit open questions.

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
| Git repository | The repo root is known. If missing, repository creation is explicitly approved or recorded as blocked. |
| Project Context | `PROJECT-CONTEXT.md` exists and `<server-hostname>`, `<wp-root>`, `<theme-path>`, `<wst-template-path>`, `<repo-name>`, `<branch-name>`, `<wp-cli-command>`, `<cache-flush-command>`, and `<path-outside-webroot>` are filled with non-secret values. |
| Git identity | `git config user.name` and `git config user.email` return maintainer-approved values for the local repository. |
| Repository access | `git fetch origin` succeeds using the approved local access method. |
| Secret handling | No real token, application password, SSH private key, complete token-bearing URL, or REST auth value is present in tracked docs. |
| Plugin content | Project-appropriate `.cursor/rules/`, `.cursor/skills/`, and release snapshot content are installed according to the internal release flow. |
| WP-CLI | `wp --info` or `php wp-cli.phar --info` succeeds, and the chosen command shape is documented. |
| Cache flush | The cache flush command is documented and only executed when approved. |
| Local MCP config | `.cursor/mcp.json`, if needed, exists only locally and is not tracked. |
| Scratch policy | Temporary scripts, dumps, and exports use `<path-outside-webroot>`, stay untracked, and are removed after use. |

## Open Question Format

When setup cannot complete automatically, record the blocker with evidence and next action:

```md
- Git repository missing: no `.git` found in `<wp-root>`. Maintainer must confirm whether to initialize here or clone/open another root.
- WP-CLI unavailable: neither `wp-cli.phar` nor global `wp` found. Maintainer must provide approved WP-CLI install method.
- Temp path unresolved: candidate path is under `public_html`; maintainer must confirm whether it is not publicly served or approve `$HOME/.weseo-tmp`.
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
