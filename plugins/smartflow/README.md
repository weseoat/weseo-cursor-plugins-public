# SmartFlow

Single WESEO Cursor plugin for the complete WordPress/WST workflow: server-side WST Section and Custom Post Type foundations, local frontend CSS/SCSS implementation, Playwright QA, and the project documentation layer.

SmartFlow is the successor to the six legacy packages `wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, and their `-beta` twins, which are frozen (see `docs/beta-channel.md`).

## Hard Switch

Installing `smartflow` (or `smartflow-beta`) replaces the complete legacy set:

- Uninstall all six legacy packages first. Running SmartFlow next to any legacy package produces duplicate and partly contradictory rules and skills.
- Within the pair, the usual collision rule applies: run `smartflow` or `smartflow-beta`, never both.

## Status

Stable `smartflow` 1.7.0 contains 24 Rules, 26 Skills, and 6 Agents for Cursor, Claude Code, and Codex. Cursor packages are published through the public distribution repository; the native Claude Code and Codex marketplaces remain in the private maintainer repository. The generated targets and installer share one authored source.

`smartflow-beta` 1.8.0-beta.1 adds the `code-cleanup` Skill (27 Skills total). The entire cleanup workflow, including structured intake and comment policy, remains in the test phase and is excluded from production 1.7.0. Slash entry points are Skills; the Commands folder remains empty.

## Package Layout

The package includes the theme-scoped `prefer-generic-over-custom` Rule: reuse existing elements, styles, and tokens while preserving the requested design. New project-owned `wso-` class names follow the centralized `css-guideline` convention; existing hooks remain stable.

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

Edit in-flight changes only in `plugins/smartflow-beta/`; the production folder changes through promotion. For a selective release that retains beta-only work, use an isolated staging copy as documented in `docs/beta-channel.md`: the commands above replace the beta tree and must not run against the active cleanup beta unchanged. Run exactly one channel in each workspace.
