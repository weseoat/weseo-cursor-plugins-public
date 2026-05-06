# WST Builder Plugin

Reusable WST workflow guidance for creating WordPress, ACF, WP Grid Builder, and WESEO Smart Template Builder Section and CPT foundations.

This plugin covers server-side WST foundation work. It creates or updates WST templates, ACF field groups, Flexible Content layout wiring, Custom Post Type foundations, WP Grid Builder card/grid foundations, CSS hooks, cache notes, and handoffs consumed by local frontend work. Values that differ per project belong in `project-template/PROJECT-CONTEXT.md`.

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
- Run the required `grill-me` preflight and emit or update a handoff draft on the same branch or PR.

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
- Skill: `wst-new-fc-section`
- Reference: `wst-new-fc-section/reference.md`
- Skill: `wst-new-post-type`
- Reference: `wst-new-post-type/reference.md`
- Examples: `wst-new-post-type/examples.md`

## Handoffs

WST Builder owns the server-side half of the cross-plugin handoff contract. It runs the required preflight, creates the prefilled Section or CPT handoff at the project-configured storage location, and records every project-specific fact that Frontend Design QA needs before local CSS or SCSS work can begin. The handoff, not chat context or legacy source material, is the source of truth for the next phase.

Before the server-side Section foundation is created or changed, run a `grill-me` preflight and create a prefilled Section handoff draft. Use `section-handoffs/section-handoff.template.md` as the reusable contract source, then store the concrete handoff at the project-configured location from Project Context. The handoff should record the Section identity, page URL, template file, CSS file, ACF references, CSS hooks, cache state, expected visual behavior, QA notes, open questions, and remaining local frontend responsibilities.

Completed Section handoffs route to the Frontend Design QA `frontend-section-qa` Skill. The filled handoff is the shared contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership.

Before the server-side CPT foundation is created or changed, run a `grill-me` preflight and create a dedicated CPT handoff draft at the project-configured storage location from Project Context. Keep this separate from the Section handoff template. It should record the CPT name, labels, detail-page decision, taxonomy decision, ACF field group, WP Grid Builder grid/card IDs or explicit no-WPGB decision, card template files, archive/grid integration, optional single template files, selectors to preserve, expected responsive and interaction behavior, cache state, open questions, unresolved placeholders, and remaining local frontend responsibilities.

Completed CPT handoffs route to the Frontend Design QA `cpt-frontend-qa` Skill. The filled handoff is the shared contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership. If a CPT display becomes primarily a dedicated WST Section layout, record the split between `frontend-section-qa` for Section layout behavior and `cpt-frontend-qa` for CPT card, archive/grid, and optional single-template behavior.

Stop instead of guessing when the preflight cannot resolve blocking values such as target URLs, ACF/WST references, field post IDs, WP Grid Builder IDs, stable selectors, CSS paths, cache commands, handoff storage, or branch/PR carrier. Keep those values in Project Context or concrete handoffs only; reusable plugin docs must use placeholders and generic examples.

## Not Included

- Frontend Design QA Plugin.
- Playwright check implementation.
- WordPress Server Ops media import workflow.
- Real project paths, field IDs, URLs, or access setup.
- Real CPT names, URL slugs, WPGB IDs, ACF keys, or repository values.
- A full local WordPress runtime.
