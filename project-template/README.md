# WESEO WordPress Project Template

Copy this directory into each WordPress/WST project to hold site-specific SmartFlow context.

Internal Cursor Plugins should stay reusable. They define shared workflows, safety rules, WST patterns, and QA guidance. This project template records the facts that are different for every client site: URLs, server paths, CPTs, page IDs, ACF keys, WP Grid Builder IDs, design tokens, and project learnings.

## Files

```text
project-template/
  README.md
  PROJECT-CONTEXT.md
  LEARNINGS.md
  SOURCE-MATERIAL-MAPPING.md
```

## Placeholder Convention

Use bracketed placeholders until real project values are known:

- `<project-name>`
- `<live-url>`
- `<staging-url>`
- `<server-hostname>`
- `<wp-root>`
- `<repo-name>`
- `<cpt-name>`
- `<page-id>`
- `<layout-name>`
- `<grid-id>`
- `<card-id>`
- `<field-key>`
- `<group-key>`
- `<theme-token>`

Do not put credentials, access tokens, application passwords, SSH private keys, or database dumps in these files.

## What Belongs Here

Put facts here when they answer "what is true for this project?"

- Client/site identity, environment URLs, repository name, server path, and deployment notes.
- CPT names, URL slugs, taxonomies, detail-page decisions, and content model notes.
- Key page IDs, option page IDs, footer/template IDs, and important slugs.
- Flexible Content layout names, section template paths, ACF field keys, clone group keys, and handoff-relevant quirks.
- WP Grid Builder grid/card IDs and how they map to CPTs or sections.
- Theme tokens: colors, fonts, container widths, button variants, clamp values, and design-specific naming.
- Project learnings discovered while implementing or debugging this site.

## What Belongs In Shared Plugins

Put guidance in an internal plugin when it answers "how does the team usually do this?"

- WST section and CPT creation workflows.
- WordPress webroot safety, file edit boundaries, WP-CLI/cache expectations, and media import procedure.
- CSS conventions, Figma-to-code process, Chrome Local Overrides policy, and Playwright verification approach.
- Reusable Cursor Rules and Skills that should work across many WESEO WordPress/WST projects.

If a rule needs both, keep the workflow in the plugin and store the project values it consumes in `PROJECT-CONTEXT.md`.
