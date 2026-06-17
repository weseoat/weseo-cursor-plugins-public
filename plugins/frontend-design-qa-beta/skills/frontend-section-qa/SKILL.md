---
name: frontend-section-qa
description: Implement and verify a local frontend Section from a filled Section handoff. Use when doing CSS or SCSS Section work, a Playwright MCP browser QA loop for navigation, snapshots, screenshots, viewports, and selector checks, optional Chrome Local Overrides spikes, responsive visual QA, or optional project-local Playwright regression acceptance against a dev or staging WordPress URL.
---

# Frontend Section QA

This Skill is a productive QA and CSS/SCSS implementation workflow with safety stops, not a strict handoff gate. Discovery and reads are always allowed. Writes proceed when scope is clear, browser access works, and an injection-proof against the real WordPress page passes. The Skill stops only at concrete risk points: missing browser access, server-side discrepancies, or pending Git pull/deploy.

Use this Skill for the local frontend phase of any Section. When the WST Builder server phase has produced a filled Section handoff, that handoff is the contract. When the task is visual-only and no handoff exists, the Skill creates a temporary mini-handoff and works from there.

When a CPT display becomes primarily a WST Section layout, use this Skill for the Section-level layout behavior only. Keep CPT card, archive/grid, carousel/filter, WP Grid Builder output, and optional single-template QA in the filled CPT handoff through `cpt-frontend-qa`.

This Skill owns local CSS or SCSS implementation, the Playwright MCP browser QA loop, CSS-injection proof against the real target URL, responsive checks, optional project-local Playwright regression acceptance, and Section handoff QA writeback. It does not own server-side WST templates, ACF field groups, WordPress content, WP Grid Builder setup, WP-CLI, cache execution, deployment, or Remote-SSH setup. Playwright MCP setup itself is owned by `setup-playwright-mcp` in this plugin.

## Required Starting Point

Always ask first whether a Section handoff or `handoff.md` exists for this task. The handoff is tracked in Git and flows from the Remote-SSH WordPress workspace (where WST Builder writes it) to the local frontend workspace through a normal commit/push/pull cycle. A missing handoff usually means the local workspace has not pulled the latest commit yet, or WST Builder created the handoff but has not committed and pushed it.

- If a handoff exists locally: read it, use it as the contract, and write QA results back into it.
- If the user is unsure whether a handoff exists: run `git pull` (or `git fetch` plus a check of the active branch) before deciding the handoff is missing, then search the project-configured handoff storage location from `PROJECT-CONTEXT.md`, and ask again with the findings.
- If the user confirms `visual-only` work without a handoff: create a temporary mini-handoff (see below) and continue.
- If the task turns out to need server-side ACF/WST/template changes: stop and route back to `wst-section-workflow`.

WST Builder owns the reusable Section handoff template at `plugins/wst-builder/handoffs/section-handoff.template.md`. The filled handoff itself lives at the project-configured storage location from Project Context.

## Mini-Handoff For Visual-Only Without Existing Handoff

When the user confirms visual-only work without an existing handoff, create a temporary mini-handoff in the project-configured handoff storage location from `PROJECT-CONTEXT.md`. If the location is unknown, ask once.

- Filename: `<section-or-page-slug>-visual-only-handoff.md`.
- If the slug is unclear, derive a slug from the target page or Section selector and ask for a one-line confirmation before writing the file.
- Use the same `section-handoff.template.md` as a base. Fill what is known and keep `<unresolved: ...>` markers for the rest.
- Treat the mini-handoff as the live work protocol for the rest of the task.

## Status Fields The Skill Maintains

The Skill updates these status fields in the active handoff or mini-handoff. They make the work mode and verification state explicit at every stop.

- `frontend work mode`: `handoff` or `visual-only-mini-handoff`.
- `browser access`: `ready`, `degraded: <broken-tool>-fallback-evaluate`, or `blocked: <reason>`.
- `mcp tool defect`: empty, or a short tag plus reason, for example `browser_navigate: URL.canParse missing in MCP runtime`.
- `proof mode`: `injection-proof` or `source-served`.
- `injection proof`: `pending`, `pass`, `pass-degraded`, `fail`, or `not-needed`.
- `delivery path`: `direct-local-serving`, `auto-deploy-available`, `git-pull-required`, or `unknown`.
- `server pull/deploy`: `not-needed`, `pending`, `user-confirmed`, or `not-reflected`.
- `source-served verification`: `pending`, `pass`, `pass-degraded`, `fail`, or `blocked`.
- `final status`: `implementation-pass-pending-deploy`, `final-source-served-pass`, `final-source-served-pass-degraded`, or `blocked`.

`pass-degraded` and `final-source-served-pass-degraded` mean Playwright MCP produced the evidence, but through the Capability Probe fallback documented under Degraded Mode, not through the standard tool chain. They still count as Playwright MCP evidence; they do not unlock a Cursor Browser fallback.

## Playwright MCP Preflight

Browser QA in this Skill runs through Playwright MCP in the local Cursor workspace. Playwright MCP is the only accepted browser authority for DOM and computed-style inspection, CSS-injection proof, screenshots, viewport checks, and source-served verification. Before the first browser interaction:

1. Read `PROJECT-CONTEXT.md` and the active handoff or mini-handoff for the `playwright_mcp` status.
2. If the status is `ready` and a quick Playwright MCP navigation to the target URL still works, continue.
3. If the status is missing, `pending`, or unverified for this local workspace, run `setup-playwright-mcp` first as the setup/repair target. Do not improvise a workaround in this Skill.
4. Run a Capability Probe before any QA work: try `browser_navigate` against the target URL once, then `browser_evaluate` to read `location.href`, `document.title`, and a known Section selector. Record each tool result as `ok`, `failed: <short reason>`, or `not-tested` in the active handoff or mini-handoff. The result drives the next step.
5. If the entire MCP server fails (process down, red in `Settings -> Tools & MCP`, no browser tools listed) or both `browser_navigate` and `browser_evaluate` fail, hard stop. Set `browser access: blocked: playwright-mcp-unavailable` and `final status: blocked`, record the symptom, and route back to `setup-playwright-mcp` for repair.
6. If only individual tools are broken but at least `browser_evaluate` works and reaches the target URL through a documented fallback, switch to Degraded Mode (see Degraded Mode For Partial Playwright MCP Tool Defects). Do not call this a workaround that bypasses Playwright MCP; it is still Playwright MCP, just through a smaller tool surface.
7. If a content-level blocker prevents browser access (login wall, cookie banner, IP allowlist, self-signed cert, headless restriction) even though Playwright MCP itself works, record the blocker in the handoff and treat browser access as a hard precondition for final CSS writes (see Browser Access Safety Stop).

Never configure Playwright MCP inside a Remote-SSH workspace from this Skill. Route that back to `setup-playwright-mcp` in the local frontend workspace.

## No Cursor Browser Fallback

When Playwright MCP is unavailable, broken, or cannot navigate to the target URL, this Skill must not silently switch to a substitute browser. Substitutes include the Cursor Browser, manual browser inspection by the user, user-pasted screenshots, raw DevTools console output, and Chrome Local Overrides.

- None of these substitutes may set `injection proof: pass` or `source-served verification: pass`. Final QA status passes only through Playwright MCP evidence.
- The Cursor Browser may be used only as a read-only diagnostic check, for example to confirm whether the target URL loads at all in a different browser context. Any such check must be documented in the handoff with the explicit note that it was diagnostic only and did not contribute to the proof or final verification status.
- Chrome Local Overrides remain an optional manual spike in the user's own Chrome, when the user explicitly chooses that path. They never replace Playwright MCP for proof or final verification.

If Playwright MCP cannot be brought up, the next action is always `setup-playwright-mcp`, not a different browser. If the user wants to force a Cursor Browser run, document that as `cursor-browser: diagnostic-only - not used for proof/verification` and keep `final status: blocked` until Playwright MCP works again.

## Degraded Mode For Partial Playwright MCP Tool Defects

Playwright MCP exposes several tools (`browser_navigate`, `browser_evaluate`, `browser_screenshot`, `browser_set_viewport`, etc.). Sometimes a single tool is defective while the rest works, for example when the MCP runtime is missing modern JS APIs and `browser_navigate` throws `TypeError: URL.canParse is not a function` even though Chromium and `browser_evaluate` are fine. In that case the Skill enters Degraded Mode instead of hard-stopping.

Degraded Mode rules:

- Degraded Mode requires that `browser_evaluate` works and that it can reach the target URL. Set `browser access: degraded: <broken-tool>-fallback-evaluate` and record the concrete defect in `mcp tool defect`, for example `browser_navigate: URL.canParse missing in MCP runtime`.
- `browser_evaluate` based navigation is the only sanctioned fallback. Use it explicitly, for example `await new Promise(r => { addEventListener('load', r, { once: true }); location.assign('<target>'); })` or a `location.href = ...` plus readiness check.
- `browser_evaluate` based DOM, selector, and computed-style reads are sufficient for the CSS-injection proof.
- CSS-injection through `browser_evaluate` is allowed: insert a `<style>` element, use `CSSStyleSheet.insertRule`, or `document.adoptedStyleSheets`. After insertion, read computed styles back through `browser_evaluate`.
- Source-served verification in Degraded Mode reads the served stylesheet content through `browser_evaluate`, for example by fetching the stylesheet URL via `fetch(...).then(r => r.text())` from the page context and searching for the new selector, plus a computed-style spot check.
- Set `injection proof: pass-degraded` and `source-served verification: pass-degraded` when those passes succeed through `browser_evaluate`. Final status can become `final-source-served-pass-degraded`. These statuses are still Playwright MCP evidence; they remain off-limits to Cursor Browser, manual checks, and screenshots.
- Document Degraded Mode explicitly in the handoff or mini-handoff, including the broken tool, the observed error, the fallback path used, and the next action (`run setup-playwright-mcp`, file an MCP defect, pin or upgrade the MCP package, verify Node 20 plus for the MCP runtime).
- If the user accepts Degraded Mode for the current task, that is fine, but the Skill must still route the defect back to `setup-playwright-mcp` so the underlying MCP issue gets a real fix in the next session.

If `browser_evaluate` itself is broken, or the target URL cannot be reached through the fallback, leave Degraded Mode and apply the hard stop from the Playwright MCP Preflight.

## Browser Access Safety Stop

Final visual CSS or SCSS writes require real browser access to the target URL. WordPress with a theme and WST on top has too many overrides to style blindly.

- If the target page needs login, cookie consent, basic auth, IP allowlist, or any other gating that Playwright MCP cannot pass, stop and ask for browser access or session login.
- Throwaway or placeholder login data may be shared by the user for the current session. Use it only to log in through Playwright/CDP.
- Do not write login credentials, cookies, tokens, or session details into the handoff, the mini-handoff, project notes, screenshots, console logs, diagnostics, or any tracked file.

Before the stop, the Skill may still:

- Create or update a mini-handoff.
- Read project context and existing CSS or SCSS patterns.
- Re-read the Figma source.
- Identify likely target files for the CSS or SCSS change.
- Prepare a draft of the planned rules.

## Inputs

Read these before editing:

- Section handoff or mini-handoff path, storage location, and current status fields.
- Target dev or staging URL.
- Preview URLs (one per variant) when the handoff lists them, or `n/a (no preview pages)` / `n/a (declined)`. When present, they are the first browser targets.
- Section slug, layout name, primary section class, wrapper classes, and selectors to preserve.
- Template, WST, ACF, and CSS or SCSS file references.
- The `Visual QA Targets` matrix: viewport mapping plus one row per verifiable expectation (variant, viewport, expectation, result).
- Original Figma or source design references (desktop and mobile frame; `no-mobile-design: derived-from-desktop` means documented interpretation latitude for mobile).
- Local Playwright MCP status from `PROJECT-CONTEXT.md` and the handoff, including any browser access blocker.
- Project context for theme tokens, style loader, local build command, optional project-local Playwright command, viewport conventions, Git workflow, and branch or PR.

If a required value is unresolved, prefer to derive it from project context, the Figma source, the real target page, or existing patterns before asking the user. Stop and ask only when a value is genuinely not derivable and a write would be risky.

## Workflow

Track progress with this checklist:

```text
Frontend Section QA:
- [ ] Ask whether a handoff exists; create a mini-handoff for visual-only work without one
- [ ] Read project context and the active handoff or mini-handoff
- [ ] Confirm Playwright MCP is ready locally or run setup-playwright-mcp
- [ ] Run the Capability Probe (browser_navigate, browser_evaluate, and the planned tools) and record per-tool status
- [ ] If the MCP server is fully down or both navigate and evaluate fail, hard stop; do not fall back to Cursor Browser; route to setup-playwright-mcp
- [ ] If only individual tools are broken but browser_evaluate reaches the target, enter Degraded Mode and document the defect
- [ ] Confirm browser access to the target URL, or stop and ask for login/access
- [ ] Re-read the Figma or source design when a link is available
- [ ] Inspect existing Section and theme CSS or SCSS patterns
- [ ] When the handoff lists preview URLs, use them as the first browser targets, then verify on the real page
- [ ] Drive a Playwright MCP browser QA loop and capture real DOM, matched and computed styles
- [ ] Run a CSS-injection proof of the planned rules against the real target URL through Playwright MCP only
- [ ] Implement final CSS or SCSS in tracked local files when the injection proof passes
- [ ] Detect the delivery path; default to git-pull-required when no auto-deploy exists
- [ ] On delivery path `git-pull-required` or `unknown`, stop with implementation-pass-pending-deploy and wait for user confirmation
- [ ] After user confirms server pull or deploy, verify that the new CSS rules are actually served before re-checking visuals
- [ ] Re-run the Playwright MCP browser loop at desktop, tablet, and mobile viewports
- [ ] Verify each Visual QA Targets row at its viewport and write the per-row Result (pass/fail: note) into the matrix
- [ ] Run the optional project-local Playwright regression command when a real harness exists
- [ ] Record stale-cache or server-output symptoms without running server commands
- [ ] Stop and document any server, markup, ACF, or WST discrepancy as a server blocker; route to wst-section-workflow
- [ ] Update the handoff QA notes and status fields
- [ ] Commit code and handoff updates on the same branch or PR
- [ ] On full completion, write a short permanent project note and keep the active handoff or mini-handoff in place until the page goes live (no `git rm` on QA pass; remove only after Go-Live)
```

## 1. Confirm The Work Mode

Ask the start question and set `frontend work mode`:

- If the user provides a handoff: `frontend work mode = handoff`.
- If the user confirms visual-only and no handoff exists: `frontend work mode = visual-only-mini-handoff`.

For `handoff` mode, confirm the handoff contains: Page URL, source design references (desktop and mobile), template file and CSS file, WST template and ACF references, primary section class and wrapper hooks, selectors that templates or tests rely on, a filled `Visual QA Targets` matrix (all base variants answered or marked `n/a`), storage location, local frontend responsibilities, QA notes, cache state, known risks, and open questions. An older handoff may still carry the legacy `Expected Visual Behavior` prose table instead of the matrix; treat that as the contract but record the missing matrix as a known risk.

For `visual-only-mini-handoff` mode, ensure the mini-handoff captures at minimum: target URL, Section selector or page anchor, original Figma or source link, CSS or SCSS target path or a discovery note, expected visual behavior, and a clear statement that no server-side ACF/WST/PHP changes are expected.

If the current project provides a Section handoff validator, run that project-local command on a real handoff. In this development repository, the optional validator is:

```sh
python scripts/validate-section-handoffs.py
```

Do not require that command in installed plugin consumers, and do not run it against a mini-handoff.

## 2. Re-Read The Figma Source

When the active handoff or mini-handoff carries a Figma link, re-read it from this Skill instead of trusting only the upstream summary.

- Pull the relevant frame, spacing, typography, breakpoints, image behavior, and interaction states yourself.
- Compare what Figma shows against what the rendered target page shows.
- Interpret Figma project-conformly: respect established project tokens, typography, container widths, button systems, breakpoints, and rem scale.
- Document real Figma-vs-project deviations as `figma shows X, project pattern enforces Y, implemented as Z`.
- If the Figma link is not accessible, fall back to the screenshot or brief in the handoff and record the limitation.

### Text alignment is not optional

Text alignment is a frequent regression source and must be inspected per text node, not assumed from the surrounding container. For every text element in scope (headlines, sub-headlines, eyebrows, body copy, list items, button labels, captions, micro-copy):

- Read the text node's own `textAlignHorizontal` / `textAlign` from Figma: `LEFT`, `CENTER`, `RIGHT`, or `JUSTIFIED`. Do not infer alignment from the parent's auto-layout alignment; in Figma a centered auto-layout often holds left-aligned text, and a left-aligned auto-layout often holds centered text.
- Read the text node's `textAlignVertical` / vertical alignment when the node sits in a fixed-height container or a card row (`TOP`, `CENTER`, `BOTTOM`).
- Check whether the alignment changes across breakpoints (desktop, tablet, mobile). Record per-breakpoint values when they differ.
- Translate Figma alignment to CSS deliberately: `text-align`, `align-items` or `justify-content` on the text wrapper (not the text node) when needed, and `align-self` on the text node when only one item flips. Center text via `text-align: center` on the text element, not by changing a flex container's `align-items` unless the whole row should move.
- When existing CSS or theme tokens set a default text alignment that contradicts Figma, override it explicitly at the Section scope; do not silently inherit a wrong default.
- During the Playwright MCP DOM inspection in step 4, capture the computed `text-align` and, where relevant, `align-items` / `justify-content` / `align-self` for every text node in scope. Re-verify after the CSS-injection proof and again under Source-Served Verification.
- Document any Figma-vs-rendered alignment deviation as `figma text X aligned <value>, rendered as <value>, fixed by <selector + property>`.

## 3. Inspect Existing Frontend Patterns

Before writing new CSS:

- Read nearby Section files with similar layout or components.
- Check global token, typography, button, background, and image rules.
- Check style loader registration such as `styles.json` or the project's equivalent.
- Check whether the project uses SCSS as an authoring layer and generated CSS as the runtime file.

Do not restructure the theme or move files outside the paths approved by project context. Do not perform server-side WST, ACF, WordPress content, WP Grid Builder, WP-CLI, cache execution, deployment, or Remote-SSH work during local frontend QA. Do not edit PHP bootstrap or MU plugin files. `functions.php` is forbidden for agent edits; `theme-functions.php` and MU plugin files require explicit prior user confirmation and should be routed to the appropriate server-side phase.

## 4. Real DOM And Specificity Inspection

Use Playwright MCP against the real target URL to capture the evidence that local CSS must work against. Specificity is checked at the affected selector, not globally.

- Locate the Section by its primary class or stable landmark.
- Read the actual DOM structure of the Section that will be styled.
- Capture matched CSS rules and computed styles for the affected elements before any change.
- Note theme or plugin selectors that compete with the target rules.

Do not try to analyse the entire theme cascade. Stay on the elements that the planned rules target.

## 5. CSS-Injection Proof

When the planned CSS or SCSS lives in local files that will reach the server only through Git pull or deploy, prove the rules against the real page through injection before writing them to tracked source. The injection proof is only valid when executed through Playwright MCP; Cursor Browser, manual user checks, and screenshots do not satisfy it.

- Compose the planned rules in the same form they will take in the tracked file.
- Inject them temporarily into the rendered target page through Playwright MCP or its CDP channel, for example as a `<style>` element or a CSS rule insertion.
- Re-read computed styles and the visual result for the affected elements through Playwright MCP.
- Confirm the rules win the cascade. If they do not, document the cause: higher specificity, later source order, `!important` rule, inline style, plugin style.
- Adjust the rules with minimally stronger or scoped selectors. Use `!important` only when the theme or plugin pattern leaves no better option.

Set `injection proof` to `pass`, `fail`, or `not-needed` in the active handoff or mini-handoff. Do not set `pass` based on Cursor Browser, manual inspection, or user-supplied screenshots; if Playwright MCP cannot run the proof, set `injection proof: blocked: playwright-mcp-unavailable` (or the navigation reason) and stop, see No Cursor Browser Fallback.

Chrome Local Overrides are not the default proof mechanism. Playwright MCP runs in its own browser context without your logged-in session and without your Chrome user profile. Treat Chrome Local Overrides as an optional manual spike in the user's real browser, when the user explicitly chooses that path and confirms session and override availability. They never replace a Playwright MCP injection proof.

## 6. Implement Tracked CSS Or SCSS

After the injection proof passes, write the final rules into tracked project source files.

- Use scoped Section variables for local spacing, sizing, and behavior.
- Reuse project theme tokens and classes where they match the design.
- Preserve handoff selectors.
- Register new CSS files in the project style loader when required.
- If using SCSS, update generated CSS through the project build command or documented manual compile path.

Decide automatically when the Section selector, target file, scope, and risk are clear. Ask only when a new CSS file would be created, a loader entry must be added, multiple plausible target files exist, or Figma and the theme system conflict in a way that needs a decision.

Never leave final visual changes only in browser overrides, untracked scratch files, or copied DevTools snippets.

## 7. Delivery Path Detection And Server Pull Stop

The Skill detects the delivery path itself from project context and Git workflow. The default assumption is Git-based delivery without direct server sync.

Set `delivery path` to one of:

- `direct-local-serving`: the local CSS file is loaded directly by the target page (rare for a Remote WordPress).
- `auto-deploy-available`: a documented sync, build, or deploy command runs from this workspace and reaches the server.
- `git-pull-required`: the change reaches the server only after a commit, push, and a Git pull or deploy on the server side.
- `unknown`: detection could not confirm a path.

When `delivery path` is `git-pull-required` or `unknown`, stop after writing the local files. This stop is a hard handoff to the user; do not run source-served verification before the user confirms back. Vague phrases like "please deploy" are not enough at this point. Spell out exactly what the user has to do, in plain language.

Set `server pull/deploy = pending` and post a handoff message that contains:

1. A one-line status: `implementation pass; waiting for server pull/deploy`.
2. A short list of the local files that were just changed and need to reach the server. Name them with their real project paths. Include any generated CSS or built artifact if the project uses SCSS.
3. The exact commit and push commands the user has to run in this local frontend workspace, using the project's conventions. Default shape, adapted to the project's Git policy and branch:
   ```sh
   git status
   git add <changed-files>
   git commit -m "FEATURE - <section slug>: <short description>"
   git push origin "$(git branch --show-current)"
   ```
4. The exact server-side step the user has to do next, named for the project. Pick the correct one and only mention that one to avoid confusion:
   - For projects where the WordPress server itself pulls via WP Pusher or a deploy hook: tell the user to open their Remote-SSH server workspace and run `git fetch origin` plus `git pull` (or to confirm WP Pusher has deployed the push), then to run the project's documented cache flush from `PROJECT-CONTEXT.md` (default `php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"`).
   - For projects with an auto-deploy or push-to-deploy setup: tell the user to confirm the deploy completed for the just-pushed commit and to confirm the documented cache flush ran.
5. A clear `come back when ...` line that tells the user how to resume: `come back when the new CSS file is reachable at <stylesheet URL or expected selector on the target page>`.
6. A note that the user does not have to start a new chat. A short `deployed and cache flushed` (or `deploy confirmed`) in the same conversation is enough. The Skill then continues with Source-Served Verification.

Do not continue source-served verification before the user actually confirms back. If the user reports an error during the deploy or cache flush (login wall, push rejected, cache flush failure, deploy job failed), record it on the active handoff and route the action to `wordpress-server-ops` or to the project's documented server step instead of guessing.

## 8. Source-Served Verification

Only after the user confirms `server pull/deploy = user-confirmed`, run the source-served verification pass against the target URL. Source-served verification is only valid through Playwright MCP; Cursor Browser, manual inspection, and screenshots do not satisfy it.

### Cached-page reality check (mandatory before the final pass)

Preview pages are nocache by design and therefore hide two bug classes that only exist on real pages: stale page cache and delayed JS (for example WP Rocket Delay-JS). Before any final pass on the real page:

1. Load the real page fresh and measure BEFORE any interaction (pre-interaction state). Theme JS that sets custom properties like `--vh` may not have run yet, so full-height rules need CSS fallbacks (`var(--vh, 1svh)`), and the pre-interaction measurement is the proof.
2. If the served page is stale, stop visual evaluation, record the symptom, and request the server-side cache flush (the `PROJECT-CONTEXT.md` command). Cache flushing on the server is not part of the local phase.

- Navigate to the target URL with a fresh Playwright MCP load.
- Confirm that the new CSS file or rules are actually present in the served stylesheets, for example by inspecting the loaded stylesheet content, a known new selector, or computed styles without injection.
- If the new rules are not served, set `server pull/deploy = not-reflected`, document the symptom (deploy not reflected, cache stale, wrong file delivered, theme override late), and stop visual evaluation. Route cache flush, WP-CLI, deployment, or server repair back to WordPress Server Ops or the project's `PROJECT-CONTEXT.md` cache guidance.
- If the new rules are served, continue with responsive and interaction checks through Playwright MCP and set `source-served verification = pass` when the visual behavior matches expectations.
- If Playwright MCP cannot run this pass, set `source-served verification: blocked: playwright-mcp-unavailable` (or the navigation reason) and route back to `setup-playwright-mcp`. Do not mark `source-served verification = pass` based on Cursor Browser or screenshots.

Final status follows the verification:

- `implementation-pass-pending-deploy` when local files and injection proof are in place but the deploy has not been confirmed or not reflected.
- `final-source-served-pass` when source-served verification passed.
- `blocked` when a server, markup, ACF, or WST discrepancy stops the work.

## 9. Responsive And Interaction Checks

After source-served verification passes, check the Section against the target URL:

- Desktop, tablet, and mobile sizes from the handoff or mini-handoff.
- Long copy, empty optional fields, repeated items, and missing images.
- Hover, focus-visible, active, loading, and disabled states that apply.
- Reduced motion or contrast preferences when the Section has motion or color-sensitive UI.
- Cache state if the rendered page does not reflect the served CSS.

Update the handoff QA notes with the result and remaining risks.

If markup, rendered data, generated CSS, or cache state looks stale, record the URL, selector, expected result, observed result, and local checks already performed.

## 10. Server, Markup, ACF, Or WST Discrepancies

If browser QA shows that the problem is not solvable in CSS, hard stop.

- Do not edit PHP, ACF, WST templates, content, shortcodes, or WP-CLI from this Skill.
- Record the observed defect with URL, selector, expected behavior, real DOM, and any console or PHP symptom in the handoff or mini-handoff as a server blocker.
- Route back to `wst-section-workflow`.
- Ask the user for OK before another skill or server workflow is started from this context.

When QA reveals a defect that CSS cannot legitimately fix (broken markup contracts, template output, field formatting), do not absorb it silently into CSS workarounds:

1. Implement at most a clearly marked interim shim that stays inert once the markup is fixed, and document it as interim.
2. Record the blocker in the handoff with evidence (URL, selector, observed DOM, expected DOM) and route it to `wst-section-workflow`.
3. Keep the frontend phase result honest: a pass "via interim shim" is documented as exactly that until the server fix lands.

This re-routing is the normal workflow path, not an escalation.

## 11. Playwright MCP Browser QA Loop

Playwright MCP is the primary browser-driving mechanism for Section QA. After the preflight confirms `playwright_mcp: ready` and browser access works, run a focused loop against the target URL.

### Preview pages first

When the handoff lists Section preview-page URLs (`/section-preview/<section>/<variant>`, the preview harness from `wst-builder`), use them as the first browser targets: isolated DOM, fixed fixture data, and stable QA hooks (`#primary[data-preview-section][data-preview-variant]`, body class `wso-section-preview`, fixture-driven `brand-*` body classes for palette variants). Iterate variant CSS there (injection-proof), then verify on the real page.

Scope every check to the target Section's chunk: WST projects can render a second global instance of the same Section outside `#primary` (for example reference-popup clones). Never anchor checks on HTML comments; minifiers strip them from the served output, so use real tags or data attributes.

If the handoff records `Preview URLs: n/a (no preview pages)` or `n/a (declined)`, skip this and target the real page directly. Do not set up preview pages from the local phase; that is `wst-builder` server work.

Core loop:

1. Navigate to the target URL.
2. Take an accessibility or DOM snapshot to confirm the page rendered without error.
3. Locate the Section by its primary class or stable landmark.
4. Capture matched and computed styles for the elements the planned rules target.
5. Inject planned CSS through Playwright or CDP as the injection proof; iterate until the rules win the cascade.
6. Switch to desktop, tablet, and mobile viewports from the handoff and re-check key content and behavior.
7. Capture screenshots only when the project workflow uses screenshots for review or handoff QA notes.

If a browser access blocker appears, record the URL, step, observed message, whether the same URL loads in a regular browser, and the suggested next action in the handoff, then apply the Browser Access Safety Stop.

## 12. Optional Project-Local Playwright Regression

Run the project's Playwright command as an optional persistent regression check when `PROJECT-CONTEXT.md` or the handoff provides a real project-local harness. If the project has no test yet, document a focused acceptance path and skip reason in the handoff.

Generic shape example:

```ts
test("feature cards section renders responsively", async ({ page }) => {
  await page.goto(process.env.SECTION_URL!);
  const section = page.locator(".wso-section-feature-cards");

  await expect(section).toBeVisible();
  await expect(section.locator(".wso-feature-card")).toHaveCount(3);

  await page.setViewportSize({ width: 390, height: 844 });
  await expect(section).toBeVisible();
});
```

Treat this as a shape example. Use the project's test runner, environment variables, locators, and viewport list.

## 13. Update The Handoff

Write QA notes back to the same handoff or mini-handoff that started the local phase. Include:

- The per-row `Result` cells of the `Visual QA Targets` matrix (`pass` or `fail: note` per row). The aggregate QA result field carries only the overall status (for example `implementation pass, deployed verification pending`); a `fail` row names the broken expectation directly.
- All status fields from Status Fields The Skill Maintains.
- Playwright MCP browser QA findings: viewports checked, selectors confirmed, screenshots captured when applicable.
- Injection proof outcome and any specificity adjustments.
- Source-served verification outcome and cache or deploy symptoms.
- Responsive findings for desktop, tablet, and mobile expectations.
- Optional project-local Playwright regression result or documented skip reason.
- Implementation notes for changed CSS or SCSS files and generated CSS when applicable.
- Remaining risks, open questions, route-back owner when action is needed, and confirmation that any Chrome Local Overrides spike was discarded or copied into tracked source.

When Playwright MCP itself failed or could not navigate, record the blocker explicitly:

- Playwright MCP status at the time of failure (`unavailable`, `no-tools`, `navigation-failed: <reason>`, or `degraded: <broken-tool>-fallback-evaluate`).
- Capability Probe result per tool (`browser_navigate`, `browser_evaluate`, `browser_screenshot`, `browser_set_viewport`, others) with `ok`, `failed: <short reason>`, or `not-tested`.
- The step that failed (preflight navigation, injection proof, source-served verification, viewport pass).
- Observed error message or symptom, copied verbatim when short (for example `TypeError: URL.canParse is not a function`).
- Whether the target URL was checked diagnostically through the Cursor Browser; if yes, note `cursor-browser: diagnostic-only - not used for proof/verification` and what was observed.
- Whether Degraded Mode was used; if yes, name the fallback path (for example `browser_evaluate location.assign + getComputedStyle` and `fetch stylesheet text` for source-served).
- Next action: `run setup-playwright-mcp`, `install Node.js LTS locally and restart Cursor`, `file MCP defect issue and pin/upgrade @playwright/mcp`, `provide session login`, `request IT allowlist`, or other concrete repair step.
- Keep `final status: blocked` while the Playwright MCP blocker is unresolved. If the task closed under Degraded Mode, set `final status: final-source-served-pass-degraded` and still record the open defect so the next session repairs it.

## 14. Commit And Close The Local Phase

Before finishing:

- Ensure final CSS or generated CSS is in tracked files.
- Update the handoff or mini-handoff's `final status`, QA result, and other status fields.
- Include Playwright MCP browser QA findings, injection proof, source-served verification, and the optional project-local Playwright regression result or a clear skip note.
- Commit code and handoff changes on the same branch or PR according to project Git policy.

On full completion, write a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed, and keep the active handoff or mini-handoff in place so the context is preserved. In the beta channel, do not `git rm` the handoff on a `final-source-served-pass`; remove it with `git rm` only once the page has gone live (Go-Live). Commit and push that removal together with the code changes that close the task, or as the closing commit when the code was already committed, so the server-side workspace sees the closed task on its next `git pull`. While `final status = implementation-pass-pending-deploy`, the handoff or mini-handoff also stays in place until the source-served verification pass closes the loop.

Do not push, deploy, or change release flow unless the project context or maintainer explicitly asks for it.

## Concise Example

A developer is asked to adjust spacing in the `Feature Cards` Section:

1. The Skill asks whether a Section handoff exists; the user says it was forgotten, the work is visual-only.
2. The Skill creates `feature-cards-visual-only-handoff.md` in the project handoff storage location and sets `frontend work mode = visual-only-mini-handoff`.
3. It confirms `playwright_mcp: ready`, navigates to the staging URL, and finds the Section gated by a placeholder login; the user pastes a throwaway login for the session.
4. It re-reads the Figma frame from the handoff link, captures real DOM and computed styles for `.wso-section-feature-cards`, and prepares scoped variable changes.
5. It injects the planned rules through Playwright/CDP, confirms they win the cascade without `!important`, and sets `injection proof = pass`.
6. It writes final CSS into the tracked Section CSS file and detects `delivery path = git-pull-required`.
7. It stops with `implementation pass; waiting for server pull/deploy`, asks the user to pull on the server, and waits.
8. The user confirms the pull; the Skill checks that the new rules are served, then runs desktop, tablet, and mobile viewport checks.
9. It sets `final status = final-source-served-pass`, writes a short note to the project's notes file, and keeps the active handoff in place until the page goes live (removal with `git rm` happens only at Go-Live).
