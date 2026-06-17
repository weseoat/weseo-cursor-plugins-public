---
name: cpt-frontend-qa
description: Implement and verify local frontend presentation for a WST CPT handoff. Use when styling CPT cards, archive or grid views, WP Grid Builder frontend output, optional single templates, driving a Playwright MCP browser QA loop, Chrome Local Overrides spikes, responsive checks, or optional project-local Playwright regression acceptance against a dev or staging WordPress URL.
---

# CPT Frontend QA

This Skill is a productive QA and CSS/SCSS implementation workflow with safety stops for CPT frontend work, not a strict handoff gate. Discovery and reads are always allowed. Writes proceed when scope is clear, browser access works, and an injection-proof against the real WordPress page passes. The Skill stops only at concrete risk points: missing browser access, server-side discrepancies, or pending Git pull/deploy.

Use this Skill for the local frontend phase after the WST Builder `wst-new-post-type` Skill has created the server-side CPT foundation and filled a CPT foundation handoff. When the task is visual-only CPT card or display styling and no handoff exists, the Skill creates a temporary mini-handoff and works from there.

This Skill owns final tracked CSS or SCSS work for CPT cards, archive/grid presentation, carousel/filter behavior, WP Grid Builder frontend output, and optional single-template presentation. It also owns a Playwright MCP browser QA loop, CSS-injection proof against the real target URL, responsive checks, optional project-local Playwright regression acceptance, and CPT handoff QA writeback. It does not own CPT registration, taxonomy setup, ACF field groups, WP Grid Builder card/grid foundation, WST templates, WordPress content, WP-CLI, cache execution, deployment, or Remote-SSH operations. Playwright MCP setup itself is owned by `setup-playwright-mcp` in this plugin.

WST Builder owns the reusable CPT handoff template at `plugins/wst-builder/handoffs/cpt-handoff.template.md`; the filled handoff itself lives at the project-configured CPT handoff storage location from Project Context.

When a CPT display becomes primarily a WST Section layout, route the Section-level layout work to `frontend-section-qa`. CPT card, archive/grid, carousel/filter, WP Grid Builder output, and optional single-template QA stay in this Skill and are documented in the CPT handoff.

## Required Starting Point

Always ask first whether a CPT foundation handoff or `handoff.md` exists for this task. The handoff is tracked in Git and flows from the Remote-SSH WordPress workspace (where WST Builder writes it) to the local frontend workspace through a normal commit/push/pull cycle. A missing handoff usually means the local workspace has not pulled the latest commit yet, or WST Builder created the handoff but has not committed and pushed it.

- If a handoff exists locally: read it, use it as the contract, and write QA results back into it.
- If the user is unsure whether a handoff exists: run `git pull` (or `git fetch` plus a check of the active branch) before deciding the handoff is missing, then search the project-configured CPT handoff storage location from `PROJECT-CONTEXT.md`, and ask again with the findings.
- If the user confirms `visual-only` CPT card or display styling without a handoff: create a temporary mini-handoff (see below) and continue.
- If the task turns out to need server-side CPT, taxonomy, ACF, WPGB, or WST template changes: stop and route back to `wst-new-post-type` or `wst-section-workflow`.

## Mini-Handoff For Visual-Only Without Existing Handoff

When the user confirms visual-only CPT styling without an existing handoff, create a temporary mini-handoff in the project-configured CPT handoff storage location from `PROJECT-CONTEXT.md`. If the location is unknown, ask once.

- Filename: `<cpt-or-display-slug>-visual-only-handoff.md`.
- If the slug is unclear, derive a slug from the CPT display URL or card selector and ask for a one-line confirmation before writing the file.
- Use `cpt-handoff.template.md` as a base. Fill what is known and keep `<unresolved: ...>` markers for the rest.
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

Browser QA for CPT card, archive/grid, carousel/filter, and optional single-template work runs through Playwright MCP in the local Cursor workspace. Playwright MCP is the only accepted browser authority for DOM and computed-style inspection, CSS-injection proof, screenshots, viewport checks, and source-served verification. Before the first browser interaction:

1. Read `PROJECT-CONTEXT.md` and the active CPT handoff or mini-handoff for the `playwright_mcp` status.
2. If the status is `ready` and a quick Playwright MCP navigation to the CPT display URL (and the representative single URL when applicable) still works, continue.
3. If the status is missing, `pending`, or unverified for this local workspace, run `setup-playwright-mcp` first as the setup/repair target. Do not improvise a workaround in this Skill.
4. Run a Capability Probe before any QA work: try `browser_navigate` against the CPT display URL once (and the representative single URL when applicable), then `browser_evaluate` to read `location.href`, `document.title`, a known card selector, and a known grid or single selector. Record each tool result as `ok`, `failed: <short reason>`, or `not-tested` in the active handoff or mini-handoff. The result drives the next step.
5. If the entire MCP server fails (process down, red in `Settings -> Tools & MCP`, no browser tools listed) or both `browser_navigate` and `browser_evaluate` fail, hard stop. Set `browser access: blocked: playwright-mcp-unavailable` and `final status: blocked`, record the symptom, and route back to `setup-playwright-mcp` for repair.
6. If only individual tools are broken but at least `browser_evaluate` works and reaches the CPT display URL (and the representative single URL when applicable) through a documented fallback, switch to Degraded Mode (see Degraded Mode For Partial Playwright MCP Tool Defects). Do not call this a workaround that bypasses Playwright MCP; it is still Playwright MCP, just through a smaller tool surface.
7. If a content-level blocker prevents browser access (login wall, cookie banner, IP allowlist, self-signed cert, headless restriction) even though Playwright MCP itself works, record the blocker in the handoff and treat browser access as a hard precondition for final CSS writes (see Browser Access Safety Stop).

Never configure Playwright MCP inside a Remote-SSH workspace from this Skill. Route that back to `setup-playwright-mcp` in the local frontend workspace.

## No Cursor Browser Fallback

When Playwright MCP is unavailable, broken, or cannot navigate to the CPT display URL or representative single URL, this Skill must not silently switch to a substitute browser. Substitutes include the Cursor Browser, manual browser inspection by the user, user-pasted screenshots, raw DevTools console output, and Chrome Local Overrides.

- None of these substitutes may set `injection proof: pass` or `source-served verification: pass` for card, archive/grid, carousel/filter, or single-template rules. Final QA status passes only through Playwright MCP evidence.
- The Cursor Browser may be used only as a read-only diagnostic check, for example to confirm whether the CPT display URL loads at all in a different browser context. Any such check must be documented in the handoff with the explicit note that it was diagnostic only and did not contribute to the proof or final verification status.
- Chrome Local Overrides remain an optional manual spike in the user's own Chrome, when the user explicitly chooses that path. They never replace Playwright MCP for proof or final verification of CPT presentation.

If Playwright MCP cannot be brought up, the next action is always `setup-playwright-mcp`, not a different browser. If the user wants to force a Cursor Browser run, document that as `cursor-browser: diagnostic-only - not used for proof/verification` and keep `final status: blocked` until Playwright MCP works again.

## Degraded Mode For Partial Playwright MCP Tool Defects

Playwright MCP exposes several tools (`browser_navigate`, `browser_evaluate`, `browser_screenshot`, `browser_set_viewport`, etc.). Sometimes a single tool is defective while the rest works, for example when the MCP runtime is missing modern JS APIs and `browser_navigate` throws `TypeError: URL.canParse is not a function` even though Chromium and `browser_evaluate` are fine. In that case the Skill enters Degraded Mode instead of hard-stopping.

Degraded Mode rules:

- Degraded Mode requires that `browser_evaluate` works and that it can reach the CPT display URL (and the representative single URL when applicable). Set `browser access: degraded: <broken-tool>-fallback-evaluate` and record the concrete defect in `mcp tool defect`, for example `browser_navigate: URL.canParse missing in MCP runtime`.
- `browser_evaluate` based navigation is the only sanctioned fallback. Use it explicitly, for example `await new Promise(r => { addEventListener('load', r, { once: true }); location.assign('<target>'); })` or a `location.href = ...` plus readiness check.
- `browser_evaluate` based DOM, selector, and computed-style reads are sufficient for the CSS-injection proof on card, archive/grid, carousel/filter, and optional single-template surfaces.
- CSS-injection through `browser_evaluate` is allowed: insert a `<style>` element, use `CSSStyleSheet.insertRule`, or `document.adoptedStyleSheets`. After insertion, read computed styles back through `browser_evaluate`.
- Source-served verification in Degraded Mode reads the served stylesheet content through `browser_evaluate`, for example by fetching the stylesheet URL via `fetch(...).then(r => r.text())` from the page context and searching for the new selector, plus a computed-style spot check on the card or single element.
- Set `injection proof: pass-degraded` and `source-served verification: pass-degraded` when those passes succeed through `browser_evaluate`. Final status can become `final-source-served-pass-degraded`. These statuses are still Playwright MCP evidence; they remain off-limits to Cursor Browser, manual checks, and screenshots.
- Document Degraded Mode explicitly in the handoff or mini-handoff, including the broken tool, the observed error, the fallback path used, and the next action (`run setup-playwright-mcp`, file an MCP defect, pin or upgrade the MCP package, verify Node 20 plus for the MCP runtime).
- If the user accepts Degraded Mode for the current task, that is fine, but the Skill must still route the defect back to `setup-playwright-mcp` so the underlying MCP issue gets a real fix in the next session.

If `browser_evaluate` itself is broken, or the CPT display URL cannot be reached through the fallback, leave Degraded Mode and apply the hard stop from the Playwright MCP Preflight.

## Browser Access Safety Stop

Final visual CSS or SCSS writes require real browser access to the CPT display URL and, when public detail pages exist, a representative single URL. WordPress with a theme, WST, and WPGB on top has too many overrides to style blindly.

- If the target page needs login, cookie consent, basic auth, IP allowlist, or any other gating that Playwright MCP cannot pass, stop and ask for browser access or session login.
- Throwaway or placeholder login data may be shared by the user for the current session. Use it only to log in through Playwright/CDP.
- Do not write login credentials, cookies, tokens, or session details into the handoff, the mini-handoff, project notes, screenshots, console logs, diagnostics, or any tracked file.

Before the stop, the Skill may still:

- Create or update a mini-handoff.
- Read project context and existing CPT/card/grid CSS or SCSS patterns.
- Identify likely target files for the CSS or SCSS change.
- Prepare a draft of the planned rules.

## Inputs

Read these before editing:

- CPT foundation handoff or mini-handoff path, storage location, and current status fields.
- Target dev or staging URL for the card/archive/grid view and optional single view.
- CPT registered name, labels, detail-page decision, taxonomy decision, and display target.
- Card, archive/grid, and optional single template file references.
- CSS or SCSS file references and style loader requirements.
- Expected card, archive/grid, and optional single selectors.
- WP Grid Builder grid and card IDs as project-local values when they affect verification.
- Expected desktop, tablet, mobile, content variation, empty-state, and interaction behavior.
- Local Playwright MCP status from `PROJECT-CONTEXT.md` and the handoff, including any browser access blocker.
- Project Context for theme tokens, breakpoints, rem scale, style loader, build command, optional project-local Playwright command, viewport conventions, Git workflow, repository policy, and design references.

If a required value is unresolved, prefer to derive it from project context, the Figma source, the real target page, or existing patterns before asking the user. Stop and ask only when a value is genuinely not derivable and a write would be risky.

## Workflow

Track progress with this checklist:

```text
CPT Frontend QA:
- [ ] Ask whether a CPT handoff exists; create a mini-handoff for visual-only work without one
- [ ] Read project context and the active handoff or mini-handoff
- [ ] Confirm Playwright MCP is ready locally or run setup-playwright-mcp
- [ ] Run the Capability Probe (browser_navigate, browser_evaluate, and the planned tools) and record per-tool status
- [ ] If the MCP server is fully down or both navigate and evaluate fail, hard stop; do not fall back to Cursor Browser; route to setup-playwright-mcp
- [ ] If only individual tools are broken but browser_evaluate reaches the target, enter Degraded Mode and document the defect
- [ ] Confirm browser access to the CPT display URL, or stop and ask for login/access
- [ ] Confirm display target, selectors, and detail-page decision
- [ ] Re-read the Figma or source design when a link is available
- [ ] Inspect existing CPT, card, grid, and theme CSS or SCSS patterns
- [ ] Drive a Playwright MCP browser QA loop and capture real DOM, matched and computed styles
- [ ] Run a CSS-injection proof of the planned rules against the real CPT display URL and single URL when applicable through Playwright MCP only
- [ ] Implement card CSS or SCSS in tracked local files when the injection proof passes
- [ ] Implement archive/grid/carousel/filter CSS or SCSS in tracked local files when the injection proof passes
- [ ] Implement optional single-template CSS or SCSS when detail pages exist and the injection proof passes
- [ ] Detect the delivery path; default to git-pull-required when no auto-deploy exists
- [ ] On delivery path `git-pull-required` or `unknown`, stop with implementation-pass-pending-deploy and wait for user confirmation
- [ ] After user confirms server pull or deploy, verify that the new CSS rules are actually served before re-checking visuals
- [ ] Re-run the Playwright MCP browser loop at desktop, tablet, and mobile viewports
- [ ] Run the optional project-local Playwright regression command when a real harness exists
- [ ] Record stale-cache or server-output symptoms without running server commands
- [ ] Stop and document any server, markup, CPT, taxonomy, ACF, WST, or WPGB discrepancy as a server blocker; route to wst-new-post-type or wst-section-workflow
- [ ] Update the handoff QA notes and status fields
- [ ] Commit code and handoff updates on the same branch or PR
- [ ] On full completion, write a short permanent project note and keep the active handoff or mini-handoff in place until the page goes live (no `git rm` on QA pass; remove only after Go-Live)
```

## 1. Confirm The Work Mode

Ask the start question and set `frontend work mode`:

- If the user provides a CPT foundation handoff: `frontend work mode = handoff`.
- If the user confirms visual-only CPT styling and no handoff exists: `frontend work mode = visual-only-mini-handoff`.

For `handoff` mode, confirm the handoff includes:

- CPT name, labels, URL slug, and explicit detail-page decision.
- Display target: WP Grid Builder grid, carousel, existing Section, dedicated Section, card-only, single-only, or optional single template.
- Card template files and optional single template files.
- CSS or SCSS files to edit and generated CSS expectations.
- Stable card, archive/grid, wrapper, taxonomy/filter, and optional single selectors.
- ACF fields, taxonomy terms, featured image usage, and optional data that affect visible output.
- WPGB grid/card IDs or an explicit no-WPGB decision.
- Expected visual behavior across breakpoints, including long copy, missing images, empty fields, repeated cards, and filter or carousel behavior.
- Project-configured handoff storage location, local frontend responsibilities, server verification status, cache notes, known risks, and open questions.

For `visual-only-mini-handoff` mode, ensure the mini-handoff captures at minimum: CPT display URL, representative single URL when applicable, card and grid selectors, original Figma or source link if any, CSS or SCSS target path or a discovery note, expected visual behavior, and a clear statement that no server-side CPT/taxonomy/ACF/WST/WPGB changes are expected.

## 2. Re-Read The Figma Source

When the active handoff or mini-handoff carries a Figma link, re-read it from this Skill instead of trusting only the upstream summary.

- Pull the relevant card, grid, filter, carousel, and optional single frames, including spacing, typography, breakpoints, image behavior, and interaction states.
- Compare what Figma shows against what the rendered CPT display page shows.
- Interpret Figma project-conformly: respect established project tokens, typography, container widths, button systems, breakpoints, and rem scale.
- Document real Figma-vs-project deviations as `figma shows X, project pattern enforces Y, implemented as Z`.
- If the Figma link is not accessible, fall back to the screenshot or brief in the handoff and record the limitation.

## 3. Inspect Existing Frontend Patterns

Before writing new CSS:

- Read nearby post-type, card, grid, Section, and single-template styles.
- Check global token, typography, button, link, image, background, and grid rules.
- Check whether the project uses SCSS as an authoring layer and generated CSS as the runtime file.
- Check style registration such as `styles.json` or the project's equivalent.
- Search for selectors that already target the same CPT, WPGB, card, taxonomy/filter, carousel, or single-template classes.

Keep the work inside project-approved frontend paths. Do not rename WST hooks or WPGB selectors marked as stable in the handoff. Do not perform server-side CPT, taxonomy, ACF, WordPress content, WP Grid Builder foundation, WP-CLI, cache execution, deployment, or Remote-SSH work. Do not edit PHP bootstrap or MU plugin files. `functions.php` is forbidden for agent edits; `theme-functions.php` and MU plugin files require explicit prior user confirmation and should be routed to the appropriate server-side phase.

## 4. Real DOM And Specificity Inspection

Use Playwright MCP against the real CPT display URL (and representative single URL when public detail pages exist) to capture the evidence that local CSS must work against. Specificity is checked at the affected selector, not globally.

- Locate the card, grid, filter wrapper, carousel, or single by the stable selector from the handoff.
- Read the actual DOM structure of the elements that will be styled.
- Capture matched CSS rules and computed styles for the affected elements before any change.
- Note theme, WPGB, or plugin selectors that compete with the target rules.

Do not try to analyse the entire theme cascade. Stay on the elements that the planned rules target.

## 5. CSS-Injection Proof

When the planned CSS or SCSS lives in local files that will reach the server only through Git pull or deploy, prove the rules against the real page through injection before writing them to tracked source. The injection proof is only valid when executed through Playwright MCP; Cursor Browser, manual user checks, and screenshots do not satisfy it.

- Compose the planned rules in the same form they will take in the tracked file.
- Inject them temporarily into the rendered CPT display URL through Playwright MCP or its CDP channel, for example as a `<style>` element or a CSS rule insertion. Repeat for the representative single URL when single-template rules are part of the change.
- Re-read computed styles and the visual result for the affected card, grid, filter, carousel, or single elements through Playwright MCP.
- Confirm the rules win the cascade. If they do not, document the cause: higher specificity, later source order, `!important` rule, inline style, WPGB style, plugin style.
- Adjust the rules with minimally stronger or scoped selectors. Use `!important` only when the theme, WPGB, or plugin pattern leaves no better option.

Set `injection proof` to `pass`, `fail`, or `not-needed` in the active handoff or mini-handoff. Do not set `pass` based on Cursor Browser, manual inspection, or user-supplied screenshots; if Playwright MCP cannot run the proof, set `injection proof: blocked: playwright-mcp-unavailable` (or the navigation reason) and stop, see No Cursor Browser Fallback.

Chrome Local Overrides are not the default proof mechanism. Playwright MCP runs in its own browser context without your logged-in session and without your Chrome user profile. Treat Chrome Local Overrides as an optional manual spike in the user's real browser, when the user explicitly chooses that path and confirms session and override availability. They never replace a Playwright MCP injection proof.

## 6. Implement Card Presentation

After the injection proof passes for card rules, write the final card styles into tracked project files:

- Use the handoff's stable card selector, usually shaped like `.wso-<resource>-card`.
- Preserve selectors used by templates, scripts, WPGB behavior, or Playwright MCP and optional project-local Playwright regression checks.
- Use scoped card variables for spacing, image ratio, content gap, overlay behavior, and state styling.
- Reuse project tokens for typography, colors, buttons, links, shadows, borders, and transitions.
- Handle optional fields so missing taxonomy terms, images, prices, dates, excerpts, or links do not leave broken spacing.
- Verify hover, focus-visible, active, and disabled states when the card is clickable or interactive.

If a visual decision requires a new project token or shared card pattern, record it in the handoff before treating it as reusable theme behavior.

## 7. Implement Archive, Grid, Or Carousel Presentation

After the injection proof passes for grid, filter, or carousel rules, style the display target named in the handoff:

- For WP Grid Builder grids, keep WPGB structure intact and scope layout changes to the CPT grid wrapper or project-approved container.
- For carousels or sliders, verify slide spacing, overflow, controls, pagination, keyboard focus, and mobile swipe behavior when applicable.
- For existing Sections that embed the grid, preserve the Section's primary class and local CSS ownership.
- For dedicated CPT Sections, route Section-level layout to `frontend-section-qa` and keep card, archive/grid, carousel/filter, WPGB output, and optional single-template QA in this Skill.
- For filterable archives, verify filter visibility, selected state, empty result behavior, and responsive wrapping.

Do not make global WPGB, Bootstrap, container, row, or column changes for one CPT unless Project Context explicitly says that shared behavior is intended.

## 8. Implement Optional Single Presentation

Only implement single-template frontend work when the handoff says the CPT has public detail pages.

For single views, after the injection proof passes for single-template rules:

- Use the handoff's stable single selector, usually shaped like `.wso-<resource>-single`.
- Reuse global content, heading, button, image, and layout patterns where they match the project.
- Handle optional fields, missing media, long rich text, related cards, and taxonomy labels.
- Verify breadcrumb, back-link, related-content, or CTA behavior when the template includes them.
- Confirm the single URL is a dev or staging URL before running the Playwright MCP browser QA loop or the optional project-local Playwright regression command.

If the CPT has no public detail page, record that single-template QA is not applicable in the handoff.

## 9. Delivery Path Detection And Server Pull Stop

The Skill detects the delivery path itself from project context and Git workflow. The default assumption is Git-based delivery without direct server sync.

Set `delivery path` to one of:

- `direct-local-serving`: the local CSS file is loaded directly by the target page (rare for a Remote WordPress).
- `auto-deploy-available`: a documented sync, build, or deploy command runs from this workspace and reaches the server.
- `git-pull-required`: the change reaches the server only after a commit, push, and a Git pull or deploy on the server side.
- `unknown`: detection could not confirm a path.

When `delivery path` is `git-pull-required` or `unknown`, stop after writing the local files. This stop is a hard handoff to the user; do not run source-served verification before the user confirms back. Vague phrases like "please deploy" are not enough at this point. Spell out exactly what the user has to do, in plain language.

Set `server pull/deploy = pending` and post a handoff message that contains:

1. A one-line status: `implementation pass; waiting for server pull/deploy`.
2. A short list of the local files that were just changed and need to reach the server. Name them with their real project paths. Cover all touched surfaces (card, archive/grid, carousel/filter, optional single) and include any generated CSS or built artifact when the project uses SCSS.
3. The exact commit and push commands the user has to run in this local frontend workspace, using the project's conventions. Default shape, adapted to the project's Git policy and branch:
   ```sh
   git status
   git add <changed-files>
   git commit -m "FEATURE - <cpt slug>: <short description>"
   git push origin "$(git branch --show-current)"
   ```
4. The exact server-side step the user has to do next, named for the project. Pick the correct one and only mention that one to avoid confusion:
   - For projects where the WordPress server itself pulls via WP Pusher or a deploy hook: tell the user to open their Remote-SSH server workspace and run `git fetch origin` plus `git pull` (or to confirm WP Pusher has deployed the push), then to run the project's documented cache flush from `PROJECT-CONTEXT.md` (default `php wp-cli.phar cache flush && php wp-cli.phar eval "if(function_exists('rocket_clean_domain')){rocket_clean_domain();}"`).
   - For projects with an auto-deploy or push-to-deploy setup: tell the user to confirm the deploy completed for the just-pushed commit and to confirm the documented cache flush ran.
5. A clear `come back when ...` line that tells the user how to resume: `come back when the new CSS file is reachable on the CPT display URL <and on the representative single URL when applicable>`.
6. A note that the user does not have to start a new chat. A short `deployed and cache flushed` (or `deploy confirmed`) in the same conversation is enough. The Skill then continues with Source-Served Verification on the display URL and, when applicable, the single URL.

Do not continue source-served verification before the user actually confirms back. If the user reports an error during the deploy or cache flush (login wall, push rejected, cache flush failure, deploy job failed), record it on the active CPT handoff and route the action to `wordpress-server-ops` or to the project's documented server step instead of guessing.

## 10. Source-Served Verification

Only after the user confirms `server pull/deploy = user-confirmed`, run the source-served verification pass against the CPT display URL and, when public detail pages exist, the representative single URL. Source-served verification is only valid through Playwright MCP; Cursor Browser, manual inspection, and screenshots do not satisfy it.

- Navigate to the URL with a fresh Playwright MCP load.
- Confirm that the new CSS file or rules are actually present in the served stylesheets, for example by inspecting the loaded stylesheet content, a known new selector, or computed styles without injection.
- If the new rules are not served, set `server pull/deploy = not-reflected`, document the symptom (deploy not reflected, cache stale, wrong file delivered, WPGB or theme override late), and stop visual evaluation. Route cache flush, WP-CLI, deployment, or server repair back to WordPress Server Ops or the project's `PROJECT-CONTEXT.md` cache guidance.
- If the new rules are served, continue with responsive and interaction checks through Playwright MCP and set `source-served verification = pass` when the visual behavior matches expectations.
- If Playwright MCP cannot run this pass, set `source-served verification: blocked: playwright-mcp-unavailable` (or the navigation reason) and route back to `setup-playwright-mcp`. Do not mark `source-served verification = pass` based on Cursor Browser or screenshots.

Final status follows the verification:

- `implementation-pass-pending-deploy` when local files and injection proof are in place but the deploy has not been confirmed or not reflected.
- `final-source-served-pass` when source-served verification passed.
- `blocked` when a server, markup, CPT, taxonomy, ACF, WST, or WPGB discrepancy stops the work.

## 11. Responsive And Interaction Checks

After source-served verification passes, check the CPT presentation against the target URL:

- Desktop, tablet, and mobile sizes from Project Context or the handoff.
- Card counts, repeated cards, long labels, long excerpts, missing images, empty optional fields, and inconsistent image ratios.
- Grid, carousel, filter, pagination, hover, focus-visible, active, loading, and disabled states that apply.
- Optional single-template layout with long rich text, missing optional fields, related content, and media variations.
- Cache state if rendered markup or CSS does not reflect the served changes.

Update the handoff QA notes with results and remaining risks.

If markup, rendered data, generated CSS, WPGB output, or cache state looks stale, record the URL, selector, expected result, observed result, and local checks already performed.

## 12. Server, Markup, CPT, ACF, WST, Or WPGB Discrepancies

If browser QA shows that the problem is not solvable in CSS, hard stop.

- Do not edit PHP, ACF, WST templates, CPT registration, taxonomy, WPGB foundation, content, shortcodes, or WP-CLI from this Skill.
- Record the observed defect with URL, selector, expected behavior, real DOM, and any console or PHP symptom in the handoff or mini-handoff as a server blocker.
- Route back to `wst-new-post-type` for CPT, taxonomy, ACF, or WPGB foundation issues, or to `wst-section-workflow` for Section-level WST/ACF issues.
- Ask the user for OK before another skill or server workflow is started from this context.

## 13. Playwright MCP Browser QA Loop

Playwright MCP is the primary browser-driving mechanism for CPT QA. After the preflight confirms `playwright_mcp: ready` and browser access works, run a focused loop against the CPT display URL and, when detail pages exist, a representative single URL.

Core card/archive/grid loop:

1. Navigate to the CPT display URL.
2. Take an accessibility or DOM snapshot to confirm the page rendered without error.
3. Locate the grid, carousel, archive, or Section wrapper by the stable selector.
4. Assert the wrapper is visible and the expected card selector is visible or matches the expected count.
5. Capture matched and computed styles for the elements the planned rules target.
6. Inject planned CSS through Playwright or CDP as the injection proof; iterate until the rules win the cascade.
7. Switch to desktop, tablet, and mobile viewports and re-check visibility, spacing, and card behavior.
8. Verify hover, keyboard focus, filter, pagination, or carousel controls when they are part of the display target.

Core optional single loop:

1. Navigate to a representative single CPT URL.
2. Locate the stable single selector.
3. Capture matched and computed styles, then run an injection proof for the planned single-template rules.
4. Assert expected title, media, taxonomy label, content area, or CTA behavior.
5. Check responsive visibility at the project's viewport list.

Capture screenshots only when the project workflow uses screenshots for review or handoff QA notes. If a browser access blocker appears, record URL, step, observed message, whether the same URL loads in a regular browser, and the suggested next action in the handoff, then apply the Browser Access Safety Stop.

## 14. Optional Project-Local Playwright Regression

Run the project's Playwright command as an optional persistent regression check when `PROJECT-CONTEXT.md` or the handoff provides a real project-local harness. If no test exists yet, document a focused acceptance path and skip reason in the handoff.

Generic shape example:

```ts
test("resource cards render responsively", async ({ page }) => {
  await page.goto(process.env.CPT_ARCHIVE_URL!);
  const grid = page.locator(".wso-resource-grid");
  const cards = grid.locator(".wso-resource-card");

  await expect(grid).toBeVisible();
  await expect(cards).toHaveCount(3);

  await page.setViewportSize({ width: 390, height: 844 });
  await expect(cards.first()).toBeVisible();
});
```

Treat this as a shape example. Use the project's environment variables, locators, viewport list, test runner, and expected counts.

## 15. Update The Handoff

Write QA notes back to the same CPT foundation handoff or mini-handoff that started the local phase. Include:

- All status fields from Status Fields The Skill Maintains.
- Local frontend phase status for card, archive/grid, and optional single-template scope.
- Playwright MCP browser QA findings: display URL, optional single URL, viewports checked, selectors confirmed, screenshots captured when applicable.
- Injection proof outcomes per surface (card, grid, filter, single) and any specificity adjustments.
- Source-served verification outcome and cache or deploy symptoms.
- Responsive findings for desktop, tablet, and mobile expectations.
- Optional project-local Playwright regression result or documented skip reason.
- Implementation notes for changed CSS or SCSS files, generated CSS, and any style loader changes.
- Remaining risks, open questions, route-back owner when action is needed, and confirmation that any Chrome Local Overrides spike was discarded or copied into tracked source.

When Playwright MCP itself failed or could not navigate to the CPT display URL or representative single URL, record the blocker explicitly:

- Playwright MCP status at the time of failure (`unavailable`, `no-tools`, `navigation-failed: <reason>`, or `degraded: <broken-tool>-fallback-evaluate`).
- Capability Probe result per tool (`browser_navigate`, `browser_evaluate`, `browser_screenshot`, `browser_set_viewport`, others) with `ok`, `failed: <short reason>`, or `not-tested`.
- The step that failed (preflight navigation, card or grid injection proof, single injection proof, source-served verification, viewport pass).
- Observed error message or symptom, copied verbatim when short (for example `TypeError: URL.canParse is not a function`).
- Whether the target URL was checked diagnostically through the Cursor Browser; if yes, note `cursor-browser: diagnostic-only - not used for proof/verification` and what was observed.
- Whether Degraded Mode was used; if yes, name the fallback path (for example `browser_evaluate location.assign + getComputedStyle` and `fetch stylesheet text` for source-served).
- Next action: `run setup-playwright-mcp`, `install Node.js LTS locally and restart Cursor`, `file MCP defect issue and pin/upgrade @playwright/mcp`, `provide session login`, `request IT allowlist`, or other concrete repair step.
- Keep `final status: blocked` while the Playwright MCP blocker is unresolved. If the task closed under Degraded Mode, set `final status: final-source-served-pass-degraded` and still record the open defect so the next session repairs it.

## 16. Commit And Close The Local Phase

Before finishing:

- Ensure final CSS, SCSS, and generated CSS are in tracked files according to Project Context.
- Update the handoff or mini-handoff's `final status`, QA result, and other status fields.
- Include Playwright MCP browser QA findings, injection proof per surface, source-served verification, and the optional project-local Playwright regression result or a clear skip note.
- Commit code and handoff updates on the same branch or PR according to project Git policy.

On full completion, write a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed, and keep the active handoff or mini-handoff in place so the context is preserved. In the beta channel, do not `git rm` the handoff on a `final-source-served-pass`; remove it with `git rm` only once the page has gone live (Go-Live). Commit and push that removal together with the code changes that close the task, or as the closing commit when the code was already committed, so the server-side workspace sees the closed task on its next `git pull`. While `final status = implementation-pass-pending-deploy`, the handoff or mini-handoff also stays in place until the source-served verification pass closes the loop.

Do not push, deploy, edit server-side CPT setup, or change release flow unless the maintainer explicitly asks for it.

## Concise Example

A developer is asked to adjust a `Resources` CPT card grid:

1. The Skill asks whether a CPT handoff exists; the user provides the filled foundation handoff for `Resources`.
2. It reads the handoff and confirms `.wso-resource-card`, `.wso-resource-grid`, the staging archive URL, the card CSS path, and that no public single page exists.
3. It confirms `playwright_mcp: ready`, navigates to the staging archive URL, and finds the archive gated by a placeholder login; the user pastes a throwaway login for the session.
4. It captures real DOM and computed styles for `.wso-resource-grid` and `.wso-resource-card`, then prepares scoped card and grid variable changes.
5. It injects the planned rules through Playwright/CDP, confirms they win the WPGB and theme cascade without `!important`, and sets `injection proof = pass`.
6. It writes final CSS into the tracked CPT CSS file and detects `delivery path = git-pull-required`.
7. It stops with `implementation pass; waiting for server pull/deploy`, asks the user to pull on the server, and waits.
8. The user confirms the pull; the Skill checks that the new rules are served, then runs desktop, tablet, and mobile viewport checks for card count, long taxonomy labels, and missing image fallbacks.
9. It sets `final status = final-source-served-pass`, writes a short project note, and keeps the active CPT handoff in place until the page goes live (removal with `git rm` happens only at Go-Live).
