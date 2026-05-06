---
name: cpt-frontend-qa
description: Implement and verify local frontend presentation for a WST CPT handoff. Use when styling CPT cards, archive or grid views, WP Grid Builder frontend output, optional single templates, Chrome Local Overrides spikes, responsive checks, or Playwright-oriented CPT acceptance checks against a dev or staging WordPress URL.
---

# CPT Frontend QA

Use this Skill for the local frontend phase after the WST Builder `wst-new-post-type` Skill has created the server-side CPT foundation and filled a CPT foundation handoff.

This Skill owns final tracked CSS or SCSS work, responsive checks, and Playwright-oriented verification for CPT cards, archive/grid presentation, and optional single-template presentation. It does not own CPT registration, taxonomy setup, ACF field groups, WP Grid Builder card/grid foundation, WST templates, WP-CLI, or Remote-SSH operations.

## Required Starting Point

Start from a concrete filled CPT foundation handoff produced by the WST Builder server phase. The handoff is the contract for CPT local frontend work and the place where QA results are written back.

Do not begin final card, archive/grid, optional single-template CSS, Chrome Local Overrides spikes, responsive checks, or Playwright-oriented work from chat context alone. If the handoff path or project-configured storage location is unknown, stop and ask for it.

## Inputs

Read these before editing:

- CPT foundation handoff path, project-configured storage location, and current handoff status.
- Target dev or staging URL for the card/archive/grid view and optional single view.
- CPT registered name, labels, detail-page decision, taxonomy decision, and display target.
- Card, archive/grid, and optional single template file references.
- CSS or SCSS file references and style loader requirements.
- Expected card, archive/grid, and optional single selectors.
- WP Grid Builder grid and card IDs as project-local values when they affect verification.
- Expected desktop, tablet, mobile, content variation, empty-state, and interaction behavior.
- Project Context for theme tokens, breakpoints, rem scale, style loader, build command, Playwright command, viewport conventions, repository policy, and design references.

If the handoff is missing target URLs, stable selectors, ACF references, WP Grid Builder IDs or an explicit no-WPGB decision, visual requirements, local frontend responsibilities, storage facts, or detail-page/display decisions, stop and ask for the missing information. Do not invent URLs, selectors, ACF references, WP Grid Builder IDs, theme tokens, file paths, storage locations, detail-page behavior, taxonomy behavior, or expected behavior.

## Workflow

Track progress with this checklist:

```text
CPT Frontend QA:
- [ ] Read Project Context and CPT foundation handoff
- [ ] Confirm display target, selectors, and detail-page decision
- [ ] Inspect existing CPT, card, grid, and theme CSS patterns
- [ ] Implement card CSS or SCSS in tracked local files
- [ ] Implement archive/grid CSS or SCSS in tracked local files
- [ ] Implement optional single-template CSS or SCSS when detail pages exist
- [ ] Use Chrome Local Overrides only for temporary spikes
- [ ] Move any successful spike changes into source files
- [ ] Run responsive and interaction checks
- [ ] Run or document Playwright-oriented CPT acceptance checks
- [ ] Update the handoff QA notes
- [ ] Commit code and handoff updates on the same branch or PR
```

## 1. Confirm The CPT Handoff

The CPT foundation handoff is the contract for local work. Confirm it includes:

- CPT name, labels, URL slug, and explicit detail-page decision.
- Display target: WP Grid Builder grid, carousel, existing Section, dedicated Section, or optional single template.
- Card template files and optional single template files.
- CSS or SCSS files to edit and generated CSS expectations.
- Stable card, archive/grid, wrapper, taxonomy/filter, and optional single selectors.
- ACF fields, taxonomy terms, featured image usage, and optional data that affect visible output.
- WPGB grid/card IDs, recorded as handoff or Project Context values rather than reusable plugin prose.
- Expected visual behavior across breakpoints, including long copy, missing images, empty fields, repeated cards, and filter or carousel behavior.
- Project-configured handoff storage location, local frontend responsibilities, server verification status, cache notes, known risks, and open questions.

Do not start final CSS work when the handoff still has unresolved placeholders or server-side questions that affect target URLs, markup, selectors, ACF references, WPGB IDs, display target, detail-page behavior, expected visual behavior, or storage location.

## 2. Inspect Existing Frontend Patterns

Before writing new CSS:

- Read nearby post-type, card, grid, Section, and single-template styles.
- Check global token, typography, button, link, image, background, and grid rules.
- Check whether the project uses SCSS as an authoring layer and generated CSS as the runtime file.
- Check style registration such as `styles.json` or the project's equivalent.
- Search for selectors that already target the same CPT, WPGB, card, taxonomy/filter, carousel, or single-template classes.

Keep the work inside project-approved frontend paths. Do not rename WST hooks or WPGB selectors marked as stable in the handoff.

## 3. Implement Card Presentation

Write final card styles in tracked project files:

- Use the handoff's stable card selector, usually shaped like `.wso-<resource>-card`.
- Preserve selectors used by templates, scripts, WPGB behavior, or Playwright checks.
- Use scoped card variables for spacing, image ratio, content gap, overlay behavior, and state styling.
- Reuse project tokens for typography, colors, buttons, links, shadows, borders, and transitions.
- Handle optional fields so missing taxonomy terms, images, prices, dates, excerpts, or links do not leave broken spacing.
- Verify hover, focus-visible, active, and disabled states when the card is clickable or interactive.

If a visual decision requires a new project token or shared card pattern, record it in the handoff before treating it as reusable theme behavior.

## 4. Implement Archive, Grid, Or Carousel Presentation

Style the display target named in the handoff:

- For WP Grid Builder grids, keep WPGB structure intact and scope layout changes to the CPT grid wrapper or project-approved container.
- For carousels or sliders, verify slide spacing, overflow, controls, pagination, keyboard focus, and mobile swipe behavior when applicable.
- For existing Sections that embed the grid, preserve the Section's primary class and local CSS ownership.
- For dedicated CPT Sections, use the existing `frontend-section-qa` Skill when Section-level layout becomes the dominant work.
- For filterable archives, verify filter visibility, selected state, empty result behavior, and responsive wrapping.

Do not make global WPGB, Bootstrap, container, row, or column changes for one CPT unless Project Context explicitly says that shared behavior is intended.

## 5. Implement Optional Single Presentation

Only implement single-template frontend work when the handoff says the CPT has public detail pages.

For single views:

- Use the handoff's stable single selector, usually shaped like `.wso-<resource>-single`.
- Reuse global content, heading, button, image, and layout patterns where they match the project.
- Handle optional fields, missing media, long rich text, related cards, and taxonomy labels.
- Verify breadcrumb, back-link, related-content, or CTA behavior when the template includes them.
- Confirm the single URL is a dev or staging URL before running browser or Playwright checks.

If the CPT has no public detail page, record that single-template QA is not applicable in the handoff.

## 6. Chrome Local Overrides Policy

Chrome Local Overrides are allowed only as short-lived spike work:

- Use them to compare spacing, sizing, state, or responsive ideas against the target URL.
- Keep the spike small and copy successful declarations into tracked CSS or SCSS immediately.
- Rebuild generated CSS when the project requires it.
- Discard failed override changes.
- Note in the handoff when an override exposed a selector, cache, or browser-only risk.

Overrides are never the source of truth for CPT frontend work.

## 7. Responsive And Interaction Checks

Check the CPT presentation against the target URL:

- Desktop, tablet, and mobile sizes from Project Context or the handoff.
- Card counts, repeated cards, long labels, long excerpts, missing images, empty optional fields, and inconsistent image ratios.
- Grid, carousel, filter, pagination, hover, focus-visible, active, loading, and disabled states that apply.
- Optional single-template layout with long rich text, missing optional fields, related content, and media variations.
- Cache state if rendered markup or CSS does not reflect local changes.

Update the handoff QA notes with results and remaining risks.

## 8. Playwright Acceptance Path

Use the project's Playwright command when available. If no test exists yet, document a focused acceptance path in the handoff.

Recommended card/archive/grid checks:

- Navigate to the handoff's CPT display URL.
- Locate the grid, carousel, archive, or Section wrapper by the stable selector from the handoff.
- Assert the wrapper is visible.
- Assert the expected card selector is visible or has the expected count from the handoff.
- Check at the project's desktop, tablet, and mobile viewports.
- Verify hover or keyboard focus states when cards include links or controls.
- Verify filter, pagination, or carousel controls when they are part of the display target.

Recommended optional single checks:

- Navigate to a representative single CPT URL from the handoff.
- Locate the stable single selector.
- Assert expected title, media, taxonomy label, content area, or CTA behavior.
- Check responsive visibility at the project's viewport list.

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

## 9. Update The Handoff

Write QA notes back to the same CPT foundation handoff that started the local phase. Include:

- Local frontend phase status for card, archive/grid, and optional single-template scope.
- Responsive browser findings for the handoff's desktop, tablet, and mobile expectations.
- Playwright result or documented acceptance path tied to the CPT display target and visible behavior in the handoff.
- Implementation notes for changed CSS or SCSS files, generated CSS, and any style loader changes.
- Remaining risks, open questions, cache notes, and confirmation that Chrome Local Overrides were discarded or copied into tracked source.

## 10. Commit The Local Phase

Before finishing:

- Ensure final CSS, SCSS, and generated CSS are in tracked files according to Project Context.
- Update the CPT foundation handoff's local frontend status and QA result.
- Include Playwright results or a clear note explaining why checks were documented instead of run.
- Confirm Chrome Local Overrides are discarded or copied into tracked files.
- Commit code and handoff updates on the same branch or PR according to project Git policy.

Do not push, deploy, edit server-side CPT setup, or change release flow unless the maintainer explicitly asks for it.

## Concise Example

A developer receives a filled handoff for `Resources`:

1. Reads the handoff and confirms `.wso-resource-card`, `.wso-resource-grid`, the staging URL, card CSS path, and that no public single page exists.
2. Checks existing card, WPGB, image, and button CSS for matching project tokens.
3. Implements scoped card and grid variables in tracked local CSS or SCSS files.
4. Uses Chrome Local Overrides briefly to compare card spacing on staging, then moves the final declarations into source.
5. Runs responsive checks and a Playwright test that locates `.wso-resource-grid` and verifies `.wso-resource-card` count and mobile visibility.
6. Updates the CPT handoff QA notes and commits the CSS plus handoff on the same branch or PR.
