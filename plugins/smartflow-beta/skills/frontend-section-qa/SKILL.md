---
name: frontend-section-qa
description: Implement and verify frontend Section CSS/SCSS in the local SmartFlow workspace from a Section work record. Use for Section CSS or SCSS work, the Playwright MCP browser QA loop (navigation, snapshots, screenshots, viewport ladder, selector checks), injection-proofed iteration against the served WordPress page, and the one-time bridge-verified served check after the bundled deploy pass. Consumes and writes back the Section work record in the project docs layer.
---

# Frontend Section QA

This Skill owns the frontend CSS/SCSS pass for a WST Section: the Playwright MCP browser QA loop, injection-proofed iteration against the real served page, tracked CSS/SCSS writes, the Visual QA Targets writeback, and the closing deploy pass with the one-time bridge-verified served check. It does not own WST templates, ACF JSON field groups, Flexible Content wiring, WordPress content, WP Grid Builder setup, or the status bridge itself.

Everything happens in one workspace: the wp-content-level repository checkout. The remote WordPress serves the last deployed commit; local CSS edits never appear on the target URL by saving. That is why this Skill works in two proof modes with a fixed relationship:

- **Injection-proof is the main mode.** All visual iteration happens by injecting the planned rules into the served page through Playwright MCP and verifying them against the real DOM. Tracked source is written once the injected rules win the cascade. Iteration continues injection-proofed on the served page — there is no per-tweak deploy round.
- **The bridge-verified served check is a one-time confirmation.** When the CSS pass is complete, everything goes into one bundled deploy pass (`deploy-and-branches` Rule): commit, hard stop, the user pushes, the deploy delivers the child theme, and the status bridge confirms `deployed_commit` — with the bounded retry and abort budget below. Only then does one served confirmation pass close the work. It is not repeated per tweak.

On every start — direct start included, not only under package orchestration — the CSS/browser-QA run executes through the `cpt-visual-implementer` runner per the `agent-routing` Rule, one runner per Section; the deploy pass, bridge verification, and commit stay with the main chat (runners never commit), and hard stops from the run come back as `OPEN DECISION` in the runner's return format. The QA semantics of this Skill are identical either way.

## Required Starting Point

The contract for this Skill is the Section work record in the project docs layer (default `docs/sections/<section-slug>.md`, or the project convention from `PROJECT-CONTEXT.md`). `wst-section-workflow` creates and fills it, including the `Visual QA Targets` matrix and the `Frontend QA Brief`. There is no separate handoff file.

- If a work record exists: read it, use it as the contract, and write QA results back into it.
- If no work record exists and the user confirms visual-only work: create a minimal work record at the default path with the existing Section identity (template path, layout name, primary class), target URL, Figma/source link, CSS status, and a clear `No template/ACF changes required` note. Keep `<unresolved: ...>` markers instead of inventing values.
- If the task turns out to need template, ACF, Flexible Content, or registration changes: stop and route back to `wst-section-workflow` through the work record.

The Frontend QA Brief is a verifiable starting point, not a blind directive: re-read the Figma link and the rendered page yourself and report contradictions back into the work record instead of silently working around them.

## Status Fields The Skill Maintains

Keep these fields current in the work record; they make the work mode and verification state explicit at every stop.

- `frontend work mode`: `work-record` or `visual-only-minimal-record`.
- `browser access`: `ready`, `degraded: <broken-tool>-fallback-evaluate`, or `blocked: <reason>`.
- `mcp tool defect`: empty, or a short tag plus reason, for example `browser_navigate: URL.canParse missing in MCP runtime`.
- `injection proof`: `pending`, `pass`, `pass-degraded`, `fail`, or `blocked: <reason>`.
- `deploy state`: `not-committed`, `committed-awaiting-push`, `bridge-verified: <commit-hash>`, or `aborted: hash-mismatch`.
- `served check`: `pending`, `pass`, `pass-degraded`, `fail: <symptom>`, or `blocked: <reason>`.
- `final status`: `implementation-pass-pending-deploy`, `final-bridge-verified-pass`, `final-bridge-verified-pass-degraded`, or `blocked`.

`pass-degraded` means Playwright MCP produced the evidence through the Degraded Mode fallback below. It still counts as Playwright MCP evidence; it does not unlock any substitute browser.

## Playwright MCP Preflight

Playwright MCP is the only accepted browser authority for DOM and computed-style inspection, injection proof, screenshots, viewport checks, and the served check. Before the first browser interaction:

1. Read `PROJECT-CONTEXT.md` and the work record for the `playwright_mcp` status. If a project runs parallel Playwright servers, follow the `playwright-browser-claim` Rule before touching a browser.
2. If the status is `ready` and a quick navigation to the target URL works, continue.
3. If the status is missing, `pending`, or broken, run the Playwright MCP step of the bundled `setup-local-project` Skill (Step 10) as the setup/repair target. Do not improvise a workaround here.
4. Run a Capability Probe: `browser_navigate` to the target URL once, then `browser_evaluate` reading `location.href`, `document.title`, and a known Section selector. Record each tool result (`ok`, `failed: <short reason>`, `not-tested`) in the work record.
5. If the whole MCP server is down or both `browser_navigate` and `browser_evaluate` fail: hard stop. Set `browser access: blocked: playwright-mcp-unavailable` and `final status: blocked`, record the symptom, route to `setup-local-project` Step 10.
6. If only individual tools are broken but `browser_evaluate` reaches the target URL, enter Degraded Mode.
7. If a content-level blocker gates the page (login wall, cookie banner, IP allowlist, certificate, headless restriction) although Playwright MCP works, record it and treat browser access as a hard precondition for final CSS writes. The user may share throwaway session logins for Playwright/CDP; never write credentials, cookies, tokens, or session details into the work record or any tracked file.

### No substitute browser

When Playwright MCP cannot run, do not switch to the Cursor Browser, manual user inspection, pasted screenshots, DevTools output, or Chrome Local Overrides. None of these may set `injection proof: pass` or `served check: pass`. The Cursor Browser is allowed only as a read-only diagnostic (documented as `cursor-browser: diagnostic-only - not used for proof/verification`); Chrome Local Overrides remain an optional manual spike in the user's own Chrome when the user explicitly chooses that, and never replace a Playwright MCP proof.

### Degraded Mode for partial tool defects

When a single tool is defective (for example `browser_navigate` throwing `TypeError: URL.canParse is not a function`) while `browser_evaluate` works and reaches the target URL:

- Set `browser access: degraded: <broken-tool>-fallback-evaluate` and record the defect in `mcp tool defect`.
- Navigate through `browser_evaluate` (`location.assign(...)` plus a load/readiness check). DOM, selector, and computed-style reads through `browser_evaluate` are sufficient evidence.
- Inject CSS through `browser_evaluate` (a `<style>` element, `CSSStyleSheet.insertRule`, or `document.adoptedStyleSheets`) and read computed styles back.
- For the served check, fetch the stylesheet URL from the page context (`fetch(...).then(r => r.text())`) and search for the new selector, plus a computed-style spot check.
- Record `pass-degraded` for passes produced this way, document the broken tool, observed error, fallback path, and next repair action, and route the defect to `setup-local-project` Step 10 for a real fix in the next session — even when the user accepts Degraded Mode for the current task.

If `browser_evaluate` itself is broken, leave Degraded Mode and apply the hard stop from the preflight.

## Inputs

Read before editing:

- The Section work record: identity, CSS hooks, selectors to preserve, `Preview URLs`, the `Visual QA Targets` matrix, the Frontend QA Brief, deploy state.
- `PROJECT-CONTEXT.md`: theme tokens, style loader, breakpoints and QA viewport rungs, rem scale, local build command, bridge base URL and credential env var names, working branch.
- The Figma/source design links from the work record (desktop and mobile frame; `no-mobile-design: derived-from-desktop` marks documented interpretation latitude).
- The bundled Rules that gate this pass: `css-guideline` (proof modes, tokens, selectors), `figma-to-code`, `frontend-section-qa-layout-preflight` (layout model extraction and container fit check before the CSS pass), `frontend-section-qa-tablet-band` (full viewport ladder, deliberate 768-991 styling).

If a required value is unresolved, derive it from project context, the Figma source, the real target page, or existing patterns before asking. Ask only when a value is genuinely not derivable and a write would be risky.

## Workflow

```text
Frontend Section QA:
- [ ] Read the Section work record (or create the minimal visual-only record)
- [ ] Read PROJECT-CONTEXT.md and the gating Rules
- [ ] Playwright MCP preflight and Capability Probe; Degraded Mode or hard stop when needed
- [ ] Confirm browser access to the target URL, or stop and ask for login/access
- [ ] Re-read the Figma/source design (desktop and mobile frame)
- [ ] Run the layout preflight (layout model, container fit check, px measurement matrix)
- [ ] Inspect existing Section and theme CSS/SCSS patterns
- [ ] Preview URLs first when the work record lists them; then the real page
- [ ] Browser QA loop: real DOM, matched and computed styles for the target elements
- [ ] Injection-proof the planned rules against the served page (main mode; iterate here)
- [ ] Write tracked CSS/SCSS once the injected rules win the cascade
- [ ] Keep iterating injection-proofed: full viewport ladder, variants, states, text alignment
- [ ] Fill the per-row Result cells of the Visual QA Targets matrix (injection-proofed values)
- [ ] Deploy pass: one commit (CSS + work record) with trailer, HARD STOP, user pushes
- [ ] Bridge check deployed_commit with the bounded retry budget; abort cleanly on mismatch
- [ ] One-time served check: rules present in served stylesheets, cached-page reality check, spot re-verification
- [ ] Final writeback: status fields, QA notes, remaining risks; refresh the Section doc (auto-docs scoped run)
```

## 1. Design, Preflight, And Existing Patterns

Re-read the Figma source from the work record link instead of trusting only the upstream summary: frame, spacing, typography, breakpoints, image behavior, interaction states. Interpret Figma project-conformly (tokens, container widths, button systems, rem scale) and document real deviations as `figma shows X, project pattern enforces Y, implemented as Z`.

Run the layout preflight from the `frontend-section-qa-layout-preflight` Rule before writing CSS: extract the layout model (paradigm, sizes, gaps, min widths, typography), run the container fit check against the project content width, and prepare the px measurement matrix. Never compensate for a global container mismatch inside a Section; record it as a project-level finding.

Check text alignment per text node, not per container: read the text node's own `textAlignHorizontal`/`textAlign` (and vertical alignment in fixed-height rows) from Figma, per breakpoint when it differs, and translate deliberately (`text-align` on the text element; flex alignment only when the whole row moves). Capture computed alignment during DOM inspection and re-verify after injection.

Then inspect existing patterns: nearby Section files, global token/typography/button rules, the style loader registration, and whether the project uses SCSS with generated CSS. Stay inside project-approved frontend paths (`file-edit-boundary` Rule); `functions.php` is forbidden, and server-side WST/ACF/content work is out of scope here.

## 2. Injection-Proofed Iteration (Main Mode)

Work against the served page, which renders the last bridge-verified deployed commit. When the work record lists Section preview URLs (`/section-preview/<section>/<variant>`), use them as the first browser targets: isolated DOM, fixed fixtures, stable QA hooks (`#primary[data-preview-section][data-preview-variant]`, body class `wso-section-preview`, fixture-driven `brand-*` body classes). Iterate variant CSS there, then verify on the real page. If the record says `Preview URLs: n/a (...)`, target the real page directly; setting up preview pages is `wst-section-workflow`/`section-preview-harness` work.

Scope every check to the target Section's chunk: WST projects can render a second global instance of the same Section outside `#primary` (reference-popup clones). Never anchor checks on HTML comments; minifiers strip them.

Core loop:

1. Navigate to the target (preview URL first, then real page). Snapshot to confirm the page rendered.
2. Locate the Section by its primary class or stable landmark; capture matched and computed styles for the elements the planned rules target.
3. Compose the planned rules in the same form they will take in the tracked file and inject them through Playwright MCP or CDP.
4. Re-read computed styles and the visual result. Confirm the rules win the cascade; when they do not, identify the cause (specificity, source order, `!important`, inline style, plugin style) and adjust minimally per the `css-guideline` Rule.
5. Once the injection proof passes, write the rules into the tracked Section CSS/SCSS file: scoped Section variables, project tokens, preserved selectors, style loader registration when required, generated CSS via the project build path when SCSS is used. Set `injection proof: pass`.
6. Continue iterating injection-proofed for every further adjustment. Do not trigger a deploy per tweak; the closing deploy pass collects all tracked changes.

Run the full QA viewport ladder from the `frontend-section-qa-tablet-band` Rule (desktop and mobile Figma anchors ±2px, all intermediate rungs deliberately styled, the 768-991 band with dedicated rules where the red flags appear). Check variants and states: long copy, empty optional fields, repeated items, missing media, hover, focus-visible, active, reduced motion. Fill the per-row `Result` cells of the `Visual QA Targets` matrix as each row is verified injection-proofed; these results carry the note `injection-proofed` until the served check confirms them.

Never leave final visual changes only in browser injections, untracked scratch files, or copied DevTools snippets.

## 3. Server, Markup, ACF, Or WST Discrepancies

If browser QA shows a problem CSS cannot legitimately fix (broken markup contract, template output, field formatting), hard stop for that finding:

- Do not edit PHP, ACF, WST templates, content, or shortcodes from this Skill.
- Record the defect in the work record as a server blocker with URL, selector, expected DOM, observed DOM, and any console or PHP symptom.
- At most, implement a clearly marked interim shim that stays inert once the markup is fixed, and document it as interim.
- Route back to `wst-section-workflow`; ask the user for OK before another workflow is started from this context. This re-routing is the normal path, not an escalation.

## 4. The Bundled Deploy Pass (Hard Stop)

When the CSS pass is complete — injection proof passed, ladder verified, matrix rows filled — bundle everything into one deploy pass per the `deploy-and-branches` Rule:

1. Commit the tracked CSS/SCSS (plus generated CSS) and the updated work record together on the project branch, with the `Made with: SmartFlow` trailer. Set `deploy state: committed-awaiting-push` and `final status: implementation-pass-pending-deploy`.
2. HARD STOP. Hand over with: the commit hash, the changed files with real project paths, a one-line statement of what the deploy will deliver, and the resume line `report back once you have pushed`. The agent never pushes.
3. The user pushes; the project deploy path delivers the child theme subdirectory and writes `.wso-deployed-commit`.

## 5. Bridge Verification With Bounded Retries

After the user reports pushing, verify the deploy over the status bridge per the `status-bridge` Rule:

1. `GET /wp-json/wso/v1/status` (credentials only through the project env vars). First compare `bridge_version` against the bundled template; on mismatch stop bridge usage and route to `install-status-bridge`.
2. Compare `deployed_commit` with the local `git rev-parse HEAD`. Equal hashes (or the served hash being the abbreviation of the local one) mean the deploy landed.
3. When the hashes do not match yet: re-check at most 3 times with a short wait between checks. If they still differ, abort with a clear message naming the local hash, the served hash (or `null`), and the most likely causes — push not done, deploy not run, deploy path not writing `.wso-deployed-commit`. Set `deploy state: aborted: hash-mismatch` and keep `final status: implementation-pass-pending-deploy`. Never poll in an endless loop, and never run the served check or record any served result while the hashes differ.
4. A `deployed_commit` of `null` means the deploy path does not write the commit file; route to the `install-status-bridge` Skill instead of retrying.

On match, set `deploy state: bridge-verified: <hash>` and continue.

## 6. One-Time Served Check

One confirmation pass against the target URL, through Playwright MCP only:

1. Cached-page reality check first: preview pages are nocache by design and hide stale page cache and delayed JS (WP Rocket Delay-JS). Load the real page fresh and measure before any interaction; full-height rules relying on JS-set custom properties need CSS fallbacks (`var(--vh, 1svh)`), and the pre-interaction state is the proof. If the served page is stale, flush through the bridge (`POST /flush-cache`), reload, and re-check; record the symptom.
2. Confirm the new rules are actually present in the served stylesheets (loaded stylesheet content, a known new selector, or computed styles without injection). If they are not, record the symptom (deploy not reflected, cache stale, wrong file delivered) as `served check: fail: <symptom>` and stop visual evaluation; re-check the deploy state before anything else.
3. Spot re-verify the visual result: the Figma anchors (desktop and mobile), any matrix row that needed iteration, and the interaction states that JS or caching could affect. The full ladder was already verified injection-proofed; the served check confirms the serving, it does not restart the iteration.
4. On success set `served check: pass`, update the matrix row results from `injection-proofed` to confirmed, and set `final status: final-bridge-verified-pass` (or `-degraded` when Degraded Mode produced the evidence).

If new visual work comes out of the served check, it goes back to injection-proofed iteration (Section 2); the resulting tracked changes ride with the next bundled deploy pass.

## 7. Close The Pass

- Write all status fields, browser QA findings (viewports checked, selectors confirmed, screenshots when the project uses them), injection proof outcome and specificity adjustments, served check outcome and cache/deploy symptoms, and remaining risks into the work record. When Playwright MCP failed at any step, record the Capability Probe results, the failing step, the verbatim error, and the concrete next repair action.
- The work record stays in place as the permanent Section documentation in the docs layer; there is no handoff file to delete. As the final step, refresh the Section's docs-layer entry by running the bundled `auto-docs` Skill scoped to this Section, which preserves the work-record sections and updates the documentation sections around them.
- An optional project-local Playwright regression command from `PROJECT-CONTEXT.md` runs when a real harness exists; otherwise document a focused acceptance path and the skip reason.

## Concise Example

A `feature-cards` spacing adjustment:

1. The Skill reads `docs/sections/feature-cards.md`, confirms `.wso-section-feature-cards`, the preview URLs, and the filled Visual QA Targets matrix.
2. Preflight passes; the preview URL for `default` is the first target. Layout preflight finds no container mismatch.
3. It captures computed styles, injects the planned scoped-variable changes, confirms they win the cascade without `!important`, and writes the tracked CSS. `injection proof: pass`.
4. It runs the viewport ladder injection-proofed, styles the 768-991 band deliberately, and fills the matrix Result cells.
5. One commit (CSS + work record) with the trailer; hard stop; the user pushes.
6. `GET /status` matches `deployed_commit` on the second re-check; `deploy state: bridge-verified`.
7. The served check finds the new selector in the served stylesheet, the pre-interaction state is correct, the anchors match. `final status: final-bridge-verified-pass`; the work record is updated and stays as the Section documentation.
