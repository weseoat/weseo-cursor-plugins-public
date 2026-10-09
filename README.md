# WESEO Cursor Plugins (Public Distribution)

Public Cursor Team Marketplace distribution for WESEO plugins. Cursor reads this repository to install reusable Rules and Skills for WordPress, WST, server-side WordPress operations, and local frontend QA.

This repository is a **generated artifact**. It is produced from the private WESEO maintainer repository by `scripts/sync-public-distribution.py`. Direct edits to this repository will be overwritten on the next sync.

## Plugins

- `plugins/smartflow` - stable 1.7.0: the complete WordPress/WST workflow with 24 Rules, 26 Skills, and 6 Agents.
- `plugins/smartflow-beta` - test channel 1.8.0-beta.1: the stable workflow plus the `code-cleanup` Skill (27 Skills). Cleanup and its intake/comment policy remain in testing and are excluded from production.
- `plugins/wordpress-server-ops`, `plugins/wst-builder`, and `plugins/frontend-design-qa` are frozen legacy packages. SmartFlow replaces these packages and their former beta twins.

Install either SmartFlow stable or beta, never both, and remove the six legacy packages when switching. Native Claude Code and Codex packages are distributed through the private team repository; this public repository remains the Cursor distribution endpoint.

## How to install

This repository is intended to be imported as a Cursor Team Marketplace.

1. In Cursor, open `Dashboard -> Settings -> Plugins -> Team Marketplaces`.
2. Click `Import` and paste the URL of this repository.
3. Configure each plugin as Required or Optional for your team.

For the WESEO-internal release, smoke-test, and rollback flow, see the maintainer documentation in the private repository.

## Project context

The contents of `project-template/` are placeholders. Each WordPress/WST project copies that template into its own repository and fills in client-specific values there. No real client data, credentials, or server paths live in this repository.
