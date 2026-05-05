# Section Handoff: <section-name>

Copy this document for each WST Flexible Content Section. Replace placeholders before the local frontend phase starts.

## Handoff Carrier

| Field | Value |
|-------|-------|
| Project | `<project-name>` |
| Branch or PR | `<branch-or-pr-url>` |
| Handoff owner | `<person-or-agent>` |
| Server phase status | `<not-started/in-progress/done>` |
| Local frontend phase status | `<not-started/in-progress/done>` |

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
| ACF Flexible Content layout | `<layout-key-and-layout-name>` |
| ACF fields | `<field-keys-or-field-names>` |
| Content setup notes | `<page-id-language-content-state>` |

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

- [ ] Create or update the WST Section template.
- [ ] Create or update the ACF section field group and Flexible Content layout.
- [ ] Register the Section in `flexible-content.php`.
- [ ] Create representative content on the target page.
- [ ] Flush relevant WordPress/cache layers.
- [ ] Fill this handoff before local CSS/Playwright work starts.

## Local Frontend Responsibilities

- [ ] Implement CSS/SCSS in the local Git repo.
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
