# Frontend Design QA Plugin

Reusable local frontend guidance for turning a filled WST Section handoff or CPT foundation handoff into tracked CSS or SCSS changes and verified visual behavior.

This plugin covers the local implementation phase. It consumes handoffs produced by server-side WST work, guides CSS and Figma-to-code decisions, allows Chrome Local Overrides as short-lived spike work, and documents a Playwright-oriented acceptance path against a dev or staging WordPress URL. Values that differ per project belong in `project-template/PROJECT-CONTEXT.md` or the current handoff.

The filled Section or CPT handoff is the source of truth for local frontend work. Do not start from chat context, legacy SmartFlow guide material, browser overrides, or reusable examples alone. If a blocking implementation or verification fact is missing, stop and update the handoff or ask for the missing value before writing final tracked CSS or SCSS.

## Responsibility

Use this plugin for local frontend work:

- Read the filled Section or CPT handoff before styling begins.
- Stop when the handoff is missing blocking facts such as target URLs, stable selectors, ACF/WST references, WP Grid Builder IDs, visual requirements, local frontend responsibilities, or project-configured storage location.
- Implement final CSS or SCSS-derived changes in tracked local project files.
- Apply reusable CSS conventions for variables, selectors, rem scaling, responsive strategy, and formatting.
- Translate Figma layout, spacing, typography, and media behavior into project tokens and scoped CSS.
- Treat Chrome Local Overrides as temporary spike work only.
- Verify the Section with responsive browser checks and Playwright-oriented acceptance checks.
- Verify CPT cards, archive/grid views, and optional single templates from a CPT foundation handoff.
- Update handoff QA notes and commit them with the local frontend code.

Do not use this plugin for server-side WST template creation, ACF field wiring, WP-CLI operations, media imports, deployment, or full WordPress runtime setup.

## Project Context Required

Before using the Skill or Rules, fill or locate these project-local values:

- Theme path and editable frontend source paths.
- Style loader or `styles.json` registration pattern.
- CSS or SCSS build command.
- Theme tokens for colors, typography, spacing, buttons, and containers.
- Breakpoints and rem scale.
- Figma style mappings and grid assumptions.
- Target dev or staging URL.
- Playwright command, test location, and viewport conventions.
- Git branch or PR policy.

## Included Content

- Rule: `css-guideline`
- Rule: `css-theme-styles`
- Rule: `figma-to-code`
- Skill: `frontend-section-qa`
- Skill: `cpt-frontend-qa`

## Section Handoff

Start from a filled Section handoff created by the server phase. The handoff should name the target URL, template and CSS files, ACF references, CSS hooks, expected visual behavior, QA notes, and local frontend responsibilities.

If the Section handoff is missing the target URL, stable selectors, ACF or WST references, CSS path, visual requirements, local frontend responsibilities, or project-configured storage facts, stop and ask for the missing information instead of guessing.

After implementation, update the handoff with:

- Local frontend phase status.
- Responsive check result.
- Playwright result or documented acceptance path.
- Remaining risks or open questions.
- Confirmation that final CSS or SCSS-derived changes live in tracked files.

Commit the updated handoff with the local frontend code so the server-side context, implementation notes, and QA result stay together on the same branch or PR.

## CPT Foundation Handoff

Start from a filled CPT foundation handoff created by the WST Builder `wst-new-post-type` Skill when finishing CPT presentation locally. The handoff should name the target dev or staging URL, CPT labels, detail-page decision, display target, card/archive/single templates, WP Grid Builder IDs, stable selectors, CSS or SCSS file paths, expected behavior, QA notes, and local frontend responsibilities.

If the CPT handoff is missing target URLs, stable selectors, ACF references, WP Grid Builder IDs or an explicit no-WPGB decision, visual requirements, local frontend responsibilities, project-configured storage facts, or detail-page/display decisions, stop and ask for the missing information instead of guessing.

Use `cpt-frontend-qa` for:

- CPT card CSS or SCSS implementation.
- Archive, grid, carousel, filter, or existing Section presentation around CPT cards.
- Optional public single-template frontend presentation.
- Responsive checks for card counts, repeated cards, long copy, missing images, empty optional fields, and interaction states.
- Playwright-oriented checks for card/archive/grid visibility and optional single-page visibility.

If a CPT display becomes primarily a new WST Section layout, use `frontend-section-qa` for the Section-level work and keep CPT card-specific checks in the CPT handoff.

After implementation, write QA results, responsive findings, Playwright result or documented acceptance path, changed CSS/SCSS files, generated CSS notes, Chrome Local Overrides disposition, cache notes, risks, and open questions back to the same CPT handoff.

## Chrome Local Overrides

Chrome Local Overrides are useful for short visual spikes against a real WordPress page. They are never the final source of truth. Copy successful declarations into tracked project files, rebuild generated CSS if needed, and discard the override.

## Playwright Acceptance

Prefer focused Section checks over broad page tests. A useful first check navigates to the handoff URL, locates the primary Section class, verifies key content or item count, and repeats visibility checks at the project's desktop, tablet, and mobile viewports.

For CPT work, prefer focused checks that navigate to the CPT display URL, locate the grid or archive wrapper, verify visible card selectors or expected card count, and repeat visibility checks at the project's desktop, tablet, and mobile viewports. When public single templates exist, include one representative single URL from the handoff.

## Not Included

- WST Builder Plugin or server-side Section creation.
- WordPress Server Ops Plugin or Remote-SSH operations.
- Project-specific token values, URLs, repository details, or deployment instructions.
- A full local WordPress development environment.
- Pixel-perfect implementation for a real design without project context.
