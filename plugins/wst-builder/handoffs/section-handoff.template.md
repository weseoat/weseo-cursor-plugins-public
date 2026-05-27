# Section Handoff Draft: <section-name>

Copy this document for each WST Flexible Content Section into the project-configured handoff storage location from Project Context. Replace placeholders before Section implementation or local frontend work starts. If a blocking value is unknown, keep an explicit `<unresolved: ...>` placeholder instead of inventing a technical value.

This template is shared across all Section work types: `new-section-foundation`, `existing-section-remodel`, and `visual-only`. The `Workflow Routing` section is filled during the preflight and gates all later server-phase writes.

## Handoff Carrier

| Field | Value |
|-------|-------|
| Project | `<project-name>` |
| Branch or PR | `<branch-or-pr-url>` |
| Handoff storage location | `<project-configured-handoff-path>` |
| Handoff owner | `<person-or-agent>` |
| Preflight status | `<not-started/in-progress/done>` |
| Server phase status | `<not-started/in-progress/done>` |
| Local frontend phase status | `<not-started/in-progress/done>` |

## Workflow Routing

| Field | Value |
|-------|-------|
| Work type | `<new-section-foundation/existing-section-remodel/visual-only/unclear>` |
| Environment | `<local/dev/staging/live/unknown>` |
| Server write scope | `<files/database-acf/content/cache/none and a short scope description>` |
| Frontend route | `<frontend-section-qa/not-needed-yet/blocked>` |
| Preflight gate status | `<read-only/handoff-created/write-approved/blocked>` |
| Live or unknown write confirmation | `<not-required/pending/granted/denied and approver>` |
| CSS status | `<existing/new-needed-for-frontend/unknown/not-applicable>` |

## Section Identity

| Field | Value |
|-------|-------|
| Section name | `<section-name>` |
| Layout name | `<layout-name>` |
| Page URL | `<dev-or-staging-url-with-section>` |
| Source design/reference | `<figma-url-or-brief>` |

## WordPress And WST References

| Field | Value |
|-------|-------|
| Template file | `smart-template-builder/sections/<section-template>.php` |
| CSS file | `styles/sections/<section-template>.css` |
| ACF section field group | `<group-key-or-title>` |
| ACF Flexible Content field | `<fc-field-key-or-post-id>` |
| ACF Flexible Content layout | `<layout-key-and-layout-name>` |
| ACF clone child field | `<clone-child-field-key-or-post-id>` |
| ACF fields | `<field-keys-or-field-names>` |
| Content setup notes | `<page-id-language-content-state>` |

## Protected Existing Artifacts

Fill this section for `existing-section-remodel` work types. List the artifacts the remodel must preserve unless the confirmed scope explicitly approves a structural change.

| Field | Value |
|-------|-------|
| Existing layout name | `<existing-layout-name-or-not-applicable>` |
| Existing layout key | `<existing-layout-key-or-not-applicable>` |
| Existing parent_layout binding | `<existing-parent-layout-or-not-applicable>` |
| Existing ACF field keys | `<existing-field-keys-or-not-applicable>` |
| Existing template path | `<existing-template-path-or-not-applicable>` |
| Existing CSS path | `<existing-css-path-or-not-applicable>` |
| Public selectors to preserve | `<selectors-or-not-applicable>` |

## CSS Hooks

| Field | Value |
|-------|-------|
| Primary section class | `.wso-section-<section-slug>` |
| Wrapper/classes | `<wrapper-or-column-classes>` |
| CSS custom properties | `<variables-or-theme-tokens>` |
| Selectors to preserve | `<selectors-that-template-or-js-relies-on>` |

## Expected Visual Behavior

| Field | Value |
|-------|-------|
| Desktop behavior | `<desktop-layout-and-spacing>` |
| Tablet behavior | `<tablet-layout-and-spacing>` |
| Mobile behavior | `<mobile-layout-and-spacing>` |
| Content variations | `<empty-fields-repeaters-long-copy-images>` |
| Interaction states | `<hover-focus-active-or-none>` |

## Server Phase Responsibilities

- [ ] Run the bundled `grill-me` preflight before creating or modifying Section files or ACF structures.
- [ ] Classify the request as `new-section-foundation`, `existing-section-remodel`, `visual-only`, or `unclear`.
- [ ] Record `Environment`, `Server write scope`, `Frontend route`, and `Preflight gate status` in `Workflow Routing` before any write.
- [ ] For `live` or `unknown` environments, capture the explicit write confirmation before changing files, ACF objects, content, or cache.
- [ ] For `existing-section-remodel`, fill `Protected Existing Artifacts` before any write.
- [ ] Search Project Context for URLs, paths, IDs, selectors, ACF references, and handoff storage before asking the maintainer.
- [ ] Create this prefilled handoff draft in the project-configured storage location.
- [ ] Create or update the WST Section template only when inside the approved server write scope.
- [ ] Create or update the ACF section field group and Flexible Content layout only when inside the approved server write scope.
- [ ] Register the Section in `flexible-content.php` only when inside the approved server write scope.
- [ ] Document CSS hooks, CSS path, and `CSS status` in this handoff; do not create or edit Section CSS files over Remote-SSH.
- [ ] Create representative content on the target page only when inside the approved server write scope.
- [ ] Flush relevant WordPress/cache layers only when inside the approved server write scope and, on `live` or `unknown`, explicitly confirmed.
- [ ] Fill this handoff before local CSS/Playwright work starts.

## Local Frontend Responsibilities

- [ ] Implement CSS/SCSS in the local Git repo.
- [ ] Create or register the Section CSS file in tracked local source when `CSS status` is `new-needed-for-frontend`.
- [ ] Use Chrome Local Overrides only as a temporary spike tool if needed.
- [ ] Move final CSS changes into tracked files.
- [ ] Run responsive checks against the handoff Page URL.
- [ ] Run or document Playwright checks for the target Section.
- [ ] Commit the handoff updates with the Section code on the same branch or PR.

## QA Notes

| Field | Value |
|-------|-------|
| Playwright target URL | `<dev-or-staging-url-with-section>` |
| Checks to run | `<visual-and-behavior-checks>` |
| Cache state | `<cache-flushed-or-known-cache-state>` |
| Known risks | `<risks-or-none>` |
| QA result | `<pending/pass/fail-and-notes>` |

## Open Questions

- `<question-or-none>`
