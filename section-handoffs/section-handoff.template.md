# Section Handoff: <section-name>

Create one new handoff per Section task. Name the file `<section-slug>-<work-type>-handoff.md` and store it at the project-configured handoff storage location from Project Context. Replace placeholders progressively while discovery, decisions, writes, and verification happen. If a blocking value is unknown, keep an explicit `<unresolved: ...>` placeholder instead of inventing a technical value.

This handoff is the live contract between server-side WST Builder work (over Remote-SSH) and local Frontend Design QA (in the local Cursor workspace). Use it as the working document throughout the task, not as a one-shot preflight form. Do not let unresolved values block safe discovery; block only risky writes.

The handoff folder is on the `.gitignore` allowlist that `setup-orientation` installs, so this file is tracked in Git. WST Builder commits and pushes the handoff after each meaningful update (initial creation, discovery findings, Frontend QA Brief), Frontend Design QA pulls it locally and commits and pushes QA updates back. Final removal at the end of the lifecycle is done with `git rm` plus a commit and push, so both workspaces see the closed task. Handoffs must not contain secrets, tokens, application passwords, SSH keys, token-bearing URLs, dumps, or full media inventories, because they travel through the shared repository.

The template is shared across all Section work types: `new-section-foundation`, `existing-section-remodel`, and `visual-only`. The `Workflow Routing` section is filled as classification firms up and gates risky writes from then on.

For visual-only Section work the handoff is intentionally minimal: no `Server write scope`, but the original Figma link, target URL, existing Section identity, stable hooks, and CSS status are still required so `frontend-section-qa` can re-read the design and verify the implementation locally.

## Handoff Carrier

| Field | Value |
|-------|-------|
| Project | `<project-name>` |
| Branch or PR | `<branch-or-pr-url>` |
| Handoff filename | `<section-slug>-<work-type>-handoff.md` |
| Handoff storage location | `<project-configured-handoff-path>` |
| Handoff owner | `<person-or-agent>` |
| Server phase status | `<not-started/in-progress/done>` |
| Local frontend phase status | `<not-started/in-progress/done>` |

## Workflow Routing

| Field | Value |
|-------|-------|
| Work type | `<new-section-foundation/existing-section-remodel/visual-only/unclear>` |
| Environment | `<local/dev/staging/live/unknown>` |
| Server write scope | `<files/database-acf/content/cache/none and a short scope description>` |
| Frontend route | `<frontend-section-qa/not-needed-yet/blocked>` |
| Discovery and safety status | `<context-checking/ready-for-safe-writes/write-approved/blocked>` |
| Live or unknown write confirmation | `<not-required/pending/granted/denied and approver>` |
| CSS status | `<existing/new-needed-for-frontend/unknown/not-applicable>` |

## Section Identity

| Field | Value |
|-------|-------|
| Section name | `<section-name>` |
| Section slug | `<section-slug>` |
| Layout name | `<layout-name>` |
| Page URL | `<dev-or-staging-url-with-section>` |
| Design desktop | `<figma-desktop-frame-url-or-brief>` |
| Design mobile | `<figma-mobile-frame-url>` or `no-mobile-design: derived-from-desktop` |
| Source design status | `<figma-accessible/brief-only/blocked: reason>` |
| Variants/states | `<single-variant-or-list-of-variants>` |

## Discovery Sources

Record the evidence the server-side decisions are based on. Keep the original Figma links unchanged so `frontend-section-qa` can re-read the design locally and verify the implementation against it. Most Sections have a dedicated mobile design frame; record it separately so mobile expectations are sourced from it instead of being derived from the desktop frame.

| Field | Value |
|-------|-------|
| Original Figma/source link (desktop) | `<figma-desktop-frame-url-or-brief>` |
| Original Figma/source link (mobile) | `<figma-mobile-frame-url>` or `no-mobile-design: derived-from-desktop` |
| Test placement / target page | `<page-url-or-page-id>` |
| Similar Section patterns inspected | `<section-files-layouts-urls-or-none-found>` |
| WST/ACF rules applied | `acf-wst-patterns.mdc`, `<reference-or-additional-rules-if-used>` |
| Media/library lookup | `<not-needed/existing-assets-reused-with-ids/wp-media-import-invoked>` |
| Project-local examples consulted | `<paths-or-none>` |
| Assumptions recorded | `<derived-from-figma-or-patterns-or-none>` |

## WordPress And WST References

| Field | Value |
|-------|-------|
| Template file | `wp-content/themes/<child-theme>/smart-template-builder/sections/<section-template>.php` (theme-relative `smart-template-builder/sections/<section-template>.php`). Never under `wp-content/plugins/weseo-smart-template-builder/`. |
| CSS file | `styles/sections/<section-template>.css` |
| ACF section field group | `<group-key-or-title>` |
| ACF Flexible Content field | `<fc-field-key-or-post-id>` |
| ACF Flexible Content layout | `<layout-key-and-layout-name>` |
| ACF clone child field | `<clone-child-field-key-or-post-id>` |
| ACF fields | `<field-keys-or-field-names>` |
| Content setup notes | `<page-id-language-content-state>` |

## Protected Existing Artifacts

Fill this section for `existing-section-remodel` work types. List the artifacts the remodel must preserve unless the confirmed scope explicitly approves a structural change. Reuse and update existing values in-place by default.

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

WST Builder records CSS hook expectations here. Final CSS or SCSS is implemented locally by `frontend-section-qa` and is not written on the server from this Skill.

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

- [ ] Read project context and the WST/ACF rules before any WST or ACF structural write (`acf-wst-patterns.mdc`, `acf-wst-patterns-reference.md` for examples).
- [ ] Classify the request as `new-section-foundation`, `existing-section-remodel`, `visual-only`, or `unclear`. Reclassification mid-task always stops and asks.
- [ ] Run the compact Start question block only for values that project context did not already supply (Figma link, test placement, slug confirmation for new Section, server-relevant variants).
- [ ] Record `Environment`, `Server write scope`, `Frontend route`, and `Discovery and safety status` in `Workflow Routing` before any write.
- [ ] For `live` or `unknown` environments, capture explicit, scoped write confirmation before changing files, ACF objects, content, or cache. Cache flush on `live` or `unknown` requires its own confirmation.
- [ ] For `existing-section-remodel`, fill `Protected Existing Artifacts` and default to in-place changes. Do not create new template files, ACF field groups, Flexible Content layouts, clone child fields, or style loader entries unless explicitly approved.
- [ ] Search Project Context, existing Sections, and rendered markup before asking the maintainer for structural references.
- [ ] Announce a short Execution Plan before any server write.
- [ ] Create or update the WST Section template only inside the approved server write scope.
- [ ] Create or update the ACF section field group and Flexible Content layout only inside the approved server write scope.
- [ ] Register the Section in `flexible-content.php` only inside the approved server write scope.
- [ ] Document CSS hooks, CSS path, and `CSS status` in this handoff. Do not create or edit Section CSS/SCSS files over Remote-SSH.
- [ ] Use `wp-media-import` (external Skill) when Media Library lookup shows the Section needs assets that are not already available, and record imported Media IDs.
- [ ] Create representative content on the target page only inside the approved server write scope.
- [ ] Flush relevant caches only inside the approved server write scope; on `live` or `unknown`, with explicit confirmation.
- [ ] Verify server-side function and existence only: page loads without PHP fatal/warning, Section markup present, primary class present, layout selectable in editor/ACF where checkable.
- [ ] Fill the `Visual QA Targets` matrix: viewport mapping from project context, every base variant answered or marked `n/a: <reason>`, mobile rows sourced from `Design mobile`.
- [ ] Write the `Frontend QA Brief` into this handoff and route to `frontend-section-qa`.

## Frontend QA Brief

Filled by `wst-section-workflow` before routing. `frontend-section-qa` treats this as a verifiable starting point and re-reads the original Figma/source link locally to confirm the design intent.

- Use `frontend-section-qa` locally in the Cursor workspace. Do not run it over Remote-SSH.
- Target URL: `<dev-or-staging-url-with-section>`
- Section selector: `.wso-section-<section-slug>`
- Figma/source links: `<design-desktop-and-design-mobile>` (unchanged from Discovery Sources so local QA can re-read them)
- CSS status: `<existing/new-needed-for-frontend/unknown/not-applicable>`
- Required viewports and expected behavior: see the `Visual QA Targets` matrix (viewport mapping plus one row per verifiable expectation). All base variants are answered or marked `n/a` before routing.
- Stable hooks to preserve: `<selectors-from-css-hooks>`
- Server contract: do not change server-side ACF/WST artifacts from the local phase. Report any server-side discrepancy back into this handoff as a server blocker.
- On completion: write a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed, then remove this active handoff file with `git rm`, commit, and push so both workspaces converge on the closed task.

## Local Frontend Responsibilities

- [ ] Re-read the original Figma/source links (desktop and mobile) and confirm the design intent against the rendered page. A `no-mobile-design` note means documented interpretation latitude for mobile, not a missing value.
- [ ] Confirm Playwright MCP is ready in the local Cursor workspace, or run `frontend-design-qa` `setup-playwright-mcp` before browser QA starts.
- [ ] Drive a Playwright MCP browser QA loop against the handoff Page URL across the required viewports.
- [ ] Implement CSS/SCSS in the local Git repo.
- [ ] Create or register the Section CSS file in tracked local source when `CSS status` is `new-needed-for-frontend`.
- [ ] Use Chrome Local Overrides only as a temporary spike tool if needed.
- [ ] Move final CSS changes into tracked files.
- [ ] Verify every `Visual QA Targets` row at its viewport and write the per-row `Result` (`pass`/`fail: note`) back into the matrix.
- [ ] Run the optional project-local Playwright regression command when a real harness exists, or document a skip reason.
- [ ] If a server-side discrepancy is found, record it as a server blocker in this handoff instead of editing ACF/WST locally.
- [ ] Commit and push handoff updates (discovery findings, QA notes, status fields) so the server-side workspace sees the latest contract on its next `git pull`.
- [ ] On successful completion, write a short permanent project note summarizing the Section, then remove this active handoff file with `git rm`, commit, and push so both workspaces converge on the closed task.
- [ ] Commit related local code changes on the same branch or PR according to project Git policy.

## QA Notes

| Field | Value |
|-------|-------|
| Browser QA target URL | `<dev-or-staging-url-with-section>` |
| Local Playwright MCP status | `<ready/pending: reason-and-next-action>` |
| Required viewports | From the `Visual QA Targets` viewport mapping (`<unresolved: ...>` until project context supplies the widths) |
| Browser access blockers | `<login-cookie-banner-ip-allowlist-self-signed-cert-or-none>` |
| Screenshot policy | `<used-for-review/not-used>` |
| Checks to run | All rows of the `Visual QA Targets` matrix; per-row results live in the matrix `Result` column |
| Project-local Playwright command | `<command-or-not-applicable>` |
| Server verification result | `<page-loads-section-present-primary-class-present-layout-selectable-or-issue>` |
| Cache state | `<cache-flushed-or-known-cache-state>` |
| Known risks | `<risks-or-none>` |
| QA result | `<pending/pass/fail-and-notes>` |

## Open Questions

- `<question-or-none>`
