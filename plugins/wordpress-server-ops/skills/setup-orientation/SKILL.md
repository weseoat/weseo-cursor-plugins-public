---
name: setup-orientation
description: Use when orienting a developer in a WESEO WordPress/WST project over Cursor Remote-SSH, opening the WordPress root, configuring local Git access, installing plugin content, filling Project Context, or creating local-only MCP config.
---

# Setup Orientation

Use this Skill for first-run setup or re-orientation in a WESEO WordPress/WST project opened through Cursor Remote-SSH.

## Inputs

Read these values from Project Context or the maintainer before taking action:

- Server hostname: `<server-hostname>`
- WordPress root: `<wp-root>`
- Theme path: `<theme-path>`
- WST template path: `<wst-template-path>`
- Repository name and default branch: `<repo-name>`, `<branch-name>`
- Approved repository access method: `<token-in-remote-url-or-credential-helper-or-ssh>`
- Local credential storage: `<password-manager-or-os-keychain>`
- WP-CLI command shape: `<wp-cli-command>`
- Cache flush command shape: `<cache-flush-command>`
- Approved temp path outside the public webroot: `<path-outside-webroot>`

Never ask the user to paste real tokens, application passwords, SSH keys, token-bearing URLs, or private server coordinates into tracked files, diagnostics, screenshots, shared chat, or commit messages.

## Step 1: Connect And Open The WordPress Root

Use Cursor Remote-SSH with the project-provided host alias or connection details. Treat the SSH config as local machine setup, not tracked project content.

Open the project-approved WordPress root, then verify that the workspace looks like a WordPress installation:

```sh
pwd
ls
```

Expected signs include `wp-content/`, `wp-admin/`, and the project theme or WST paths from Project Context.

## Step 2: Confirm Local Git Identity

Check the repository-local Git identity in the WordPress root:

```sh
cd <wp-root>
git config user.name
git config user.email
```

If either value is missing, configure the local repository identity with maintainer-approved values:

```sh
git config user.name "<developer-name>"
git config user.email "<developer-email>"
```

Do not change global Git config unless the maintainer explicitly asks.

## Step 3: Configure Repository Access Locally

Use the approved repository access method from Project Context.

If the approved method is a token-bearing remote URL, keep it local-only and use placeholders in tracked docs:

```sh
git remote set-url origin https://x-token-auth:<token>@<repo-host>/<repo-name>.git
```

Real token-bearing URLs must never be committed, copied into tracked notes, included in diagnostics, captured in screenshots, shared in chat, or used in commit messages. Store the real token only in the approved local credential storage.

Verify repository access without exposing secrets:

```sh
git remote -v
git fetch origin
```

When showing `git remote -v` output in notes or chat, redact any secret-bearing URL first.

## Step 4: Install Or Update Project Guidance Content

Install the released internal plugin content according to the project-approved release and install flow. The expected result is that project-appropriate Rules and Skills are available to Cursor, while project-specific values stay in Project Context.

Verify the installed guidance shape without assuming one repository layout:

```sh
ls .cursor
ls .cursor/rules
ls .cursor/skills
```

If the project uses a release snapshot directory, verify it as documented by the internal release flow. Do not copy private setup notes, real access values, database dumps, or operational scratch files into tracked plugin or template content.

## Step 5: Fill Project Context Placeholders

Open Project Context and fill only non-secret project coordinates:

- Server hostname, WordPress root, theme path, and WST template path.
- Repository name, default branch, approved access method, and local storage method.
- WP-CLI and cache flush command shapes.
- Approved temporary path outside the public webroot.
- Environment URLs and editable path policy.

Do not store real tokens, application passwords, SSH private keys, database dumps, complete token-bearing URLs, or REST credentials in Project Context.

## Step 6: Create Local-Only MCP Config If Needed

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

## Step 7: Operational Scratch And Dump Policy

Database dumps, exports, temporary PHP scripts, and setup scratch files must:

- Live under the approved temp path outside the public webroot.
- Stay untracked.
- Be removed as soon as the task is complete.
- Have only the policy and approved placeholder path recorded in Project Context.

Never place these files inside the WordPress root, plugin packages, project template, or any public web path.

## Verification

Use `reference.md` for the setup verification checklist. At minimum, confirm:

- Cursor is connected to the expected Remote-SSH workspace.
- The opened folder is the WordPress root from Project Context.
- Git identity and repository access work locally without exposing secrets.
- Required Rules and Skills are installed through the approved release flow.
- Project Context contains non-secret coordinates and no secrets.
- `.cursor/mcp.json`, if present, is local-only and untracked.
- Operational scratch policy is recorded and uses an outside-webroot path.

## Outputs

When setup orientation is complete:

- Project Context contains the non-secret setup coordinates needed for WordPress Server Ops work.
- Local Git access works through the approved project method.
- Reusable plugin content is installed or verified.
- Local-only MCP config exists only when needed and remains untracked.
- Any unresolved access or setup question is recorded as an open question in Project Context.

## Scope Boundaries

Do not change the resolved Setup And Access Policy. Do not migrate final visual CSS, Chrome Local Overrides spikes, responsive QA, Playwright checks, WST Builder setup, or Frontend Design QA setup in this Skill. Do not store or request real secrets in tracked content.
