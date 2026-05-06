---
name: frontend-section-qa
description: Implement and verify a local frontend Section from a filled Section handoff. Use when doing CSS or SCSS Section work, Chrome Local Overrides spikes, responsive visual QA, or Playwright-oriented acceptance checks against a dev or staging WordPress URL.
---

# Frontend Section QA

Use this Skill for the local frontend phase after the server phase has created or updated a WST Section foundation and filled a Section handoff.

## Required Starting Point

Start from a concrete filled Section handoff produced by the WST Builder server phase. The handoff is the contract for local frontend work and the place where QA results are written back.

Do not begin final CSS, SCSS, Chrome Local Overrides spikes, responsive checks, or Playwright-oriented work from chat context alone. If the handoff path or project-configured storage location is unknown, stop and ask for it.

## Inputs

Read these before editing:

- Section handoff path, project-configured storage location, and current handoff status.
- Target dev or staging URL.
- Section slug, layout name, primary section class, wrapper classes, and selectors to preserve.
- Template, WST, ACF, and CSS or SCSS file references.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Source design or reference notes.
- Project context for theme tokens, style loader, local build command, Playwright command, and Git branch or PR.

If the handoff is missing the target URL, stable selectors, ACF or WST references, CSS path, visual requirements, local frontend responsibilities, or storage facts, stop and ask for the missing information. Do not invent URLs, selectors, ACF references, WST layout names, theme tokens, file paths, storage locations, or expected behavior.

## Workflow

Track progress with this checklist:

```text
Frontend Section QA:
- [ ] Read project context and Section handoff
- [ ] Inspect existing Section and theme CSS patterns
- [ ] Implement CSS or SCSS in tracked local files
- [ ] Use Chrome Local Overrides only for temporary spikes
- [ ] Move any successful spike changes into source files
- [ ] Run responsive browser checks
- [ ] Run or document Playwright acceptance checks
- [ ] Update the handoff QA notes
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

Do not start final CSS work while the handoff contains unresolved placeholders for markup, selectors, ACF/WST references, visual behavior, target URLs, or storage location.

Run the handoff validator when the handoff lives in this repository:

```sh
python scripts/validate-section-handoffs.py
```

## 2. Inspect Existing Frontend Patterns

Before writing new CSS:

- Read nearby Section files with similar layout or components.
- Check global token, typography, button, background, and image rules.
- Check style loader registration such as `styles.json` or the project's equivalent.
- Check whether the project uses SCSS as an authoring layer and generated CSS as the runtime file.

Do not restructure the theme or move files outside the paths approved by project context.

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

## 6. Playwright Acceptance Path

Use the project's Playwright command when available. If the project has no test yet, document a focused acceptance path in the handoff.

Recommended checks:

- Navigate to the handoff target URL.
- Locate the Section by its primary class or stable landmark.
- Assert the Section is visible.
- Assert expected key content or item count from the handoff.
- Check responsive visibility at the project's desktop, tablet, and mobile viewports.
- Check hover or keyboard focus states when the Section has links or controls.
- Capture a screenshot only when the project workflow uses screenshots for review.

Generic example:

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

## 7. Update The Handoff

Write QA notes back to the same Section handoff that started the local phase. Include:

- Local frontend phase status.
- Responsive browser findings for the handoff's desktop, tablet, and mobile expectations.
- Playwright result or documented acceptance path tied to the visible behavior in the handoff.
- Implementation notes for changed CSS or SCSS files and generated CSS when applicable.
- Remaining risks, open questions, cache notes, and confirmation that Chrome Local Overrides were discarded or copied into tracked source.

## 8. Commit The Local Phase

Before finishing:

- Ensure final CSS or generated CSS is in tracked files.
- Update the Section handoff's local frontend status and QA result.
- Include Playwright results or a clear note explaining why the check was documented instead of run.
- Commit code and handoff changes on the same branch or PR according to project Git policy.

Do not push, deploy, or change release flow unless the project context or maintainer explicitly asks for it.

## Concise Example

A developer receives a filled handoff for `Feature Cards`:

1. Reads the handoff and confirms `.wso-section-feature-cards`, target URL, CSS path, and expected three-card responsive behavior.
2. Checks existing card and button CSS for matching tokens.
3. Implements scoped variables and grid styles in the tracked Section CSS or SCSS file.
4. Uses Chrome Local Overrides briefly to compare spacing on the staging URL, then moves the final declarations into source.
5. Runs responsive checks and a Playwright test that locates `.wso-section-feature-cards` and verifies the expected card count.
6. Updates the handoff QA notes and commits the Section CSS plus handoff on the same branch or PR.
