---
name: cpt-frontend-qa
description: Implement and verify frontend presentation for a WST Custom Post Type in the local SmartFlow workspace from a CPT work record. Use for CPT card, archive or grid, WP Grid Builder output, carousel or filter, and optional single-template CSS/SCSS work, the Playwright MCP browser QA loop, injection-proofed iteration against the served WordPress page, and the one-time bridge-verified served check after the bundled deploy pass. Consumes and writes back the CPT work record in the project docs layer.
---

# CPT Frontend QA

This Skill owns the frontend CSS/SCSS pass for a WST CPT: cards, archive/grid presentation, carousel/filter behavior, WP Grid Builder frontend output, optional single templates, the Playwright MCP browser QA loop, injection-proofed iteration, the Visual QA Targets writeback, and the closing deploy pass with the one-time bridge-verified served check. It does not own CPT registration, taxonomies, ACF JSON field groups, WPGB grid/card foundation, WST templates, WordPress content, or the status bridge.

Everything happens in one workspace: the wp-content-level repository checkout. The remote WordPress serves the last deployed commit; local CSS edits never appear on the target URL by saving. The two proof modes have a fixed relationship:

- **Injection-proof is the main mode.** All visual iteration happens by injecting the planned rules into the served page through Playwright MCP and verifying them against the real DOM — per surface: card, archive/grid, carousel/filter, optional single. Tracked source is written once the injected rules win the cascade; iteration continues injection-proofed without per-tweak deploy rounds.
- **The bridge-verified served check is a one-time confirmation.** When the CSS pass is complete, everything goes into one bundled deploy pass (`deploy-and-branches` Rule): commit, hard stop, the user pushes, and the status bridge confirms `deployed_commit` with the bounded retry and abort budget. Then one served confirmation pass closes the work.

When a CPT display becomes primarily a dedicated WST Section layout, route Section-level layout behavior to `frontend-section-qa`; card, archive/grid, carousel/filter, WPGB output, and optional single-template QA stay in this Skill. On every start — direct start included, not only under package orchestration — the CSS/browser-QA run executes through the `cpt-visual-implementer` runner per the `agent-routing` Rule, one runner per surface; the deploy pass, bridge verification, and commit stay with the main chat (runners never commit), and hard stops from the run come back as `OPEN DECISION` in the runner's return format. The semantics are identical either way.

## Required Starting Point

The contract is the CPT work record in the project docs layer (default `docs/post-types/<resource>.md`, or the project convention from `PROJECT-CONTEXT.md`). `wst-new-post-type` creates and fills it: identity, detail-page decision, display target, selectors, WPGB IDs read back over the bridge, and the frontend responsibilities. There is no separate handoff file.

- If a work record exists: read it, use it as the contract, and write QA results back into it.
- If no work record exists and the user confirms visual-only CPT styling: create a minimal work record at the default path with the existing CPT identity (post type, display URL, card/grid selectors), Figma/source link, CSS status, and a clear `No structural CPT/taxonomy/ACF/WPGB changes required` note. Keep `<unresolved: ...>` markers instead of inventing values.
- If the task turns out to need CPT, taxonomy, ACF, WPGB, or WST template changes: stop and route back to `wst-new-post-type` (or `wst-section-workflow` for Section-level WST/ACF issues) through the work record.

## Status Fields The Skill Maintains

- `frontend work mode`: `work-record` or `visual-only-minimal-record`.
- `browser access`: `ready`, `degraded: <broken-tool>-fallback-evaluate`, or `blocked: <reason>`.
- `mcp tool defect`: empty, or a short tag plus reason.
- `injection proof` (per surface: card, grid/archive, filter/carousel, single): `pending`, `pass`, `pass-degraded`, `fail`, or `blocked: <reason>`.
- `deploy state`: `not-committed`, `committed-awaiting-push`, `bridge-verified: <commit-hash>`, or `aborted: hash-mismatch`.
- `served check`: `pending`, `pass`, `pass-degraded`, `fail: <symptom>`, or `blocked: <reason>`.
- `final status`: `implementation-pass-pending-deploy`, `final-bridge-verified-pass`, `final-bridge-verified-pass-degraded`, or `blocked`.

`pass-degraded` means Playwright MCP produced the evidence through the Degraded Mode fallback. It still counts as Playwright MCP evidence and does not unlock any substitute browser.

## Playwright MCP Preflight

Identical rules to `frontend-section-qa`, applied to the CPT display URL and — when public detail pages exist — a representative single URL:

1. Read `PROJECT-CONTEXT.md` and the work record for the `playwright_mcp` status; follow the `playwright-browser-claim` Rule when parallel Playwright servers run.
2. Setup or repair goes to the Playwright MCP step of the bundled `setup-local-project` Skill (Step 10), never improvised here.
3. Run the Capability Probe against the display URL (and the single URL when applicable): `browser_navigate` once, then `browser_evaluate` reading `location.href`, `document.title`, a known card selector, and a known grid or single selector. Record per-tool results in the work record.
4. Whole server down or both navigate and evaluate broken: hard stop, `browser access: blocked: playwright-mcp-unavailable`, `final status: blocked`, route to `setup-local-project` Step 10.
5. Single-tool defects with a working `browser_evaluate`: Degraded Mode — evaluate-based navigation, DOM/computed-style reads, CSS injection, and stylesheet fetch for the served check, recorded as `pass-degraded` with the defect, fallback path, and next repair action documented.
6. Content-level blockers (login, cookie consent, allowlist): record them and treat browser access as a hard precondition for final CSS writes. Throwaway session logins may be used through Playwright/CDP only; never write credentials into the work record or any tracked file.

No substitute browser: the Cursor Browser, manual inspection, pasted screenshots, and Chrome Local Overrides never set `injection proof: pass` or `served check: pass`. A Cursor Browser check is diagnostic-only and documented as such.

## Inputs

Read before editing:

- The CPT work record: registered post type, labels, detail-page decision, display target (WPGB grid, carousel, embedding Section, card-only, single), card/grid/single template references, stable selectors, WPGB grid/card IDs, ACF fields and taxonomy terms that affect visible output, the `Visual QA Targets` matrix, deploy state.
- `PROJECT-CONTEXT.md`: theme tokens, breakpoints and QA viewport rungs, rem scale, style loader, build command, bridge base URL and credential env var names, working branch.
- The Figma/source design links from the work record (card, grid, filter, carousel, and single frames; desktop and mobile).
- The gating Rules: `css-guideline`, `figma-to-code`, `frontend-section-qa-layout-preflight` (container fit check applies to grid wrappers too), `frontend-section-qa-tablet-band` (full viewport ladder).

If a required value is unresolved, derive it from project context, the Figma source, the real target page, or existing patterns before asking; missing WPGB IDs are read over the bridge (`GET /wp-json/wso/v1/status`) or recorded as `<unresolved: ...>`, never guessed.

## Workflow

```text
CPT Frontend QA:
- [ ] Read the CPT work record (or create the minimal visual-only record)
- [ ] Read PROJECT-CONTEXT.md and the gating Rules
- [ ] Playwright MCP preflight and Capability Probe on display URL and single URL when applicable
- [ ] Confirm browser access, or stop and ask for login/access
- [ ] Confirm display target, selectors, and detail-page decision from the work record
- [ ] Re-read the Figma/source design (card, grid, filter, single frames)
- [ ] Inspect existing CPT, card, grid, and theme CSS/SCSS patterns
- [ ] Browser QA loop: real DOM, matched and computed styles per surface
- [ ] Injection-proof the planned rules per surface (main mode; iterate here)
- [ ] Write tracked CSS/SCSS per surface once the injected rules win the cascade
- [ ] Keep iterating injection-proofed: viewport ladder, card counts, filters, states
- [ ] Fill the per-row Result cells of the Visual QA Targets matrix (injection-proofed values)
- [ ] Deploy pass: one commit (CSS + work record) with trailer, HARD STOP, user pushes
- [ ] Bridge check deployed_commit with the bounded retry budget; abort cleanly on mismatch
- [ ] One-time served check on display URL and single URL when applicable
- [ ] Final writeback: status fields, QA notes, remaining risks; refresh the CPT doc (auto-docs scoped run)
```

## 1. Design And Existing Patterns

Re-read the Figma frames for card, grid, filter, carousel, and optional single from the work record link: spacing, typography, breakpoints, image behavior, interaction states. Interpret them project-conformly and document real deviations as `figma shows X, project pattern enforces Y, implemented as Z`.

Inspect existing patterns before writing: nearby post-type, card, grid, Section, and single-template styles; global token/typography/button/image rules; SCSS-vs-generated-CSS setup; the style loader; and selectors that already target the same CPT, WPGB, card, taxonomy/filter, carousel, or single classes.

Keep the work inside project-approved frontend paths (`file-edit-boundary` Rule). Do not rename WST hooks or WPGB selectors marked stable in the work record. `functions.php` is forbidden; server-side CPT, taxonomy, ACF, WPGB foundation, and content work are out of scope here.

## 2. Injection-Proofed Iteration Per Surface (Main Mode)

Work against the served pages, which render the last bridge-verified deployed commit. Scope checks to the target display's chunk and never anchor on HTML comments (minifiers strip them).

Core loop (display URL; repeat the proof on the single URL when single rules are in scope):

1. Navigate and snapshot to confirm the page rendered; locate the grid, carousel, archive, or Section wrapper by the stable selector; assert the expected card selector is visible or matches the expected count.
2. Capture matched and computed styles for the elements the planned rules target; note theme, WPGB, or plugin selectors that compete.
3. Compose the planned rules in their tracked-file form and inject them through Playwright MCP or CDP; re-read computed styles; confirm the rules win the cascade, adjusting minimally per the `css-guideline` Rule. `!important` only when the theme, WPGB, or plugin pattern leaves no better option.
4. Once the proof passes for a surface, write the tracked CSS/SCSS for that surface and set its `injection proof: pass`:
   - **Card**: the stable card selector (usually `.wso-<resource>-card`), scoped card variables (spacing, image ratio, content gap, overlay, states), project tokens, and graceful handling of missing taxonomy terms, images, prices, dates, excerpts, or links. Verify hover, focus-visible, active, and disabled states when the card is interactive.
   - **Archive/grid/carousel/filter**: keep WPGB structure intact and scope layout changes to the CPT grid wrapper or approved container; verify slide spacing, overflow, controls, pagination, keyboard focus, and mobile swipe for carousels; filter visibility, selected state, empty-result behavior, and responsive wrapping for filterable archives. No global WPGB, container, row, or column changes for one CPT unless project context says shared behavior is intended.
   - **Optional single** (only when the work record says public detail pages exist): the stable single selector (usually `.wso-<resource>-single`), global content/heading/button/image patterns, long rich text, missing optional fields, related content, breadcrumb/CTA behavior. If no public detail page exists, record that single QA is not applicable.
5. Continue iterating injection-proofed for every further adjustment; the closing deploy pass collects all tracked changes.

Run the full QA viewport ladder from the `frontend-section-qa-tablet-band` Rule on the display URL (and single URL when applicable). Check the CPT base variants: expected card counts and repeated cards, long labels and excerpts, missing images, empty optional fields, inconsistent image ratios, filter states, pagination. Fill the per-row `Result` cells of the `Visual QA Targets` matrix as rows are verified injection-proofed.

If a visual decision requires a new project token or shared card pattern, record it in the work record before treating it as reusable theme behavior.

## 3. Server, Markup, CPT, ACF, WST, Or WPGB Discrepancies

If browser QA shows a problem CSS cannot legitimately fix, hard stop for that finding: record the defect (URL, selector, expected DOM, observed DOM, console or PHP symptom) as a server blocker in the work record, at most add a clearly marked interim shim, and route back to `wst-new-post-type` for CPT/taxonomy/ACF/WPGB foundation issues or `wst-section-workflow` for Section-level WST/ACF issues. Ask the user for OK before another workflow starts. This is the normal path, not an escalation.

Known non-bug on single URLs: when the CPT single URL renders only the generic theme output and the expected single selector (usually `.wso-<resource>-single`) is absent, the Smart Template assignment for the CPT single view is missing or incomplete (see `wst-new-post-type`, single template foundation) — WST projects never render single partials through the WordPress template hierarchy. That is a pending admin step, not a CSS or template defect: do not start a debugging round; record `injection proof: blocked: smart-template-assignment-pending` for the single surface, hand over to the user with the assignment apply-spec reference, and continue with the other surfaces.

Stale markup, rendered data, generated CSS, WPGB output, or cache state is recorded with URL, selector, expected and observed result; cache flushes go through the bridge (`POST /flush-cache`), not through server commands.

## 4. The Bundled Deploy Pass (Hard Stop)

When the CSS pass is complete across all in-scope surfaces:

1. Commit the tracked CSS/SCSS (plus generated CSS) and the updated work record together on the project branch with the `Made with: SmartFlow` trailer. Set `deploy state: committed-awaiting-push`, `final status: implementation-pass-pending-deploy`.
2. HARD STOP. Hand over with the commit hash, the changed files per surface with real project paths, what the deploy will deliver, and the resume line `report back once you have pushed`. The agent never pushes.

## 5. Bridge Verification With Bounded Retries

After the user reports pushing, verify per the `status-bridge` Rule: `GET /wp-json/wso/v1/status`, `bridge_version` comparison first, then `deployed_commit` against the local `git rev-parse HEAD`. On mismatch re-check at most 3 times with a short wait; if the hashes still differ, abort with a clear message naming the local hash, the served hash (or `null`), and the likely causes (push not done, deploy not run, deploy path not writing `.wso-deployed-commit`), set `deploy state: aborted: hash-mismatch`, and keep results at `implementation pass, deployed verification pending`. Never poll endlessly and never run the served check while the hashes differ. `deployed_commit: null` routes to `install-status-bridge` instead of retrying.

## 6. One-Time Served Check

One confirmation pass through Playwright MCP against the display URL and, when applicable, the single URL:

1. Cached-page reality check: fresh load, measure before any interaction; if the served page is stale, flush through the bridge, reload, re-check, and record the symptom.
2. Confirm the new rules are present in the served stylesheets (stylesheet content, a known new selector, or computed styles without injection). If not, `served check: fail: <symptom>` and stop visual evaluation; re-check the deploy state first.
3. Spot re-verify per surface: the Figma anchors, card count and missing-image handling, filter/pagination/carousel controls, and any matrix row that needed iteration. The full ladder was verified injection-proofed; the served check confirms the serving.
4. On success set `served check: pass`, confirm the matrix row results, and set `final status: final-bridge-verified-pass` (or `-degraded`).

New visual work coming out of the served check goes back to injection-proofed iteration; the tracked changes ride with the next bundled deploy pass.

## 7. Close The Pass

- Write all status fields, per-surface findings, injection proof outcomes and specificity adjustments, served check outcome, cache or deploy symptoms, and remaining risks into the work record. Record Playwright MCP failures with the Capability Probe results, failing step, verbatim error, and next repair action.
- The work record stays in place as the permanent CPT documentation in the docs layer. As the final step, refresh the CPT's docs-layer entry by running the bundled `auto-docs` Skill scoped to this CPT (it uses the bundled `cpt-docs` workflow and preserves the work-record sections).
- An optional project-local Playwright regression command runs when a real harness exists; otherwise document a focused acceptance path and skip reason.

## Concise Example

A `resources` card grid adjustment:

1. The Skill reads `docs/post-types/resources.md`, confirms `.wso-resource-card`, `.wso-resource-grid`, the staging archive URL, WPGB grid/card IDs, and that no public single exists.
2. Preflight passes on the archive URL; existing card and grid patterns are inspected.
3. It injects the planned scoped card and grid variable changes, confirms they win the WPGB and theme cascade without `!important`, and writes the tracked CSS. `injection proof: pass` for card and grid.
4. It runs the viewport ladder injection-proofed, checks card counts, long taxonomy labels, and missing-image fallbacks, and fills the matrix Result cells.
5. One commit (CSS + work record) with the trailer; hard stop; the user pushes.
6. `GET /status` matches `deployed_commit`; the served check finds the new selector served, and the spot checks pass. `final status: final-bridge-verified-pass`; the work record is updated and stays as the CPT documentation.
