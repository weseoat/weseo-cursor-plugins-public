# Project Context: <project-name>

This file is the project-local source of truth for site-specific SmartFlow facts. Keep reusable workflow guidance in internal Cursor Plugins and record only this project's values here.

## Project Overview

| Field | Value |
|-------|-------|
| Project name | `<project-name>` |
| Client / brand | `<client-name>` |
| Live URL | `<live-url>` |
| Staging / dev URL | `<staging-url>` |
| WordPress root | `<wp-root>` |
| Theme path | `wp-content/themes/astra-child/` |
| WST template path | `wp-content/plugins/weseo-smart-template-builder/` |
| Repository | `<repo-name>` |
| Default branch | `<branch-name>` |
| Deployment path | `<deployment-method>` |

## Environment

| Field | Value |
|-------|-------|
| Server hostname | `<server-hostname>` |
| PHP version | `<php-version>` |
| Database | `<database-type-and-version>` |
| WP-CLI command | `cd <wp-root> && php wp-cli.phar <command>` |
| Cache flush command | `cd <wp-root> && php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"` |

Do not store passwords, tokens, SSH keys, or application passwords here.

## Setup And Access Policy

Record only non-secret coordinates and the approved local methods. Real tokens, passwords, SSH keys, application passwords, and credential-bearing URLs stay outside tracked files.

| Field | Value | Notes |
|-------|-------|-------|
| Repository access method | `<token-in-remote-url-or-credential-helper-or-ssh>` | Document the approved local method. Real token-bearing URLs are never tracked. |
| Local credential storage | `<password-manager-or-os-keychain>` | Where the developer keeps tokens, application passwords, SSH passphrases. |
| MCP configuration policy | Local-only `.cursor/mcp.json`, never tracked | Tracked docs may include redacted examples only. |
| Approved temp path outside webroot | `<path-outside-webroot>` | Used for temporary scripts, dumps, exports, scratch files. |
| Database dump policy | Outside webroot, untracked, removed after use | Never store dumps in the WordPress root or in a tracked repo path. |

## Confluence Project Source

The anchored PL page is the leading input source per the `confluence-source` Rule; this block is the local mirror. It is written at setup, refreshed only through the `sync-project-brief` Skill (diff shown, user confirms), and read fresh by the work-package Skills at run start. Never mirror secrets here — placeholders plus pointer only. If the project has no Confluence page, record `confluence_anchor: none` and remove the mirror rows.

| Field | Value |
|-------|-------|
| Page ID | `<confluence-page-id>` |
| Page URL | `<confluence-page-url>` |
| Last mirror | `<iso-timestamp>` |
| Anchor gate | `<mirrored-or-none-or-pending-reason>` |

### Mirrored Extract

Distilled facts from the anchored page. Mark every gap the page does not answer as `<unresolved: ...>`.

| Fact | Value | Source note |
|------|-------|-------------|
| Dev URL / final domain | `<value>` | `<page-section-or-fuer-cursor-block>` |
| Main Figma link | `<value>` | `<source-note>` |
| Jira parent task | `<value>` | `<source-note>` |
| Languages / accessibility | `<value>` | `<source-note>` |
| Interfaces / content sources | `<value>` | `<source-note>` |
| Modules / sections | `<value-or-list>` | `<source-note>` |
| Constraints / out of scope | `<value>` | `<source-note>` |

## Theme Stack

| Area | Value |
|------|-------|
| Parent theme | Astra |
| Child theme | Astra Child |
| Template system | WESEO Smart Template Builder (WST) |
| CSS framework | Bootstrap `<bootstrap-version>` |
| ACF | ACF PRO + ACF Extended |
| Grid/card engine | WP Grid Builder |

## Custom Post Types

CPTUI name is the registered post type used in DB queries, ACF locations, and WST template paths. URL slug is the public rewrite slug. Use `-` when the CPT has no public rewrite.

| CPTUI name | URL slug | Label | Has detail page | Taxonomy | WPGB grid/card | Notes |
|------------|----------|-------|-----------------|----------|----------------|-------|
| `<cpt-name>` | `<url-slug>` | `<label>` | `<yes-or-no>` | `<taxonomy-name>` | `<grid-id>/<card-id>` | `<notes>` |

## Key Pages And Templates

| Page / template | ID | Slug | Language | Purpose / notes |
|-----------------|----|------|----------|-----------------|
| Homepage | `<page-id>` | `<slug>` | `<language>` | `<notes>` |
| ACF options page | `<options-page-id>` | `<slug>` | `<language>` | `<notes>` |
| Footer template | `<footer-template-id>` | `<slug>` | `<language>` | `<notes>` |

## Flexible Content Layouts

Record all layouts registered in `flexible-content.php`. Add project-specific layout keys and template notes as they are discovered.

| Layout name | Section template | ACF group/key | Category | Notes |
|-------------|------------------|---------------|----------|-------|
| `<layout-name>` | `sections/<section-template>.php` | `<group-key-or-field-key>` | `<category>` | `<notes>` |

## ACF References

| Reference | Value | Notes |
|-----------|-------|-------|
| Flexible Content field key | `<fc-field-key>` | `<notes>` |
| Flexible Content field post ID | `<fc-post-id>` | `<notes>` |
| Flexible Content group key | `<fc-group-key>` | `<notes>` |
| Inhalt clone group key | `<inhalt-group-key>` | `<notes>` |
| Button clone group key | `<button-group-key>` | `<notes>` |
| Layout clone group key | `<layout-group-key>` | `<notes>` |

## WP Grid Builder

| Grid ID | Card ID | Post type / source | Used in | Notes |
|---------|---------|--------------------|---------|-------|
| `<grid-id>` | `<card-id>` | `<post-type-or-source>` | `<section-or-page>` | `<notes>` |

## Theme Tokens

### Colors

| Token | Value | Usage |
|-------|-------|-------|
| `<theme-token>` | `<hex-or-css-value>` | `<usage>` |

### Fonts

| Token | Value | Usage |
|-------|-------|-------|
| `<font-token>` | `<font-family>` | `<usage>` |

### Container Widths

| Breakpoint | Width | Notes |
|------------|-------|-------|
| `<breakpoint>` | `<width>` | `<notes>` |

### Button Variants

| Variant | Class / token | Shape | Background | Notes |
|---------|---------------|-------|------------|-------|
| `<variant-name>` | `<class-or-token>` | `<shape>` | `<token-or-value>` | `<notes>` |

### Typography And Clamp Values

| Text style | Token / selector | Value | Notes |
|------------|------------------|-------|-------|
| `<text-style>` | `<token-or-selector>` | `<clamp-or-size>` | `<notes>` |

## File Boundaries

| Path | Policy | Notes |
|------|--------|-------|
| `wp-content/themes/astra-child/` | Editable | Main project code area. |
| `wp-content/plugins/weseo-smart-template-builder/` | `<editable-or-read-only>` | Record project-specific exception details. |
| `wp-content/uploads/` | `<policy>` | Usually media import only. |
| `<other-path>` | `<policy>` | `<notes>` |

## Project-Specific Workflow Notes

- `<note-about-git-or-deployment>`
- `<note-about-cache-or-wp-cli>`
- `<note-about-local-css-or-playwright>`

## Open Questions

- `<question-that-needs-maintainer-or-client-answer>`
