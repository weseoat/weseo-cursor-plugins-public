# SmartFlow

Single WESEO Cursor plugin for the complete WordPress/WST workflow: server-side WST Section and Custom Post Type foundations, local frontend CSS/SCSS implementation, Playwright QA, and the project documentation layer.

SmartFlow is the successor to the six legacy packages `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, and their `-beta` twins, which are frozen (see `docs/beta-channel.md`).

## Hard Switch

Installing `smartflow` (or `smartflow-beta`) replaces the complete legacy set:

- Uninstall all six legacy packages first. Running SmartFlow next to any legacy package produces duplicate and partly contradictory rules and skills.
- Within the pair, the usual collision rule applies: run `smartflow` or `smartflow-beta`, never both.

## Status

Scaffold. The package structure, manifest, and beta twin exist; the migrated Rules, Skills, the five Agents, and the Commands land in the wave 1 and wave 2 migrations of the SmartFlow plugin rework plan.

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
