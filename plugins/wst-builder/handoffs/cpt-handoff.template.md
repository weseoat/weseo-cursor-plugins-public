# CPT Workflow Handoff Draft: <cpt-name>

Create one new CPT handoff per CPT task. Name the file `<cpt-slug>-<work-type>-handoff.md` and store it at the project-configured CPT handoff storage location from Project Context. Replace placeholders progressively while discovery, decisions, writes, and verification happen. If a blocking value is unknown, keep an explicit `<unresolved: ...>` placeholder instead of inventing a technical value.

This handoff is the live contract between server-side WST Builder work (over Remote-SSH) and local Frontend Design QA (in the local Cursor workspace). Use it as the working document throughout the task, not as a one-shot preflight form. Do not let unresolved values block safe discovery; block only risky writes.

The CPT handoff folder is on the `.gitignore` allowlist that `setup-orientation` installs, so this file is tracked in Git. WST Builder commits and pushes the handoff after each meaningful update (initial creation, discovery findings, Frontend QA Brief), Frontend Design QA pulls it locally and commits and pushes QA updates back. Final removal happens only after the page goes live (Go-Live), done with `git rm` plus a commit and push, so both workspaces see the closed task; until Go-Live the handoff stays in place so the context is preserved. Handoffs must not contain secrets, tokens, application passwords, SSH keys, token-bearing URLs, dumps, or full media inventories, because they travel through the shared repository.

The template is shared across all CPT work types: `new-cpt-foundation`, `existing-cpt-remodel`, and `visual-only`. The `Workflow Routing` section is filled as classification firms up and gates risky writes from then on.

For visual-only CPT styling without a server foundation step, the handoff is intentionally minimal: no `Server write scope`, but the original Figma link, CPT display URL, optional single URL, stable card/grid/single selectors, and CSS status are still required so `cpt-frontend-qa` can re-read the design and verify the implementation locally.

## Handoff Carrier

| Field | Value |
|-------|-------|
| Project | `<project-name>` |
| Branch or PR | `<branch-or-pr-url>` |
| Handoff storage location | `<project-configured-cpt-handoff-path>` |
| Handoff owner | `<person-or-agent>` |
| Preflight status | `<not-started/in-progress/done>` |
| Server phase status | `<not-started/in-progress/done>` |
| Local frontend phase status | `<not-started/in-progress/done>` |
| Source brief or design | `<figma-url-issue-brief-or-reference>` |
| Source status | `<figma-accessible/brief-only/blocked: reason>` |

## Workflow Routing

| Field | Value |
|-------|-------|
| Work type | `<new-cpt-foundation/existing-cpt-remodel/visual-only/unclear>` |
| Environment | `<local/dev/staging/live/unknown>` |
| Server write scope | `<files/database-acf/wpgb/content/cache/none and a short scope description>` |
| Frontend route | `<cpt-frontend-qa/frontend-section-qa/not-needed-yet/blocked>` |
| Discovery and safety status | `<context-checking/ready-for-safe-writes/write-approved/blocked>` |
| Live or unknown write confirmation | `<not-required/pending/granted/denied and approver>` |
| CSS status | `<existing/new-needed-for-frontend/unknown/not-applicable>` |

## CPT Identity

| Field | Value |
|-------|-------|
| CPT slug | `<resource>` |
| Registered post type | `wso_<resource>` |
| Singular label | `<singular-label>` |
| Plural label | `<plural-label>` |
| Admin visibility | `<show-ui-menu-position-icon-notes>` |
| Supports | `<title-thumbnail-editor-excerpt-or-other>` |
| Content owner | `<who-creates-sample-content>` |

## Detail Page Decision

| Field | Value |
|-------|-------|
| Public detail pages | `<yes/no>` |
| URL slug | `<url-slug-or-not-applicable>` |
| Public archive | `<yes/no>` |
| Search behavior | `<included/excluded/not-applicable>` |
| Single template file | `wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/singles/<resource>-single.php` (theme-relative `smart-template-builder/post-types/<resource>/singles/<resource>-single.php`). Never under `wp-content/plugins/weseo-smart-template-builder/`. |
| Representative single URL | `<dev-or-staging-single-url-or-not-applicable>` |
| Unresolved detail-page questions | `<questions-or-none>` |

## Taxonomy Decision

| Field | Value |
|-------|-------|
| Taxonomy needed | `<yes/no>` |
| Taxonomy name | `wso_tax_<resource>` |
| Taxonomy labels | `<singular-and-plural-labels>` |
| Hierarchy | `<category-style/tag-style/not-applicable>` |
| Public taxonomy archive | `<yes/no>` |
| Rewrite slug | `<taxonomy-slug-or-not-applicable>` |
| Purpose | `<filtering-grouping-card-label-editor-organization-or-none>` |
| Unresolved taxonomy questions | `<questions-or-none>` |

## ACF And Content Model

| Field | Value |
|-------|-------|
| ACF field group | `<group-key-or-title>` |
| Field ownership | `<core-post-fields-taxonomy-featured-image-acf-fields>` |
| ACF fields | `<field-names-field-types-or-unresolved>` |
| Generated field references | `<field-keys-or-post-ids-from-project-context-or-handoff>` |
| Optional or empty fields | `<fields-that-may-be-empty-and-rendering-expectations>` |
| Content variations | `<long-copy-missing-images-empty-terms-repeated-items>` |
| Unresolved field questions | `<questions-or-none>` |

## WP Grid Builder And Display Target

| Field | Value |
|-------|-------|
| WPGB required | `<yes/no>` |
| WPGB grid ID | `<project-local-grid-id-or-explicit-no-wpgb>` |
| WPGB card ID | `<project-local-card-id-or-explicit-no-wpgb>` |
| Display target | `<grid/carousel/existing-section/dedicated-section/card-only/single-only>` |
| Archive or grid URL | `<dev-or-staging-display-url>` |
| Archive/grid integration file | `<template-section-or-registration-path>` |
| Carousel or filter behavior | `<controls-pagination-filters-empty-state-or-not-applicable>` |
| No-WPGB decision | `<reason-if-applicable>` |

## Templates And CSS Hooks

| Field | Value |
|-------|-------|
| Card template file | `wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php` (theme-relative `smart-template-builder/post-types/<resource>/cards/<resource>-card.php`). Never under `wp-content/plugins/weseo-smart-template-builder/`. |
| Additional card parts | `<part-1-part-2-or-none>` |
| Single template file | `<single-template-path-or-not-applicable>` |
| Section integration file | `<section-template-path-or-not-applicable>` |
| Card selector | `.wso-<resource>-card` |
| Archive/grid selector | `.wso-<resource>-grid` |
| Carousel/filter selectors | `<selectors-or-not-applicable>` |
| Single selector | `.wso-<resource>-single` |
| Selectors to preserve | `<selectors-that-template-wpgb-js-or-tests-rely-on>` |
| CSS or SCSS files | `<tracked-source-files-and-generated-files>` |
| CSS custom properties | `<variables-or-theme-tokens>` |

## Protected Existing Artifacts

Fill this section for `existing-cpt-remodel` work types. List the artifacts the remodel must preserve unless the confirmed scope explicitly approves a structural change.

| Field | Value |
|-------|-------|
| Existing registered post type | `<registered-post-type-or-not-applicable>` |
| Existing rewrite/archive/search behavior | `<rewrite-archive-search-settings-or-not-applicable>` |
| Existing taxonomy | `<taxonomy-name-hierarchy-public-archive-or-not-applicable>` |
| Existing ACF field keys | `<existing-field-keys-or-not-applicable>` |
| Existing WPGB grid/card IDs | `<existing-grid-card-ids-or-not-applicable>` |
| Existing template paths | `<card-archive-section-single-template-paths-or-not-applicable>` |
| Existing CSS path | `<existing-css-path-or-not-applicable>` |
| Public selectors to preserve | `<selectors-or-not-applicable>` |

## Expected Visual Behavior

| Field | Value |
|-------|-------|
| Desktop behavior | `<card-grid-single-layout-spacing-media-behavior>` |
| Tablet behavior | `<tablet-layout-and-spacing>` |
| Mobile behavior | `<mobile-layout-and-spacing>` |
| Card states | `<hover-focus-active-disabled-or-none>` |
| Filter or carousel states | `<selected-empty-loading-pagination-or-none>` |
| Link behavior | `<full-card-link-button-link-no-link>` |
| Accessibility notes | `<labels-focus-order-reduced-motion-or-none>` |

## Server Phase Responsibilities

- [ ] Run the bundled `grill-me` preflight before creating or modifying CPT, taxonomy, ACF, WPGB, card, archive, Section integration, or single-template foundations.
- [ ] Classify the request as `new-cpt-foundation`, `existing-cpt-remodel`, `visual-only`, or `unclear`.
- [ ] Ask for the source brief/Figma/reference and public detail-page requirement immediately after classification.
- [ ] Record `Environment`, `Server write scope`, `Frontend route`, and `Discovery and safety status` in `Workflow Routing` before any write.
- [ ] For `live` or `unknown` environments, capture explicit write confirmation before changing files, ACF/DB/WPGB objects, content, or cache.
- [ ] For `existing-cpt-remodel`, fill `Protected Existing Artifacts` before any write.
- [ ] Create this prefilled CPT handoff draft from `plugins/wst-builder/handoffs/cpt-handoff.template.md` in the project-configured CPT handoff storage location.
- [ ] Search Project Context for URLs, paths, IDs, selectors, ACF/WPGB references, and handoff storage before asking the maintainer.
- [ ] Confirm the detail-page decision before enabling public single URLs, rewrites, archives, or search behavior.
- [ ] Confirm the taxonomy decision before creating taxonomy registration or public taxonomy archives.
- [ ] Create or update the CPT registration only when inside the approved server write scope.
- [ ] Create or update the taxonomy only when inside the approved server write scope and the handoff says it is needed.
- [ ] Create or update the ACF CPT field group only when inside the approved server write scope and custom fields are needed.
- [ ] Create or update the WP Grid Builder card and grid foundation only when inside the approved server write scope, or record the explicit no-WPGB decision.
- [ ] Create card template foundation and optional single template foundation only when inside the approved server write scope.
- [ ] Document CSS hooks, CSS path, and `CSS status` in this handoff; do not create or edit CPT CSS files over Remote-SSH.
- [ ] Flush relevant WordPress/cache layers only when inside the approved server write scope and, on `live` or `unknown`, explicitly confirmed.
- [ ] Fill this handoff before local CSS, responsive QA, or browser QA work starts.

## Frontend QA Brief

Filled by `wst-new-post-type` before routing to local frontend QA. `cpt-frontend-qa` treats this as a verifiable starting point and re-reads the original Figma/source link locally to confirm the design intent.

- Use `cpt-frontend-qa` locally in the Cursor workspace. Do not run it over Remote-SSH.
- CPT display URL: `<dev-or-staging-display-url>`
- Representative single URL: `<representative-single-url-or-not-applicable>`
- Card selector: `.wso-<resource>-card`
- Archive/grid selector: `.wso-<resource>-grid`
- Optional single selector: `.wso-<resource>-single` or not applicable.
- Figma/source link: `<figma-url-or-brief>` (unchanged from Handoff Carrier so local QA can re-read it)
- CSS status: `<existing/new-needed-for-frontend/unknown/not-applicable>`
- Required viewports and expected behavior: `<summary-of-desktop-tablet-mobile-card-grid-filter-single>`
- Stable hooks to preserve: `<selectors-from-templates-css-hooks>`
- Server contract: do not change server-side CPT, taxonomy, ACF, WPGB, or WST artifacts from the local phase. Report any server-side discrepancy back into this handoff as a server blocker.
- Verification model: `cpt-frontend-qa` proves planned rules through CSS injection against the real WordPress pages before writing tracked CSS, and only records a final QA pass after the change is actually served by the target URL.
- On completion: write a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed, and keep this active handoff file in place until the page goes live; remove it with `git rm`, commit, and push only after Go-Live so both workspaces converge on the closed task.

## Local Frontend Responsibilities

- [ ] Re-read the original Figma/source link and confirm the design intent against the rendered CPT pages.
- [ ] Confirm Playwright MCP is ready in the local Cursor workspace, or run `frontend-design-qa` `setup-playwright-mcp` before browser QA starts.
- [ ] Confirm browser access to the CPT display URL and the representative single URL; if a login wall, cookie banner, basic auth, or IP allowlist blocks Playwright MCP, stop and ask for session login or access. Never write credentials into this handoff or any tracked file.
- [ ] Use Frontend Design QA `cpt-frontend-qa` for CPT card, archive/grid, carousel/filter, WPGB output, and optional single-template presentation.
- [ ] Use Frontend Design QA `frontend-section-qa` for Section-level layout work when a dedicated WST Section becomes the main CPT display surface; keep CPT card, archive/grid, and optional single-template checks in this CPT handoff.
- [ ] Capture real DOM, matched CSS rules, and computed styles for the affected card, grid, filter, carousel, and single elements before any change.
- [ ] Run a CSS-injection proof of the planned rules against the real CPT display URL (and the single URL when applicable) before writing tracked CSS or SCSS.
- [ ] Implement final CSS or SCSS in tracked local project files only after the injection proof passes.
- [ ] Treat Chrome Local Overrides as an optional manual spike in the user's real browser only, not as the default proof mechanism.
- [ ] Detect the delivery path; on `git-pull-required` or `unknown`, stop with `implementation pass; waiting for server pull/deploy` and wait for user confirmation before continuing to source-served verification.
- [ ] After the user confirms server pull or deploy, verify that the new CSS rules are actually served by the target URL before judging the visual result. If the change is not reflected, record the cache or deploy symptom and do not declare a final pass.
- [ ] Run responsive checks against the CPT display URL and the single URL when applicable.
- [ ] Run the optional project-local Playwright regression command when a real harness exists, or document a skip reason.
- [ ] Stop and document any server, markup, CPT, taxonomy, ACF, WST, or WPGB discrepancy as a server blocker; route back to `wst-new-post-type` or `wst-section-workflow` instead of editing server-side artifacts locally.
- [ ] Commit and push handoff updates (discovery findings, QA notes, status fields) so the server-side workspace sees the latest contract on its next `git pull`.
- [ ] On successful completion, write a short permanent project note summarizing the CPT frontend result, and keep this active handoff file until the page goes live; remove it with `git rm`, commit, and push only after Go-Live so both workspaces converge on the closed task.
- [ ] Commit related local code changes on the same branch or PR according to project Git policy.

## QA Notes

| Field | Value |
|-------|-------|
| Frontend work mode | `<handoff/visual-only-mini-handoff>` |
| Browser access | `<ready/blocked: reason-and-next-action>` |
| Browser QA display URL | `<dev-or-staging-display-url>` |
| Browser QA single URL | `<representative-single-url-or-not-applicable>` |
| Local Playwright MCP status | `<ready/pending: reason-and-next-action>` |
| Required viewports | `<desktop-tablet-mobile-sizes>` |
| Browser access blockers | `<login-cookie-banner-ip-allowlist-self-signed-cert-or-none>` |
| Screenshot policy | `<used-for-review/not-used>` |
| Checks to run | `<card-grid-carousel-filter-single-visibility-and-behavior-checks>` |
| Project-local Playwright command | `<command-or-not-applicable>` |
| Proof mode | `<injection-proof/source-served>` |
| Injection proof | `<pending/pass/fail/not-needed and notes per surface>` |
| Delivery path | `<direct-local-serving/auto-deploy-available/git-pull-required/unknown>` |
| Server pull/deploy | `<not-needed/pending/user-confirmed/not-reflected>` |
| Source-served verification | `<pending/pass/fail/blocked and notes>` |
| Cache state | `<cache-flushed-or-known-cache-state>` |
| Known risks | `<risks-or-none>` |
| QA result | `<pending/pass/fail-and-notes>` |
| Final status | `<implementation-pass-pending-deploy/final-source-served-pass/blocked>` |

## Open Questions

- `<question-or-none>`
