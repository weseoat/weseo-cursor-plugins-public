# CPT Foundation Handoff Draft: <cpt-name>

Copy this document for each WST Custom Post Type foundation into the project-configured CPT handoff storage location from Project Context. Replace placeholders before CPT implementation or local frontend work starts. If a blocking value is unknown, keep an explicit `<unresolved: ...>` placeholder instead of inventing a technical value.

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
| Single template file | `smart-template-builder/post-types/<resource>/singles/<resource>-single.php` |
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
| Card template file | `smart-template-builder/post-types/<resource>/cards/<resource>-card.php` |
| Additional card parts | `<part-1-part-2-or-none>` |
| Single template file | `<single-template-path-or-not-applicable>` |
| Section integration file | `<section-template-path-or-not-applicable>` |
| Card selector | `.wso-<resource>-card` |
| Archive/grid selector | `.wso-<resource>-grid` |
| Carousel/filter selectors | `<selectors-or-not-applicable>` |
| Single selector | `.wso-<resource>-single` |
| Selectors to preserve | `<selectors-that-template-wpgb-js-or-tests-rely-on>` |
| CSS or SCSS files | `<tracked-source-files-and-generated-files>` |

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
- [ ] Create this prefilled CPT handoff draft from `plugins/wst-builder/handoffs/cpt-handoff.template.md` in the project-configured CPT handoff storage location.
- [ ] Confirm the detail-page decision before enabling public single URLs, rewrites, archives, or search behavior.
- [ ] Confirm the taxonomy decision before creating taxonomy registration or public taxonomy archives.
- [ ] Create or update the CPT registration through the project-approved path.
- [ ] Create or update the taxonomy only when the handoff says it is needed.
- [ ] Create or update the ACF CPT field group when custom fields are needed.
- [ ] Create or update the WP Grid Builder card and grid foundation, or record the explicit no-WPGB decision.
- [ ] Create card template foundation and optional single template foundation.
- [ ] Record stable selectors and tracked CSS or SCSS file expectations.
- [ ] Flush relevant WordPress/cache layers with the project-local command.
- [ ] Fill this handoff before local CSS, responsive QA, or Playwright-oriented work starts.

## Local Frontend Responsibilities

- [ ] Use Frontend Design QA `cpt-frontend-qa` for CPT card, archive/grid, carousel/filter, WPGB output, and optional single-template presentation.
- [ ] Use Frontend Design QA `frontend-section-qa` for Section-level layout work when a dedicated WST Section becomes the main CPT display surface.
- [ ] Keep CPT card, archive/grid, and optional single-template checks recorded in this CPT handoff even when Section layout work is split out.
- [ ] Implement final CSS or SCSS in tracked local project files.
- [ ] Use Chrome Local Overrides only as a temporary spike tool if needed.
- [ ] Run responsive checks against the handoff display URLs.
- [ ] Run or document Playwright-oriented checks for the target CPT display and optional single view.
- [ ] Commit the handoff updates with the CPT frontend code on the same branch or PR.

## QA Notes

| Field | Value |
|-------|-------|
| Playwright display URL | `<dev-or-staging-display-url>` |
| Playwright single URL | `<representative-single-url-or-not-applicable>` |
| Checks to run | `<card-grid-carousel-filter-single-visibility-and-behavior-checks>` |
| Responsive viewports | `<desktop-tablet-mobile-sizes>` |
| Cache state | `<cache-flushed-or-known-cache-state>` |
| Known risks | `<risks-or-none>` |
| QA result | `<pending/pass/fail-and-notes>` |

## Open Questions

- `<question-or-none>`
