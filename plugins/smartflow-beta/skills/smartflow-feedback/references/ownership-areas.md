# Ownership Areas

Use this bundled mapping during feedback intake. Each bundled Rule, Skill and Agent belongs to exactly one area; an entry may affect several areas. Area names below are the German Confluence values; labels use `sf-bereich-<slug>`.

| Slug | Bereich | Rules | Skills | Agents |
| --- | --- | --- | --- | --- |
| `setup-deploy` | Setup & Deploy/Server | `deploy-and-branches`, `status-bridge`, `secrets`, `webroot-safety`, `file-edit-boundary`, `commit-trailer` | `setup-local-project`, `setup-ticket-ready`, `migrate-ssh-to-local`, `install-status-bridge`, `project-css-setup` | — |
| `wst-acf` | WST & ACF | `acf-local-json`, `wst-php-authoring-route`, `wst-conditional-nesting`, `wordpress-taxonomies` | `wst-section-workflow`, `wst-new-post-type`, `wst-shortcodes`, `wst-nested-shortcodes`, `smarttags`, `grid-cards`, `setup-acf-local-json` | `wst-shortcode-implementer`, `wpgb-specialist`, `cpt-codebase-analyst` |
| `frontend-qa` | Frontend & Visual QA | `css-guideline`, `figma-to-code`, `frontend-section-qa-layout-preflight`, `frontend-section-qa-tablet-band`, `playwright-browser-claim`, `wordpress-brand-tokens` | `frontend-section-qa`, `cpt-frontend-qa`, `section-preview-harness` | `cpt-visual-implementer`, `cpt-figma-analyst` |
| `content-media` | Content & Media | `wordpress-content-editing` | `content`, `wp-media-import`, `headline-filling`, `figma-page-builder` | — |
| `jira` | Jira-Workflows | — | `jira-ticket-workflow`, `jira-batch-workflow` | `jira-ticket-runner` |
| `docs-feedback` | Confluence, Docs & Feedback | `confluence-source`, `feedback-capture` | `auto-docs`, `cpt-docs`, `sync-project-brief`, `smartflow-feedback` | — |
| `core` | Core | `agent-routing`, `plugin-package-boundary`, `jira-comment-signature`, `runner-gate`, `prefer-generic-over-custom` | `grill-me`, `code-cleanup` | — |

The Core assignment for `jira-comment-signature`, `runner-gate`, `prefer-generic-over-custom` and the beta-only `code-cleanup` is interim. Core is not a fallback for unknown names: no match means `Bereich: <ergänzt die Kuration>` and no area label. For proposals changing existing artifacts, use those artifacts' areas. For new artifacts, assign an area only when the submitter's answer or related existing artifacts make it unambiguous; otherwise leave it to curation.

Owner names are read live from the ownership page in the Skill's hub-page table, never stored here. The maintainer updates this reference together with the development repository's source of truth, `docs/ownership-split.md`, and checks coverage against the bundled `rules/`, `skills/` and `agents/` inventories. Consumers need only this reference and the live ownership page, not that development document.
