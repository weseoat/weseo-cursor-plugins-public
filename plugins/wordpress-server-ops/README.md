# WordPress Server Ops Plugin

Reusable server-phase guidance for safe Cursor Remote-SSH work in WESEO WordPress and WST projects.

This plugin keeps WordPress-root work constrained and explicit. It covers setup orientation, file boundaries, public webroot safety, WP-CLI/cache expectations, WordPress content editing, and media imports. Values that differ per project belong in `project-template/PROJECT-CONTEXT.md`.

## Responsibility

Use this plugin for server-side work:

- WordPress root orientation and Remote-SSH safety.
- First-run setup orientation for opening the WordPress root, local Git access, Project Context placeholders, and local-only MCP config.
- WST template and server-side PHP context.
- ACF field and content updates.
- WP-CLI commands and cache flushes.
- Media files that must be registered in the WordPress Media Library.
- Handoff notes for later local CSS/Playwright implementation.

Do not use this plugin as the source of truth for final visual implementation. Final CSS/SCSS work, Chrome Local Overrides spikes, responsive checks, and Playwright acceptance checks belong to the local frontend phase and should consume the Section handoff document.

## Project Context Required

Before applying these Rules or Skills, fill the project-local context with:

- WordPress root and theme path.
- WP-CLI command.
- Cache flush command.
- Editable path policy and any approved plugin exceptions.
- Upload directory policy.
- Environment URLs used for verification.
- Section handoff location for server-to-local work.
- Approved repository access method, local credential storage, and setup scratch path outside the public webroot.

## Included Content

- Rule: `server-phase-boundary`
- Rule: `webroot-safety`
- Rule: `file-edit-boundary`
- Rule: `wp-cli-cache`
- Rule: `wordpress-content-editing`
- Skill: `setup-orientation`
- Skill: `wp-media-import`

## Setup Orientation

Use `setup-orientation` when a developer needs first-run orientation in a WESEO WordPress/WST project over Cursor Remote-SSH. The Skill covers opening the project-approved WordPress root, confirming local Git identity, configuring repository access through the approved local method, installing or verifying internal plugin content, filling non-secret Project Context placeholders, creating local-only `.cursor/mcp.json` when needed, and keeping dumps or scratch files outside the public webroot.

Tracked examples use placeholders such as `<token>`, `<repo-host>`, `<repo-name>`, `<domain>`, `<user>`, `<app-password>`, and `<figma-api-key>`. Real tokens, application passwords, SSH keys, database dumps, and token-bearing URLs stay local-only and untracked.

## Server-To-Local Handoff

When server work creates or changes a WST Flexible Content Section, update a Section handoff on the same Git branch or PR. The handoff should name the page URL, template file, ACF references, CSS hooks, expected visual behavior, and QA notes before local frontend work begins.

## Not Included

- WST Builder Skill migration.
- Frontend Design QA Plugin.
- Playwright implementation details.
- Real project paths, site values, or private access values.
- A full local WordPress runtime.
