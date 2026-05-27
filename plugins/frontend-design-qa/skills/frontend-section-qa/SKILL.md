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
- `browser access`: `ready` or `blocked: <reason>`.
- `proof mode`: `injection-proof` or `source-served`.
- `injection proof`: `pending`, `pass`, `fail`, or `not-needed`.
- `delivery path`: `direct-local-serving`, `auto-deploy-available`, `git-pull-required`, or `unknown`.
- `server pull/deploy`: `not-needed`, `pending`, `user-confirmed`, or `not-reflected`.
- `source-served verification`: `pending`, `pass`, `fail`, or `blocked`.
- `final status`: `implementation-pass-pending-deploy`, `final-source-served-pass`, or `blocked`.

## Playwright MCP Preflight

Browser QA in this Skill runs through Playwright MCP in the local Cursor workspace. Before the first browser interaction:

1. Read `PROJECT-CONTEXT.md` and the active handoff or mini-handoff for the `playwright_mcp` status.
2. If the status is `ready` and a quick navigation to the target URL still works, continue.
3. If the status is missing, `pending`, or unverified for this local workspace, run `setup-playwright-mcp` first.
4. If a blocker prevents browser access (login wall, cookie banner, IP allowlist, self-signed cert, headless restriction), record the blocker in the handoff and treat browser access as a hard precondition for final CSS writes (see Browser Access Safety Stop).

Never configure Playwright MCP inside a Remote-SSH workspace from this Skill. Route that back to `setup-playwright-mcp` in the local frontend workspace.

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
- Section slug, layout name, primary section class, wrapper classes, and selectors to preserve.
- Template, WST, ACF, and CSS or SCSS file references.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Original Figma or source design reference.
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
- [ ] Confirm browser access to the target URL, or stop and ask for login/access
- [ ] Re-read the Figma or source design when a link is available
- [ ] Inspect existing Section and theme CSS or SCSS patterns
- [ ] Drive a Playwright MCP browser QA loop and capture real DOM, matched and computed styles
- [ ] Run a CSS-injection proof of the planned rules against the real target URL
- [ ] Implement final CSS or SCSS in tracked local files when the injection proof passes
- [ ] Detect the delivery path; default to git-pull-required when no auto-deploy exists
- [ ] On delivery path `git-pull-required` or `unknown`, stop with implementation-pass-pending-deploy and wait for user confirmation
- [ ] After user confirms server pull or deploy, verify that the new CSS rules are actually served before re-checking visuals
- [ ] Re-run the Playwright MCP browser loop at desktop, tablet, and mobile viewports
- [ ] Run the optional project-local Playwright regression command when a real harness exists
- [ ] Record stale-cache or server-output symptoms without running server commands
- [ ] Stop and document any server, markup, ACF, or WST discrepancy as a server blocker; route to wst-section-workflow
- [ ] Update the handoff QA notes and status fields
- [ ] Commit code and handoff updates on the same branch or PR
- [ ] On full completion, write a short permanent project note and delete an active mini-handoff
```

## 1. Confirm The Work Mode

Ask the start question and set `frontend work mode`:

- If the user provides a handoff: `frontend work mode = handoff`.
- If the user confirms visual-only and no handoff exists: `frontend work mode = visual-only-mini-handoff`.

For `handoff` mode, confirm the handoff contains: Page URL, source design or reference, template file and CSS file, WST template and ACF references, primary section class and wrapper hooks, selectors that templates or tests rely on, expected visual behavior across breakpoints, storage location, local frontend responsibilities, QA notes, cache state, known risks, and open questions.

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

When the planned CSS or SCSS lives in local files that will reach the server only through Git pull or deploy, prove the rules against the real page through injection before writing them to tracked source.

- Compose the planned rules in the same form they will take in the tracked file.
- Inject them temporarily into the rendered target page through Playwright or CDP, for example as a `<style>` element or a CSS rule insertion.
- Re-read computed styles and the visual result for the affected elements.
- Confirm the rules win the cascade. If they do not, document the cause: higher specificity, later source order, `!important` rule, inline style, plugin style.
- Adjust the rules with minimally stronger or scoped selectors. Use `!important` only when the theme or plugin pattern leaves no better option.

Set `injection proof` to `pass`, `fail`, or `not-needed` in the active handoff or mini-handoff.

Chrome Local Overrides are not the default proof mechanism. Playwright MCP runs in its own browser context without your logged-in session and without your Chrome user profile. Treat Chrome Local Overrides as an optional manual spike in the user's real browser, when the user explicitly chooses that path and confirms session and override availability.

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

When `delivery path` is `git-pull-required` or `unknown`, stop after writing the local files with:

```
implementation pass; waiting for server pull/deploy
```

Set `server pull/deploy = pending` and ask the user to pull or deploy on the server, then confirm when the target page actually serves the new CSS. Do not continue source-served verification before that confirmation.

## 8. Source-Served Verification

Only after the user confirms `server pull/deploy = user-confirmed`, run the source-served verification pass against the target URL.

- Navigate to the target URL with a fresh load.
- Confirm that the new CSS file or rules are actually present in the served stylesheets, for example by inspecting the loaded stylesheet content, a known new selector, or computed styles without injection.
- If the new rules are not served, set `server pull/deploy = not-reflected`, document the symptom (deploy not reflected, cache stale, wrong file delivered, theme override late), and stop visual evaluation. Route cache flush, WP-CLI, deployment, or server repair back to WordPress Server Ops or the project's `PROJECT-CONTEXT.md` cache guidance.
- If the new rules are served, continue with responsive and interaction checks and set `source-served verification = pass` when the visual behavior matches expectations.

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

## 11. Playwright MCP Browser QA Loop

Playwright MCP is the primary browser-driving mechanism for Section QA. After the preflight confirms `playwright_mcp: ready` and browser access works, run a focused loop against the target URL.

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

- All status fields from Status Fields The Skill Maintains.
- Playwright MCP browser QA findings: viewports checked, selectors confirmed, screenshots captured when applicable.
- Injection proof outcome and any specificity adjustments.
- Source-served verification outcome and cache or deploy symptoms.
- Responsive findings for desktop, tablet, and mobile expectations.
- Optional project-local Playwright regression result or documented skip reason.
- Implementation notes for changed CSS or SCSS files and generated CSS when applicable.
- Remaining risks, open questions, route-back owner when action is needed, and confirmation that any Chrome Local Overrides spike was discarded or copied into tracked source.

## 14. Commit And Close The Local Phase

Before finishing:

- Ensure final CSS or generated CSS is in tracked files.
- Update the handoff or mini-handoff's `final status`, QA result, and other status fields.
- Include Playwright MCP browser QA findings, injection proof, source-served verification, and the optional project-local Playwright regression result or a clear skip note.
- Commit code and handoff changes on the same branch or PR according to project Git policy.

On full completion, especially when a mini-handoff was used, write a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed, then remove the active mini-handoff with `git rm` and commit and push the removal so the server-side workspace sees the closed task on its next `git pull`. While `final status = implementation-pass-pending-deploy`, keep the mini-handoff in place until the source-served verification pass closes the loop.

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
9. It sets `final status = final-source-served-pass`, writes a short note to the project's notes file, and deletes the mini-handoff.
