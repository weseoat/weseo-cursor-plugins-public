# SmartFlow

Single WESEO Cursor plugin for the complete WordPress/WST workflow: server-side WST Section and Custom Post Type foundations, local frontend CSS/SCSS implementation, Playwright QA, and the project documentation layer.

SmartFlow is the successor to the six legacy packages `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, and their `-beta` twins, which are frozen (see `docs/beta-channel.md`).

## Hard Switch

Installing `smartflow` (or `smartflow-beta`) replaces the complete legacy set:

- Uninstall all six legacy packages first. Running SmartFlow next to any legacy package produces duplicate and partly contradictory rules and skills.
- Within the pair, the usual collision rule applies: run `smartflow` or `smartflow-beta`, never both.

## Status

Wave 1 in progress. The package structure, manifest, beta twin, status bridge (`status-bridge` Rule plus `install-status-bridge` Skill), the migrated rules layer, the five package Agents (with orchestrator/handoff practices in `agent-routing`), the setup and migration Skills (`setup-local-project` — including the absorbed legacy `setup-wordpress-cursor` REST exposure and options endpoints — and `migrate-ssh-to-local`), the WST workflow Skills (`wst-section-workflow`, `wst-new-post-type`, `section-preview-harness`, `wst-shortcodes` with the bundled `SMART-TEMPLATE-HILFE.md` catalog snapshot), the topic Skills from the cursor.zip deltas (`grid-cards`, `wst-nested-shortcodes`, `smarttags`, `headline-filling` — the 159 per-shortcode catalog skills from the zip are deliberately discarded), the frontend QA Skills (`frontend-section-qa`, `cpt-frontend-qa` with injection-proof as the main mode and the one-time bridge-verified served check, plus the REST-based `wp-media-import`), the `content` Skill for REST content edits (from the zip `/content` command), and the documentation layer (`auto-docs`, `cpt-docs` — the docs/ work records absorb the legacy handoffs, including the Visual QA Targets matrix) exist. Slash entry points are the Skills; the Commands folder stays empty by decision. Wave 2 (`figma-page-builder`, `jira-ticket-workflow`, `jira-batch-workflow`) follows once the wave-1 routing targets are stable.

## Package Layout

```text
plugins/smartflow/
  manifest.json            # WESEO plugin manifest (rules, skills, agents, commands)
  .cursor-plugin/
    plugin.json            # Cursor plugin manifest
  rules/                   # Cursor Rules
  skills/                  # Cursor Skills (one folder per Skill with SKILL.md)
  agents/                  # Agent definitions (one .md per agent)
  commands/                # Command definitions (one .md per command)
  release-notes/           # One markdown file per released version
```

## Beta Channel

`smartflow-beta` is the permanent beta twin, managed with `scripts/manage-beta.py` exactly like the legacy twins:

```sh
python scripts/manage-beta.py refresh smartflow
python scripts/manage-beta.py promote smartflow --release <version>
```

Edit in-flight changes only in `plugins/smartflow-beta/`; the production folder changes only through `promote`. The stable half is initially not added to the frontend Team Access group in the Cursor dashboard; testers work with `smartflow-beta` until the migration is validated.
