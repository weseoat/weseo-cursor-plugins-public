# WST Builder Plugin

Reusable WST workflow guidance for creating WordPress, ACF, WP Grid Builder, and WESEO Smart Template Builder Section and CPT foundations.

This plugin covers server-side WST foundation work. It creates or updates WST templates, ACF field groups, Flexible Content layout wiring, Custom Post Type foundations, WP Grid Builder card/grid foundations, CSS hooks, cache notes, and handoffs consumed by local frontend work. Values that differ per project belong in the project-local `PROJECT-CONTEXT.md` or concrete handoffs.

For the full WESEO WordPress/WST delivery workflow, install this plugin together with `wordpress-server-ops` and `frontend-design-qa`. WST Builder owns the pre-frontend foundation phase: Section and CPT foundations, bundled Section and CPT handoff templates, ACF/WST/CPT/WPGB invariants, and concrete handoffs that let Frontend Design QA continue without guessing project facts.

PHP bootstrap files are sensitive across the full workflow. `functions.php` is forbidden for agent edits. `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change.

The plugin package is the canonical reusable workflow source. Legacy SmartFlow guide material can be used as source material or archive, but should not be maintained as a second copy of the workflow or copied into reusable plugin guidance with project-specific values intact.

## Responsibility

Use this plugin for WST builder work:

- Review reusable ACF/WST structure for Flexible Content layouts, clone child fields, WST shortcodes, and Section template invariants.
- Create a Flexible Content Section template.
- Create the matching ACF section field group.
- Add the Flexible Content layout entry.
- Create the clone child field with the matching `parent_layout`.
- Register the Section include in `flexible-content.php`.
- Create the initial CSS file and stable selector hooks.
- Create a CPT foundation with optional taxonomy, ACF CPT fields, WP Grid Builder grid/card setup, card template foundation, and optional single template foundation.
- Flush the project cache using the project-local command.
- Run the bundled `grill-me` preflight and emit or update a handoff draft on the same branch or PR.

Do not use this plugin as the source of truth for final visual implementation. Final CSS/SCSS refinement, Chrome Local Overrides spikes, responsive visual checks, and Playwright acceptance checks belong to the local frontend phase.

## Project Context Required

Before using the Skill, fill or locate these project-local values:

- WST template path.
- Theme path and CSS sections path.
- Flexible Content field key and field post ID.
- Standard clone group keys for content, button, and layout.
- WP-CLI command and cache flush command.
- CPT naming conventions, URL slug decisions, ACF CPT fields, and WP Grid Builder grid/card IDs.
- Project-configured Section and CPT handoff storage locations.
- Target dev or staging URL for verification.

## Included Content

- Rule: `acf-wst-patterns`
- Rule: `cpt-wpgb-patterns`
- Rule: `plugin-package-boundary`
- Skill: `grill-me`
- Skill: `wst-new-fc-section`
- Reference: `wst-new-fc-section/reference.md`
- Template: `handoffs/section-handoff.template.md`
- Template: `handoffs/cpt-handoff.template.md`
- Skill: `wst-new-post-type`
- Reference: `wst-new-post-type/reference.md`
- Examples: `wst-new-post-type/examples.md`

## Handoffs

WST Builder owns the server-side half of the cross-plugin handoff contract. It runs the bundled preflight, creates the prefilled Section or CPT handoff at the project-configured storage location, and records every project-specific fact that Frontend Design QA needs before local CSS or SCSS work can begin. The handoff, not chat context or legacy source material, is the source of truth for the next phase.

Before the server-side Section foundation is created or changed, run the bundled `grill-me` preflight and create a prefilled Section handoff draft. Use the bundled reusable template at `handoffs/section-handoff.template.md` as the canonical Section handoff source, then store the concrete handoff at the project-configured location from Project Context. The handoff should record the Section identity, page URL, template file, CSS file, ACF references, CSS hooks, cache state, expected visual behavior, QA notes, open questions, and remaining local frontend responsibilities.

Completed Section handoffs route to the Frontend Design QA `frontend-section-qa` Skill. The filled handoff is the shared contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership.

Before the server-side CPT foundation is created or changed, run the bundled `grill-me` preflight and create a dedicated CPT handoff draft. Use the bundled reusable template at `handoffs/cpt-handoff.template.md` as the canonical CPT handoff source, then store the concrete handoff at the project-configured CPT handoff storage location from Project Context. Keep this separate from Section handoff content. It should record the CPT name, labels, detail-page decision, taxonomy decision, ACF field group, WP Grid Builder grid/card IDs or explicit no-WPGB decision, card template files, archive/grid integration, optional carousel/filter behavior, optional single template files, selectors to preserve, expected responsive and interaction behavior, cache state, open questions, unresolved placeholders, and remaining local frontend responsibilities.

Completed CPT handoffs route to the Frontend Design QA `cpt-frontend-qa` Skill. The filled handoff is the shared contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership. If a CPT display becomes primarily a dedicated WST Section layout, record the split between `frontend-section-qa` for Section layout behavior and `cpt-frontend-qa` for CPT card, archive/grid, and optional single-template behavior.

Stop instead of guessing when the preflight cannot resolve blocking values such as target URLs, ACF/WST references, field post IDs, WP Grid Builder IDs, stable selectors, CSS paths, cache commands, handoff storage, or branch/PR carrier. Keep those values in Project Context or concrete handoffs only; reusable plugin docs must use placeholders and generic examples.

## Package Boundary Guard Rail

Plugin consumers only have access to files inside the installed plugin package. Any required Skill, Rule, reference, template, checklist, or workflow contract named by this plugin must be bundled inside the plugin package, which is `plugins/wst-builder` in this development repository, or explicitly marked as optional and external.

Do not make required WST Builder workflows depend on development-only paths such as `.agents`, `.cursor/plugins/cache`, `weseo-smartflow-frontend-guide`, `.scratch`, or repository-local scripts. Use the `plugin-package-boundary` Rule when changing this package, and stop before shipping if a required workflow reference points outside the plugin folder.

## Not Included

- Frontend Design QA Plugin.
- Playwright check implementation.
- WordPress Server Ops media import workflow.
- Real project paths, field IDs, URLs, or access setup.
- Real CPT names, URL slugs, WPGB IDs, ACF keys, or repository values.
- A full local WordPress runtime.
