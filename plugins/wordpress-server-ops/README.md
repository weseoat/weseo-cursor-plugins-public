# WordPress Server Ops Plugin

Reusable server-phase guidance for safe Cursor Remote-SSH work in WESEO WordPress and WST projects.

This plugin keeps WordPress-root work constrained and explicit. It covers the guided first-setup wizard, file boundaries, public webroot safety, WP-CLI/cache expectations, WordPress content editing, and media imports. Project-specific values live in the project-local `PROJECT-CONTEXT.md`; access values never do.

## Responsibility

Use this plugin for server-side work:

- WordPress root orientation and Remote-SSH safety.
- Guided first-run setup for Remote-SSH WordPress roots: plain-language German explanations, concrete user actions, root detection, `PROJECT-CONTEXT.md` creation as required contract, WST stack verification, local Bitbucket Git access, restrictive `.gitignore` with tracked `.cursor` skeleton, WP-CLI/cache verification and execution, personal Cursor plugin verification, untracked `.cursor/mcp.json` setup for WordPress and Figma, safe temp policy.
- WST template and server-side PHP context.
- ACF field and content updates.
- WP-CLI commands and cache flushes.
- Media files that must be registered in the WordPress Media Library.
- Handoff notes for later WST builder and local frontend implementation.

Do not use this plugin as the source of truth for final visual implementation. Final CSS/SCSS work, Chrome Local Overrides spikes, responsive checks, and Playwright acceptance checks belong to the local frontend phase via the `frontend-design-qa` plugin.

## Project Context Contract

`setup-orientation` creates and maintains `PROJECT-CONTEXT.md` in the WordPress root as the project's non-secret context contract. Later WordPress, WST, and Frontend Skills must read it first and update it when new non-secret facts are confirmed.

The final context should contain:

- Project name, environment URLs, server hostname, WordPress root.
- Theme path and WST template path.
- Repository host/name, default/current branch, redacted access method.
- WP-CLI command, cache flush command.
- Editable path policy and any approved plugin exceptions.
- Approved temp path outside the public webroot.
- WST stack status (active theme + plugins).
- Project specifics carried over from the old SmartFlow setup: CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, ACF IDs, button variants, container widths, clamp values.
- Setup completion status per step (`done`, `pending: <reason>`, `skipped: <reason>` with next action).

`PROJECT-CONTEXT.md` never holds tokens, application passwords, SSH keys, token-bearing URLs, REST credentials, or database dumps. Access values must also stay out of chat, Git, commits, tracked files, diagnostics, screenshots, and public webroot artifacts.

## Included Content

- Rule: `server-phase-boundary`
- Rule: `webroot-safety`
- Rule: `file-edit-boundary`
- Rule: `wp-cli-cache`
- Rule: `wordpress-content-editing`
- Skill: `setup-orientation` (with `reference.md` and `frontend-onboarding.md`)
- Skill: `wp-media-import`

## Setup Orientation

`setup-orientation` is a guided German wizard for the first setup of a WESEO WordPress/WST project over Cursor Remote-SSH. It leads with plain-language explanations, then gives the concrete action the user must take. Technical labels such as `Remote-SSH`, `PROJECT-CONTEXT.md`, `WP-CLI`, `Settings -> Tools & MCP`, and `.cursor/mcp.json` remain visible so users can find the real tools, but the wizard explains their purpose before relying on the terms.

It works from any starting state: if Cursor has no Remote-SSH connection or the WordPress root is not open yet, the wizard leads the user through `Remote-SSH: Connect to Host` and `Open Folder` first; if those are already in place, it continues with discovery and setup.

The wizard:

- Detects the WordPress root, project facts, and the WST stack (Astra Child Theme, WST plugin, ACF PRO, ACF Extended, WP Grid Builder, CPT UI).
- Creates or updates `PROJECT-CONTEXT.md` and treats it as the required context contract for later Skills.
- Configures Bitbucket Git for this project through a hidden terminal prompt with `x-token-auth`, with detailed step-by-step terminal instructions for users who are not familiar with the terminal. Verifies access with `git fetch origin`; never runs a blind `git pull origin master`.
- Installs a deny-all WordPress-root `.gitignore` allowing only setup files, the `.cursor` skeleton (`.cursor/rules/.gitkeep`, `.cursor/skills/.gitkeep`), and detected child themes by default. Project-owned plugins (including `weseo-smart-template-builder`) are allowed only after explicit confirmation.
- Verifies WP-CLI and runs the documented cache flush as part of setup once WP-CLI and the WordPress root are confirmed.
- Verifies that the personal Cursor plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) are loaded for the Remote-SSH workspace and falls back to manual projection only when needed.
- Configures untracked `.cursor/mcp.json` in the opened Cursor workspace for both WordPress MCP and Figma MCP as required setup gates, with explicit `pending: <reason>` allowed.
- Records a safe temp/scratch path outside the public webroot.
- Shows `frontend-onboarding.md` as the final handoff for users new to the three-plugin workflow, or records that the user already knows it or will read it later.
- Finishes with a non-technical summary first, then technical completion details and any remaining `pending` next actions.

Tracked examples use placeholders such as `<token>`, `<repo-host>`, `<repo-name>`, `<domain>`, `<user>`, `<app-password>`, `<figma-api-key>`, `<wp-root>`, and `<path-outside-webroot>`. Real tokens, application passwords, SSH keys, database dumps, and token-bearing URLs stay out of chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, screenshots, and public webroot artifacts.

## Server-To-Local Handoff

When server work creates or changes a WST Flexible Content Section or CPT foundation, the `wst-builder` plugin emits a handoff on the same Git branch or PR. The handoff names the page URL, template file, ACF references, CSS hooks, expected visual behavior, and QA notes before local frontend work begins through the `frontend-design-qa` plugin.

## Not Included

- WST Builder Skill content (lives in the `wst-builder` plugin).
- Frontend Design QA content (lives in the `frontend-design-qa` plugin).
- Playwright implementation details.
- Real project paths, site values, or private access values.
- A full local WordPress runtime.
