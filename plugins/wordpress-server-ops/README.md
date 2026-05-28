# WordPress Server Ops Plugin

Reusable server-phase guidance for safe Cursor Remote-SSH work in WESEO WordPress and WST projects.

This plugin keeps WordPress-root work constrained and explicit. It covers the guided first-setup wizard, file boundaries, public webroot safety, WP-CLI/cache expectations, WordPress content editing, media imports, a guided cleanup for legacy Cursor Rules in projects that were initially set up from the older `weseo-smartflow-frontend-guide`, and a guided authoring wizard for new project-local Cursor Rules. Project-specific values live in the project-local `PROJECT-CONTEXT.md`; access values never do.

For the full WESEO WordPress/WST delivery workflow, install this plugin together with `wst-builder` and `frontend-design-qa`. WordPress Server Ops owns the server/setup phase: Remote-SSH orientation, Project Context, server-safety boundaries, WP-CLI/cache guidance, media import, content editing, and the frontend onboarding handoff that prepares later WST Builder and Frontend Design QA work.

PHP bootstrap files are sensitive across the full workflow. `functions.php` is forbidden for agent edits. `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change.

## Responsibility

Use this plugin for server-side work:

- WordPress root orientation and Remote-SSH safety.
- Guided first-run setup for Remote-SSH WordPress roots: plain-language German explanations, concrete user actions, root detection, `PROJECT-CONTEXT.md` creation as required contract, Section/CPT handoff storage, `LEARNINGS.md` status, WST stack verification, local Bitbucket Git access, restrictive `.gitignore` with tracked `.cursor` skeleton, WP-CLI/cache verification and execution, co-installed workflow skill verification, untracked `.cursor/mcp.json` setup for WordPress and Figma, safe temp policy.
- WST template and server-side PHP context.
- ACF field and content updates.
- WP-CLI commands and cache flushes.
- Media files that must be registered in the WordPress Media Library.
- Handoff notes for later WST builder and local frontend implementation.

Do not use this plugin as the source of truth for final visual implementation. Final CSS/SCSS work, Chrome Local Overrides spikes, responsive checks, local Playwright MCP browser QA, and optional project-local Playwright regression acceptance belong to the local frontend phase via the `frontend-design-qa` plugin. Playwright MCP itself is configured only in the developer's local Cursor workspace through `frontend-design-qa` `setup-playwright-mcp`, never inside the Remote-SSH workspace.

## Project Context Contract

`setup-orientation` creates and maintains `PROJECT-CONTEXT.md` in the WordPress root as the project's non-secret context contract. Later WordPress, WST, and Frontend Skills must read it first and update it when new non-secret facts are confirmed.

The final context should contain:

- Project name, environment URLs, server hostname, WordPress root.
- Theme path and WST template path.
- Project-configured Section handoff storage and CPT handoff storage.
- `LEARNINGS.md` status (`exists`, `create when first learning appears`, or `pending: <reason>`).
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
- Rule: `plugin-package-boundary`
- Rule: `wp-cli-cache`
- Rule: `wordpress-content-editing`
- Skill: `setup-orientation` (with `reference.md` and `frontend-onboarding.md`)
- Skill: `wp-media-import`
- Skill: `project-rules-cleanup`
- Skill: `project-rule-authoring`

## Setup Orientation

`setup-orientation` is a guided German wizard for the first setup of a WESEO WordPress/WST project over Cursor Remote-SSH. It leads with plain-language explanations, then gives the concrete action the user must take. Technical labels such as `Remote-SSH`, `PROJECT-CONTEXT.md`, `WP-CLI`, `Settings -> Tools & MCP`, and `.cursor/mcp.json` remain visible so users can find the real tools, but the wizard explains their purpose before relying on the terms.

It works from any starting state: if Cursor has no Remote-SSH connection or the WordPress root is not open yet, the wizard leads the user through `Remote-SSH: Connect to Host` and `Open Folder` first; if those are already in place, it continues with discovery and setup.

The wizard:

- Detects the WordPress root, project facts, and the WST stack (Astra Child Theme, WST plugin, ACF PRO, ACF Extended, WP Grid Builder, CPT UI).
- Creates or updates `PROJECT-CONTEXT.md` and treats it as the required context contract for later Skills, including Section/CPT handoff storage and `LEARNINGS.md` status.
- Configures Bitbucket Git for this project through a hidden terminal prompt with `x-token-auth`, with detailed step-by-step terminal instructions for users who are not familiar with the terminal. Verifies access with `git fetch origin`; never runs a blind `git pull origin master`.
- Installs a deny-all WordPress-root `.gitignore` allowing only setup files, the `.cursor` skeleton (`.cursor/rules/.gitkeep`, `.cursor/skills/.gitkeep`), and detected child themes by default. Project-owned plugins (including `weseo-smart-template-builder`) are allowed only after explicit confirmation.
- Verifies WP-CLI and runs the documented cache flush as part of setup once WP-CLI and the WordPress root are confirmed.
- Verifies that the personal Cursor plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`) and required workflow skills (`grill-me`, `frontend-section-qa`, `cpt-frontend-qa`) are loaded for the Remote-SSH workspace and falls back to manual projection only when needed.
- Configures untracked `.cursor/mcp.json` in the opened Cursor workspace for both WordPress MCP and Figma MCP as required setup gates, with explicit `pending: <reason>` allowed.
- Records a safe temp/scratch path outside the public webroot.
- Shows `frontend-onboarding.md` as the final handoff for users new to the three-plugin workflow, or records that the user already knows it or will read it later.
- Finishes with a non-technical summary first, then technical completion details and any remaining `pending` next actions.

Tracked examples use placeholders such as `<token>`, `<repo-host>`, `<repo-name>`, `<domain>`, `<user>`, `<app-password>`, `<figma-api-key>`, `<wp-root>`, and `<path-outside-webroot>`. Real tokens, application passwords, SSH keys, database dumps, and token-bearing URLs stay out of chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, screenshots, and public webroot artifacts.

## Project Rules Cleanup

`project-rules-cleanup` is a separate guided German wizard for projects that already exist and still carry an older `.cursor/rules` setup. Many of these projects were originally bootstrapped from `weseo-smartflow-frontend-guide`, which copied generic Rules and SmartFlow placeholders into the project repository. With the three plugins now installed, those Rules either duplicate plugin guidance or hold filled-in project values that should live in `PROJECT-CONTEXT.md` instead.

The wizard:

- Audits `.cursor/rules`, `.cursor/skills`, and `PROJECT-CONTEXT.md` first; it never edits or deletes anything before producing a bundled change plan.
- Classifies each Rule as `legacy-smartflow-generic`, `legacy-smartflow-project-values`, `project-specific`, `third-party-or-custom`, or `suspicious-or-unsafe`.
- Migrates clearly non-secret project values from legacy Rules into `PROJECT-CONTEXT.md`. Placeholders, conflicts, and possibly sensitive values are recorded as `pending: <reason> - nächster Schritt: <action>` and never moved automatically.
- Warns when more than two Rules use `alwaysApply: true` and recommends `globs` or Skill triggers for workflow-, role-, technology-, or file-type-specific guidance.
- Deletes only `legacy-smartflow-generic` Rules and `legacy-smartflow-project-values` Rules whose content has been fully migrated, and only after explicit confirmation. There is no automatic archive copy; deletion is opt-in per file.
- Audits `.cursor/skills` read-only. Local Skills that are now redundant with plugin Skills (for example legacy `wst-new-fc-section`, local `wst-new-post-type`, local `wp-media-import`) are flagged in the change plan but never deleted by this Skill.
- Records a short `Cursor Rules Cleanup Status` block in `PROJECT-CONTEXT.md` with date, checked files, applied changes, and any remaining `pending` points. There is no separate cleanup report file.

`project-rules-cleanup` is a separate Skill from `setup-orientation`. The setup wizard remains responsible for first-run setup and only recommends `project-rules-cleanup` as a follow-up when it detects non-skeleton `.cursor/rules/*.mdc` files. Real tokens, application passwords, SSH keys, dumps, and token-bearing URLs stay out of chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, screenshots, and public webroot artifacts during cleanup just as they do during setup.

## Project Rule Authoring

`project-rule-authoring` is a separate guided German wizard for projects that already finished setup and now want to add a new project-local Cursor Rule. It wraps the generic Cursor built-in `create-rule` flow with WESEO-specific decisions so that new `.cursor/rules/*.mdc` files do not duplicate plugin guidance or accumulate as `alwaysApply: true` clutter.

The wizard:

- Reads `.cursor/rules` and `PROJECT-CONTEXT.md` first; it never writes a Rule before producing a bundled change plan and getting explicit confirmation.
- Runs a short grill, one question at a time with a recommended answer, covering purpose, trigger, scope, carrier choice, redundancy with plugin guidance, secret risk, and size.
- Classifies the request into a carrier: `project-rule`, `project-context-value`, `project-skill`, `plugin-guidance-existing`, `plugin-guidance-gap`, or `suspicious-or-unsafe`. Only `project-rule` results in a new `.mdc`; other carriers route the content to `PROJECT-CONTEXT.md`, to a Skill proposal, or back to existing plugin guidance.
- Applies an `alwaysApply` hygiene threshold: with two or more existing `alwaysApply: true` Rules, new Rules must use `alwaysApply: false` with concrete `globs` or be proposed as a Skill instead.
- Validates the planned `.mdc` against a frontmatter and content checklist (`description`, `alwaysApply`, optional `globs`, body under 50 lines, no secrets, no project-context values, no duplicated plugin guidance) before writing.
- Records an optional `Cursor Rules Authoring Log` entry in `PROJECT-CONTEXT.md` so later agents see which project-specific Rules exist and why.

`project-rule-authoring` is independent from `setup-orientation` and `project-rules-cleanup`. Setup remains responsible for the `.cursor` skeleton and the Project Context contract; cleanup remains responsible for legacy Rule migration and hygiene; authoring is the gate every new Rule must pass. Real tokens, application passwords, SSH keys, dumps, and token-bearing URLs stay out of chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, screenshots, and public webroot artifacts during authoring just as they do during setup and cleanup.

## Package Boundary Guard Rail

Plugin consumers only have access to files inside the installed plugin package. Any required Skill, Rule, reference, checklist, script, or workflow contract named by this plugin must be bundled inside `plugins/wordpress-server-ops` in this development repository, or explicitly marked as optional and external.

`PROJECT-CONTEXT.md` is project-local and created or maintained in the target WordPress project. Do not reference a plugin-local Project Context template unless this package actually bundles one. Required commands, handoff paths, URLs, server roots, repository values, and access details belong in the project-local context or concrete handoffs, not reusable package prose.

Do not make required WordPress Server Ops workflows depend on development-only paths such as `.agents`, `.cursor/plugins/cache`, `weseo-smartflow-frontend-guide`, `.scratch`, or repository-local scripts.

## Server-To-Local Handoff

When server work creates or changes a WST Flexible Content Section or CPT foundation, the `wst-builder` plugin emits a handoff on the same Git branch or PR. Section handoffs use the bundled reusable template at `plugins/wst-builder/handoffs/section-handoff.template.md`; CPT handoffs use the bundled reusable template at `plugins/wst-builder/handoffs/cpt-handoff.template.md`. Filled Section and CPT handoffs live at the project-configured storage locations recorded in `PROJECT-CONTEXT.md`. The handoff names the page URL, template file, ACF references, CSS hooks, expected visual behavior, cache state, QA notes, and open risks before local frontend work begins through the `frontend-design-qa` plugin.

## Not Included

- WST Builder Skill content (lives in the `wst-builder` plugin).
- Frontend Design QA content (lives in the `frontend-design-qa` plugin).
- Playwright implementation details.
- Real project paths, site values, or private access values.
- A full local WordPress runtime.
