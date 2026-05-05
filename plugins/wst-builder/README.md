# WST Builder Plugin

Reusable WST workflow guidance for creating WordPress, ACF, and WESEO Smart Template Builder section foundations.

This plugin covers server-side WST foundation work. It creates or updates WST templates, ACF field groups, Flexible Content layout wiring, Custom Post Type foundations, WP Grid Builder card/grid foundations, CSS hooks, cache notes, and handoffs consumed by local frontend work. Values that differ per project belong in `project-template/PROJECT-CONTEXT.md`.

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
- Emit or update a handoff on the same branch or PR.

Do not use this plugin as the source of truth for final visual implementation. Final CSS/SCSS refinement, Chrome Local Overrides spikes, responsive visual checks, and Playwright acceptance checks belong to the local frontend phase.

## Project Context Required

Before using the Skill, fill or locate these project-local values:

- WST template path.
- Theme path and CSS sections path.
- Flexible Content field key and field post ID.
- Standard clone group keys for content, button, and layout.
- WP-CLI command and cache flush command.
- CPT naming conventions, URL slug decisions, ACF CPT fields, and WP Grid Builder grid/card IDs.
- Section handoff location.
- Target dev or staging URL for verification.

## Included Content

- Rule: `acf-wst-patterns`
- Skill: `wst-new-fc-section`
- Skill: `wst-new-post-type`

## Handoffs

After the server-side Section foundation exists, update a Section handoff from `section-handoffs/section-handoff.template.md`. The handoff should record the Section identity, page URL, template file, CSS file, ACF references, CSS hooks, cache state, expected visual behavior, QA notes, and remaining local frontend responsibilities.

After a CPT foundation exists, update the same branch or PR with a CPT foundation handoff. It should record the CPT name, labels, detail-page decision, taxonomy decision, ACF field group, WP Grid Builder grid/card IDs, card template files, optional single template files, selectors to preserve, cache state, open questions, and remaining local frontend responsibilities.

## Not Included

- Frontend Design QA Plugin.
- Playwright check implementation.
- WordPress Server Ops media import workflow.
- Real project paths, field IDs, URLs, or access setup.
- Real CPT names, URL slugs, WPGB IDs, ACF keys, or repository values.
- A full local WordPress runtime.
