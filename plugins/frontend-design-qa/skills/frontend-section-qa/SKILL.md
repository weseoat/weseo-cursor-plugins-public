---
name: frontend-section-qa
description: Implement and verify a local frontend Section from a filled Section handoff. Use when doing CSS or SCSS Section work, a Playwright MCP browser QA loop for navigation, snapshots, screenshots, viewports, and selector checks, optional Chrome Local Overrides spikes, responsive visual QA, or optional project-local Playwright regression acceptance against a dev or staging WordPress URL.
---

# Frontend Section QA

Use this Skill for the local frontend phase after the server phase has created or updated a WST Section foundation and filled a Section handoff.

When a CPT display becomes primarily a WST Section layout, use this Skill for the Section-level layout behavior only. Keep CPT card, archive/grid, carousel/filter, WP Grid Builder output, and optional single-template QA in the filled CPT handoff through `cpt-frontend-qa`.

This Skill owns local CSS or SCSS implementation, the Playwright MCP browser QA loop, responsive checks, optional project-local Playwright regression acceptance, and Section handoff QA writeback. It does not own server-side WST templates, ACF field groups, WordPress content, WP Grid Builder setup, WP-CLI, cache execution, deployment, or Remote-SSH setup. Playwright MCP setup itself is owned by `setup-playwright-mcp` in this plugin.

## Required Starting Point

Start from a concrete filled Section handoff produced by the WST Builder server phase. The handoff is the contract for local frontend work and the place where QA results are written back.

WST Builder owns the reusable Section handoff template at `plugins/wst-builder/handoffs/section-handoff.template.md`. The filled handoff itself lives at the project-configured storage location from Project Context.

Do not begin final CSS, SCSS, Chrome Local Overrides spikes, responsive checks, or browser QA from chat context alone. If the handoff path or project-configured storage location is unknown, stop and ask for it.

## Playwright MCP Preflight

Browser QA in this Skill runs through Playwright MCP in the local Cursor workspace. Before the first browser interaction:

1. Read `PROJECT-CONTEXT.md` and the active handoff for the `playwright_mcp` status.
2. If the status is `ready` and a quick navigation to the handoff target URL still works, continue.
3. If the status is missing, `pending`, or unverified for this local workspace, run `setup-playwright-mcp` first.
4. If a blocker prevents browser access (login wall, cookie banner, IP allowlist, self-signed cert, headless restriction), record the blocker in the handoff and continue with a focused manual acceptance path until the blocker is resolved.

Never configure Playwright MCP inside a Remote-SSH workspace from this Skill. Route that back to `setup-playwright-mcp` in the local frontend workspace.

## Inputs

Read these before editing:

- Section handoff path, project-configured storage location, and current handoff status.
- Target dev or staging URL.
- Section slug, layout name, primary section class, wrapper classes, and selectors to preserve.
- Template, WST, ACF, and CSS or SCSS file references.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Source design or reference notes.
- Local Playwright MCP status from `PROJECT-CONTEXT.md` and the handoff, including any browser access blocker.
- Project context for theme tokens, style loader, local build command, optional project-local Playwright command, viewport conventions, and Git branch or PR.

If the handoff is missing the target URL, stable selectors, ACF or WST references, CSS path, visual requirements, local frontend responsibilities, storage facts, cache state, known risks, or open questions, stop and ask for the missing information before final CSS work. Do not invent URLs, selectors, ACF references, WST layout names, theme tokens, file paths, storage locations, cache behavior, or expected behavior.

## Workflow

Track progress with this checklist:

```text
Frontend Section QA:
- [ ] Read project context and Section handoff
- [ ] Confirm Playwright MCP is ready locally or run setup-playwright-mcp
- [ ] Inspect existing Section and theme CSS patterns
- [ ] Drive a Playwright MCP browser QA loop against the handoff target URL
- [ ] Implement CSS or SCSS in tracked local files
- [ ] Use Chrome Local Overrides only for temporary spikes
- [ ] Move any successful spike changes into source files
- [ ] Re-run the Playwright MCP browser loop at desktop, tablet, and mobile viewports
- [ ] Run the optional project-local Playwright regression command when a real harness exists
- [ ] Record stale-cache or server-output symptoms without running server commands
- [ ] Update the handoff QA notes including local Playwright MCP status
- [ ] Commit code and handoff updates on the same branch or PR
```

## 1. Confirm The Handoff

The Section handoff is the contract for local work. Confirm it includes:

- Page URL.
- Source design or reference.
- Template file and CSS file.
- WST template, ACF layout, clone child field, and fields that affect rendering.
- Primary section class and wrapper hooks.
- Selectors that tests, scripts, or templates rely on.
- Expected visual behavior across breakpoints.
- Project-configured handoff storage location, local frontend responsibilities, QA notes, cache state, known risks, and open questions.

Do not start final CSS work while the handoff contains unresolved placeholders for markup, selectors, ACF/WST references, visual behavior, target URLs, cache state, local frontend responsibilities, open questions, or storage location.

If the current project provides a Section handoff validator, run that project-local command. In this development repository, the optional validator is:

```sh
python scripts/validate-section-handoffs.py
```

Do not require that command in installed plugin consumers. When no validator is bundled by the current project, proceed if the filled handoff contains the required URL, selectors, WST and ACF references, CSS path, visual behavior, local responsibilities, QA storage facts, cache state, and open risks.

## 2. Inspect Existing Frontend Patterns

Before writing new CSS:

- Read nearby Section files with similar layout or components.
- Check global token, typography, button, background, and image rules.
- Check style loader registration such as `styles.json` or the project's equivalent.
- Check whether the project uses SCSS as an authoring layer and generated CSS as the runtime file.

Do not restructure the theme or move files outside the paths approved by project context.
Do not perform server-side WST, ACF, WordPress content, WP Grid Builder, WP-CLI, cache execution, deployment, or Remote-SSH work during local frontend QA.
Do not edit PHP bootstrap or MU plugin files during local frontend QA. `functions.php` is forbidden for agent edits; `theme-functions.php` and MU plugin files require explicit prior user confirmation and should be routed to the appropriate server-side phase.

## 3. Implement Tracked CSS Or SCSS

Write final changes in tracked project source files:

- Use scoped Section variables for local spacing, sizing, and behavior.
- Reuse project theme tokens and classes where they match the design.
- Preserve handoff selectors.
- Register new CSS files in the project style loader when required.
- If using SCSS, update generated CSS through the project build command or documented manual compile path.

Never leave final visual changes only in browser overrides, untracked scratch files, or copied DevTools snippets.

## 4. Chrome Local Overrides Policy

Chrome Local Overrides are allowed only as a spike tool:

- Use them to test a visual idea quickly against the target URL.
- Keep the spike short and copy successful declarations into tracked source files immediately.
- Discard failed spike changes.
- Rebuild or refresh the tracked CSS output after moving the change.
- Note in the handoff if an override revealed a follow-up risk or browser-only difference.

Overrides are not the source of truth.

## 5. Responsive And Interaction Checks

Check the Section against the target URL:

- Desktop, tablet, and mobile sizes from the handoff.
- Long copy, empty optional fields, repeated items, and missing images.
- Hover, focus-visible, active, loading, and disabled states that apply.
- Reduced motion or contrast preferences when the Section has motion or color-sensitive UI.
- Cache state if the rendered page does not reflect local changes.

Update the handoff QA notes with the result and remaining risks.

If markup, rendered data, generated CSS, or cache state looks stale, record the URL, selector, expected result, observed result, and local checks already performed in the handoff. Route cache flushes, WP-CLI, deployment, or server repair action back to WordPress Server Ops or the project's `PROJECT-CONTEXT.md` cache guidance.

## 6. Playwright MCP Browser QA Loop

Playwright MCP is the primary browser-driving mechanism for Section QA. After the preflight confirms `playwright_mcp: ready`, run a focused loop against the handoff target URL.

Core loop:

1. Navigate to the handoff target URL.
2. Take an accessibility or DOM snapshot to confirm the page rendered without error.
3. Locate the Section by its primary class or stable landmark from the handoff.
4. Assert the Section is visible and expected key content or item count matches the handoff.
5. Switch to desktop, tablet, and mobile viewports from the handoff and re-check visibility and key content.
6. Inspect selectors, computed style, or bounding boxes when the MCP exposes them, to diagnose spacing, typography, color, or grid issues.
7. Edit tracked CSS or SCSS, rebuild generated CSS when required, reload the page, and re-check the affected viewports until the handoff's expected visual behavior holds.
8. Capture screenshots only when the project workflow uses screenshots for review or handoff QA notes.

If a browser access blocker appears (login wall, cookie banner overlay, IP allowlist, self-signed cert, headless restriction), record the URL, step, observed message, whether the same URL loads in a regular browser, and the suggested next action in the handoff. Do not paste credentials, cookies, or session tokens into chat, tracked files, diagnostics, or screenshots.

## 7. Optional Project-Local Playwright Regression

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

## 8. Update The Handoff

Write QA notes back to the same Section handoff that started the local phase. Include:

- Local frontend phase status.
- Local Playwright MCP status (`ready` or `pending: <reason>`) and any browser access blocker.
- Playwright MCP browser QA findings: viewports checked, selectors confirmed, screenshots captured when applicable.
- Responsive browser findings for the handoff's desktop, tablet, and mobile expectations.
- Optional project-local Playwright regression result or documented skip reason tied to the visible behavior in the handoff.
- Implementation notes for changed CSS or SCSS files and generated CSS when applicable.
- Remaining risks, open questions, stale-cache or server-output symptoms, route-back owner when action is needed, and confirmation that Chrome Local Overrides were discarded or copied into tracked source.

## 9. Commit The Local Phase

Before finishing:

- Ensure final CSS or generated CSS is in tracked files.
- Update the Section handoff's local frontend status and QA result.
- Include Playwright MCP browser QA findings and the optional project-local Playwright regression result or a clear note explaining why a project test was documented instead of run.
- Commit code and handoff changes on the same branch or PR according to project Git policy.

Do not push, deploy, or change release flow unless the project context or maintainer explicitly asks for it.

## Concise Example

A developer receives a filled handoff for `Feature Cards`:

1. Reads the handoff and confirms `.wso-section-feature-cards`, target URL, CSS path, and expected three-card responsive behavior.
2. Confirms `playwright_mcp: ready` for the local workspace, otherwise runs `setup-playwright-mcp`.
3. Drives a Playwright MCP browser loop: navigates to the staging URL, snapshots the page, locates `.wso-section-feature-cards`, verifies the card count, and checks desktop, tablet, and mobile viewports.
4. Checks existing card and button CSS for matching tokens.
5. Implements scoped variables and grid styles in the tracked Section CSS or SCSS file.
6. Uses Chrome Local Overrides briefly to compare spacing, then moves the final declarations into source.
7. Re-runs the Playwright MCP browser loop to confirm the final result, and runs the optional project-local Playwright regression test when a real harness exists.
8. Updates the handoff QA notes including local Playwright MCP status, and commits the Section CSS plus handoff on the same branch or PR.
