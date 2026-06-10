# Section Handoff Draft: <section-name>

Copy this document for each WST Flexible Content Section into the project-configured handoff storage location from Project Context. Replace placeholders before Section implementation or local frontend work starts. If a blocking value is unknown, keep an explicit `<unresolved: ...>` placeholder instead of inventing a technical value.

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

## Section Identity

| Field | Value |
|-------|-------|
| Section name | `<section-name>` |
| Layout name | `<layout-name>` |
| Page URL | `<dev-or-staging-url-with-section>` |
| Design desktop | `<figma-desktop-frame-url-or-brief>` |
| Design mobile | `<figma-mobile-frame-url>` or `no-mobile-design: derived-from-desktop` |

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

## CSS Hooks

| Field | Value |
|-------|-------|
| Primary section class | `.wso-section-<section-slug>` |
| Wrapper/classes | `<wrapper-or-column-classes>` |
| CSS custom properties | `<variables-or-theme-tokens>` |
| Selectors to preserve | `<selectors-that-template-or-js-relies-on>` |

## Visual QA Targets

This matrix is the single source of truth for the expected visual behavior. It replaces free-form behavior prose. One row = one verifiable expectation; each row is individually checked and answered in its `Result` cell during local frontend QA.

Viewport roles map to project pixel values from `PROJECT-CONTEXT.md`. Do not invent widths; keep an explicit `<unresolved: ...>` placeholder until project context supplies them.

| Viewport role | Check width |
|---------------|-------------|
| desktop | `<px-from-project-context>` |
| tablet | `<px-from-project-context>` |
| mobile | `<px-from-project-context>` |

Phrasing rules for every expectation:

1. Each expectation must be answerable as a yes/no question against the rendered page. Vague words such as "comfortable", "nice", or "enough spacing" are not allowed.
2. Where a theme token or CSS custom property defines a value, name it (for example `gap = --section-gap-mobile`) instead of describing a vague size.
3. Name elements through the stable selectors from the CSS Hooks section, not through visual descriptions like "the card on the left".

The base variants below are mandatory. Every base variant keeps at least one expectation row or is marked `n/a: <reason>` (for example `n/a: Section has no image field`). Mobile expectations must be sourced from the `Design mobile` frame; when it is `no-mobile-design: derived-from-desktop`, say so in the expectation so the local phase knows where interpretation latitude exists. Add free extra rows for Section-specific cases.

| Variant | Viewport | Expectation | Result |
|---------|----------|-------------|--------|
| default | desktop | `<expectation>` | `<pending/pass/fail: note>` |
| default | mobile | `<expectation-from-design-mobile>` | `<pending/pass/fail: note>` |
| long headline/copy | desktop | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| long headline/copy | mobile | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| optional field empty | desktop | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| optional field empty | mobile | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| many repeats | desktop | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| many repeats | mobile | `<expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |
| mobile stack | mobile | `<stacking-order-and-gap-expectation>` | `<pending/pass/fail: note>` |
| interaction states | `<viewport-or-all>` | `<hover-focus-active-expectation-or-n/a: reason>` | `<pending/pass/fail: note>` |

## Server Phase Responsibilities

- [ ] Run `grill-me` preflight before creating or modifying Section files or ACF structures.
- [ ] Search Project Context for URLs, paths, IDs, selectors, ACF references, and handoff storage before asking the maintainer.
- [ ] Create this prefilled handoff draft in the project-configured storage location.
- [ ] Create or update the WST Section template.
- [ ] Create or update the ACF section field group and Flexible Content layout.
- [ ] Register the Section in `flexible-content.php`.
- [ ] Create representative content on the target page.
- [ ] Flush relevant WordPress/cache layers.
- [ ] Fill the Visual QA Targets matrix (viewport mapping, all base variants answered or `n/a: <reason>`, mobile rows sourced from `Design mobile`).
- [ ] Fill this handoff before local CSS/Playwright work starts.

## Local Frontend Responsibilities

- [ ] Implement CSS/SCSS in the local Git repo.
- [ ] Use Chrome Local Overrides only as a temporary spike tool if needed.
- [ ] Move final CSS changes into tracked files.
- [ ] Verify every Visual QA Targets row at its viewport and write the per-row `Result` (`pass`/`fail: note`) back into the matrix.
- [ ] Run or document Playwright checks for the target Section.
- [ ] Commit the handoff updates with the Section code on the same branch or PR.

## QA Notes

| Field | Value |
|-------|-------|
| Playwright target URL | `<dev-or-staging-url-with-section>` |
| Checks to run | All rows of the `Visual QA Targets` matrix; per-row results live in the matrix `Result` column |
| Cache state | `<cache-flushed-or-known-cache-state>` |
| Known risks | `<risks-or-none>` |
| QA result | `<pending/pass/fail-and-notes>` |

## Open Questions

- `<question-or-none>`
