# WESEO Cursor Plugins (Public Distribution)

Public Cursor Team Marketplace distribution for WESEO plugins. Cursor reads this repository to install reusable Rules and Skills for WordPress, WST, server-side WordPress operations, and local frontend QA.

This repository is a **generated artifact**. It is produced from the private WESEO maintainer repository by `scripts/sync-public-distribution.py`. Direct edits to this repository will be overwritten on the next sync.

## Plugins

- `plugins/wordpress-server-ops` - server-phase guidance for safe Cursor Remote-SSH work in WordPress and WST projects.
- `plugins/wst-builder` - WST Flexible Content Section and Custom Post Type foundation workflows.
- `plugins/frontend-design-qa` - local frontend implementation guidance for CSS, Figma-to-code, and Playwright-oriented QA.

## How to install

This repository is intended to be imported as a Cursor Team Marketplace.

1. In Cursor, open `Dashboard -> Settings -> Plugins -> Team Marketplaces`.
2. Click `Import` and paste the URL of this repository.
3. Configure each plugin as Required or Optional for your team.

For the WESEO-internal release, smoke-test, and rollback flow, see the maintainer documentation in the private repository.

## Project context

The contents of `project-template/` are placeholders. Each WordPress/WST project copies that template into its own repository and fills in client-specific values there. No real client data, credentials, or server paths live in this repository.
