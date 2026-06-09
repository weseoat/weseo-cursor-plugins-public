# Frontend Design QA Plugin

Reusable local frontend guidance for turning a filled WST Section handoff or CPT foundation handoff into tracked CSS or SCSS changes and verified visual behavior.

This plugin covers the local implementation phase. It consumes handoffs produced by server-side WST work, guides CSS and Figma-to-code decisions, drives a Playwright MCP browser loop against a dev or staging WordPress URL, allows Chrome Local Overrides as short-lived spike work, and treats project-local Playwright test commands as optional persistent regression checks. Values that differ per project belong in the project-local `PROJECT-CONTEXT.md` or the current handoff.

For the full WESEO WordPress/WST delivery workflow, install this plugin together with `wordpress-server-ops` and `wst-builder`. Frontend Design QA owns the local frontend phase: local Playwright MCP setup, Playwright MCP-driven browser QA, tracked CSS/SCSS implementation, Figma-to-code translation, Chrome Local Overrides spikes, responsive verification, optional project-local Playwright regression acceptance, cache/server escalation notes, and QA writeback to the filled Section or CPT handoff.

The Playwright MCP setup is local-only. Configure it in the local Cursor workspace through the bundled `setup-playwright-mcp` Skill. Do not configure Playwright MCP inside a Remote-SSH server workspace. WordPress MCP and Figma MCP remain owned by `wordpress-server-ops` `setup-orientation` in the server workspace.

PHP bootstrap files are sensitive across the full workflow. `functions.php` is forbidden for agent edits. `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change.

The filled Section or CPT handoff is the source of truth for local frontend work. Do not start from chat context, legacy SmartFlow guide material, browser overrides, or reusable examples alone. If a blocking implementation or verification fact is missing, stop and update the handoff or ask for the missing value before writing final tracked CSS or SCSS.

## Responsibility

Use this plugin for local frontend work:

- Read the filled Section or CPT handoff before styling begins.
- Stop when the handoff is missing blocking facts such as target URLs, stable selectors, ACF/WST references, WP Grid Builder IDs, visual requirements, local frontend responsibilities, or project-configured storage location.
- Configure Playwright MCP in the local Cursor workspace through `setup-playwright-mcp` before browser QA runs the first time, and re-run that Skill when the MCP server is missing, errored, or unverified for the active local workspace.
- Drive a Playwright MCP browser loop against the handoff target URL for navigation, accessibility/DOM snapshots, screenshots, desktop/tablet/mobile viewports, and selector or computed-style inspection.
- Implement final CSS or SCSS-derived changes in tracked local project files.
- Apply reusable CSS conventions for variables, selectors, rem scaling, responsive strategy, and formatting.
- Translate Figma layout, spacing, typography, and media behavior into project tokens and scoped CSS.
- Treat Chrome Local Overrides as temporary spike work only.
- Verify Sections and CPT cards, archive/grid views, and optional single templates with the Playwright MCP browser loop, and run the project's Playwright test command as an optional persistent regression check when a real project-local harness exists.
- Update handoff QA notes and commit them with the local frontend code.

Do not use this plugin for server-side WST template creation, CPT or taxonomy registration, ACF field wiring, WP Grid Builder card or grid setup, WP-CLI operations, cache execution, media imports, deployment, Remote-SSH setup, or full WordPress runtime setup. Local frontend work does not grant exceptions for PHP bootstrap or MU plugin edits: `functions.php` is forbidden, and `theme-functions.php` or MU plugin files require explicit prior user confirmation in the correct server-side phase.

## Project Context Required

Before using the Skill or Rules, fill or locate these project-local values:

- Theme path and editable frontend source paths.
- Style loader or `styles.json` registration pattern.
- CSS or SCSS build command.
- Theme tokens for colors, typography, spacing, buttons, and containers.
- Breakpoints and rem scale.
- Figma style mappings and grid assumptions.
- Target dev or staging URL.
- Local Playwright MCP readiness status (`playwright_mcp: ready` or `pending: <reason>`) and any browser access blockers such as login walls, cookie banners, IP allowlists, or self-signed certs.
- Optional project-local Playwright command, test location, and viewport conventions when a real harness exists.
- Git branch or PR policy.

## Included Content

- Rule: `css-guideline`
- Rule: `css-theme-styles`
- Rule: `figma-to-code`
- Rule: `plugin-package-boundary`
- Rule: `local-phase-boundary`
- Skill: `setup-playwright-mcp`
- Skill: `frontend-section-qa`
- Skill: `cpt-frontend-qa`

## Section Handoff

Start from a filled Section handoff created by the WST Builder `wst-section-workflow` Skill (older releases called it `wst-new-fc-section`). WST Builder's bundled reusable Section template lives at `plugins/wst-builder/handoffs/section-handoff.template.md`; the filled project handoff lives at the project-configured storage location from Project Context. The handoff should name the `Work type`, `Environment`, `Frontend route`, `CSS status`, target URL, template and CSS files, ACF references, CSS hooks, expected visual behavior, QA notes, and local frontend responsibilities.

If the Section handoff is missing the target URL, stable selectors, ACF or WST references, CSS path, visual requirements, local frontend responsibilities, or project-configured storage facts, stop and ask for the missing information instead of guessing.

Section handoff validators are project-local and optional unless the current project bundles one. Do not block local frontend QA on a missing `python scripts/validate-section-handoffs.py` command when the filled handoff contains the required implementation and QA facts.

After implementation, update the handoff with:

- Local frontend phase status.
- Local Playwright MCP status (`ready` or `pending: <reason>`) and any browser access blocker.
- Browser QA findings from the Playwright MCP loop: viewports checked, selectors confirmed, screenshots captured if applicable.
- Responsive check result.
- Optional project-local Playwright regression result or documented skip reason.
- Remaining risks or open questions.
- Confirmation that final CSS or SCSS-derived changes live in tracked files.

Commit the updated handoff with the local frontend code so the server-side context, implementation notes, and QA result stay together on the same branch or PR.

## CPT Foundation Handoff

Start from a filled CPT foundation handoff created by the WST Builder `wst-new-post-type` Skill when finishing CPT presentation locally. WST Builder's bundled reusable CPT template lives at `plugins/wst-builder/handoffs/cpt-handoff.template.md`; the filled project handoff lives at the project-configured CPT handoff storage location from Project Context. The handoff should name the target dev or staging URL, CPT labels, detail-page decision, taxonomy decision, display target, card/archive/grid/carousel/single templates, WP Grid Builder IDs or explicit no-WPGB decision, stable selectors, CSS or SCSS file paths, expected behavior, QA notes, and local frontend responsibilities.

If the CPT handoff is missing target URLs, stable selectors, ACF references, WP Grid Builder IDs or an explicit no-WPGB decision, visual requirements, local frontend responsibilities, project-configured storage facts, or detail-page/display decisions, stop and ask for the missing information instead of guessing.

Use `cpt-frontend-qa` for:

- CPT card CSS or SCSS implementation.
- Archive, grid, carousel, filter, or existing Section presentation around CPT cards.
- Optional public single-template frontend presentation.
- Responsive checks for card counts, repeated cards, long copy, missing images, empty optional fields, and interaction states.
- Playwright MCP browser QA for card/archive/grid visibility and optional single-page visibility, plus optional project-local Playwright regression checks when a real harness exists.

If a CPT display becomes primarily a new WST Section layout, use `frontend-section-qa` for the Section-level layout behavior and keep CPT card, archive/grid, carousel/filter, WPGB output, and optional single-template checks in the CPT handoff through `cpt-frontend-qa`.

After implementation, write QA results, responsive findings, Playwright MCP browser QA findings, optional project-local Playwright regression result or documented skip reason, changed CSS/SCSS files, generated CSS notes, Chrome Local Overrides disposition, cache notes, risks, and open questions back to the same CPT handoff.

## Package Boundary Guard Rail

Plugin consumers only have access to files inside the installed plugin package. Any required Skill, Rule, reference, checklist, script, validator, or workflow contract named by this plugin must be bundled inside `plugins/frontend-design-qa` in this development repository, or explicitly marked as optional and external.

`PROJECT-CONTEXT.md` is project-local. Handoff validators, project-local Playwright test commands, build commands, browser URLs, generated CSS paths, and handoff storage locations are project-local unless the current project or installed package actually provides them. The local Playwright MCP server configuration lives only in the untracked `.cursor/mcp.json` of the local Cursor workspace and is owned by `setup-playwright-mcp`. Do not reference a plugin-local Project Context template unless this package actually bundles one.

Do not make required Frontend Design QA workflows depend on development-only paths such as `.agents`, `.cursor/plugins/cache`, `weseo-smartflow-frontend-guide`, `.scratch`, or repository-local scripts. Section and CPT handoff templates remain owned by WST Builder, while filled handoffs and project values remain project-local.

## Chrome Local Overrides

Chrome Local Overrides are useful for short visual spikes against a real WordPress page. They are never the final source of truth. Copy successful declarations into tracked project files, rebuild generated CSS if needed, and discard the override.

## Cache And Server Escalation

Frontend Design QA records stale markup, stale generated CSS, cache uncertainty, or server-rendered output mismatches in the active Section or CPT handoff. Include the URL, selector, expected result, observed result, and local checks already performed.

Do not run cache flushes, WP-CLI, deployment commands, or server repair steps from this plugin. Route the action back to WordPress Server Ops or the current project's `PROJECT-CONTEXT.md` cache guidance.

## Browser QA With Playwright MCP

Playwright MCP is the primary browser-driving mechanism for Frontend Design QA. Configure it once per local workspace through `setup-playwright-mcp`, then run a focused browser loop during Section or CPT QA.

A useful first check navigates to the handoff URL, takes an accessibility or DOM snapshot, locates the primary Section class or CPT grid/card selector, captures a screenshot when the project workflow uses screenshots, verifies key content or item count, and repeats visibility checks at the project's desktop, tablet, and mobile viewports. When public CPT single templates exist, include one representative single URL from the handoff.

Use Playwright MCP for the interactive CSS or SCSS loop: navigate, inspect snapshots and computed styles or bounding boxes when available, edit tracked CSS or SCSS, rebuild generated CSS when required, reload, and re-check viewports until the handoff's expected visual behavior holds.

## Optional Project-Local Playwright Regression

Project-local Playwright test files and a project test runner are optional persistent regression checks, not the primary browser-driving mechanism. Run the project's Playwright command only when `PROJECT-CONTEXT.md` or the handoff provides one. When no project-local harness exists, the Playwright MCP browser QA loop is the acceptance path; document that decision and any browser access blockers in the handoff.

## Not Included

- WST Builder Plugin or server-side Section creation.
- WordPress Server Ops Plugin or Remote-SSH operations.
- Project-specific token values, URLs, repository details, or deployment instructions.
- A full local WordPress development environment.
- Pixel-perfect implementation for a real design without project context.
