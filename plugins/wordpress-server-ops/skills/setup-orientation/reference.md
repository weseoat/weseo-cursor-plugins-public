# Setup Orientation Reference

Supporting checks for the `setup-orientation` Skill. Keep real project values in Project Context or local developer storage.

## Verification Checklist

| Area | Check |
|---|---|
| Cursor Remote-SSH | Cursor is connected to the expected project host alias or approved connection target. |
| Workspace root | The opened folder matches `<wp-root>` and contains `wp-content/`, `wp-admin/`, and project-approved source paths. |
| Project Context | `<server-hostname>`, `<wp-root>`, `<theme-path>`, `<wst-template-path>`, `<repo-name>`, `<branch-name>`, `<wp-cli-command>`, and `<cache-flush-command>` are filled with non-secret values. |
| Git identity | `git config user.name` and `git config user.email` return maintainer-approved values for the local repository. |
| Repository access | `git fetch origin` succeeds using the approved local access method. |
| Secret handling | No real token, application password, SSH private key, complete token-bearing URL, or REST auth value is present in tracked docs. |
| Plugin content | Project-appropriate `.cursor/rules/`, `.cursor/skills/`, and release snapshot content are installed according to the internal release flow. |
| Local MCP config | `.cursor/mcp.json`, if needed, exists only locally and is not tracked. |
| Scratch policy | Temporary scripts, dumps, and exports use `<path-outside-webroot>`, stay untracked, and are removed after use. |

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
