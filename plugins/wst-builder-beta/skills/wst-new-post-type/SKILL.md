---
name: wst-new-post-type
description: Plan, classify, and execute server-side WST Custom Post Type work. Use for any new CPT foundation, existing CPT remodel, CPT/WPGB/card preflight, taxonomy or ACF CPT changes, optional single templates, or CPT handoff work. CPT visual-only changes route directly to Frontend Design QA `cpt-frontend-qa`.
---

# WST New Post Type

This Skill is the single entry point for WST Custom Post Type work owned by the server phase. It classifies the request, runs the preflight as a write gate, protects existing CPT artifacts during remodels, and emits or updates the CPT handoff that Frontend Design QA consumes.

This Skill does not own final CPT CSS or SCSS. Server-side WST Builder may document CSS paths, stable classes, hooks, and expected behavior in the handoff. Final CSS/SCSS implementation belongs to the local frontend phase via `cpt-frontend-qa`; Section-level CPT displays may also route Section behavior to `frontend-section-qa`.

## Hard Stop Rules

Apply these rules before any other action.

Do not perform any write operation before the CPT preflight has produced a concrete handoff draft.
Write operations include CPT registration, taxonomy registration, ACF, WP Grid Builder, PHP templates, CSS/SCSS, style loader registration, WP-CLI, cache flushes, and WordPress content edits.

If work type is `unclear`, stop and ask for the missing decision.
If work type is `visual-only`, stop server-side work and route to Frontend Design QA `cpt-frontend-qa`.
If environment is `live` or `unknown`, do not write until the maintainer explicitly confirms the exact write scope.

For `existing-cpt-remodel`, do not create a new CPT, taxonomy, ACF field group, WPGB grid/card, card template, archive integration, single template, CSS file, or style loader entry unless the confirmed handoff explicitly says the remodel requires a new artifact.
Preserve existing registered post type, rewrite behavior, taxonomy names, ACF field keys, WPGB IDs, template paths, and selectors by default.

WST Builder may document CSS paths, stable classes, hooks, and expected behavior in the handoff.
WST Builder must not create or edit final CPT CSS/SCSS over Remote-SSH. Missing CSS entry points are recorded as local frontend work for `cpt-frontend-qa`.

## Quick Start

1. Classify the request into a `Work type`.
2. Immediately ask for the source brief, Figma link, or display requirement for the CPT and whether public detail pages are required.
3. Identify the `Environment`.
4. Run the bundled CPT preflight to produce a prefilled CPT handoff draft at the project-configured storage location.
5. For `new-cpt-foundation` and confirmed `existing-cpt-remodel`, perform only the explicitly approved server-side steps.
6. For `visual-only`, route to `cpt-frontend-qa` without server-side writes.
7. Emit or update the CPT handoff and hand off to `cpt-frontend-qa`, with `frontend-section-qa` noted when a dedicated Section owns layout behavior.

Use the bundled CPT/WPGB invariant guidance in `rules/cpt-wpgb-patterns.mdc` when reviewing CPT foundation decisions.

Before asking the maintainer for technical values, search the project-local context for:

- WordPress root, WST template path, theme path, and style registration path. The WST template path is theme-internal: `wp-content/themes/<child-theme>/smart-template-builder/`. The WST plugin folder `wp-content/plugins/weseo-smart-template-builder/` is the WST runtime/library and is off-limits for CPT card, archive/grid, single template, and ACF/FC writes unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath.
- CPT naming conventions, URL slug policy, and whether the CPT has a detail page.
- ACF field creation approach, field key naming expectations, and existing CPT field groups.
- WP Grid Builder grid/card conventions and where grid/card IDs are recorded.
- CPT handoff storage location, branch or PR carrier, and target dev or staging URL.
- Existing selectors, CPT references, taxonomy references, card/grid/single template paths, source references, and project workflow notes.

If any blocking value is missing, stop and ask the maintainer or record an explicit unresolved placeholder in the dedicated CPT handoff draft. Do not invent CPT names, rewrite slugs, taxonomy names, ACF keys, field post IDs, WPGB IDs, paths, URLs, selectors, storage locations, or theme values.

## 1. Classify The Work

The very first server-phase decision is the `Work type`. Until this is recorded in the handoff draft, no write is allowed.

| Work type | Meaning |
| --- | --- |
| `new-cpt-foundation` | A WST CPT that does not exist yet. Create CPT registration, optional taxonomy, ACF CPT field group, WPGB card/grid foundation, card templates, optional archive/grid integration, optional single template, and CSS hook documentation. |
| `existing-cpt-remodel` | An existing WST CPT whose registration, taxonomy, ACF shape, WPGB setup, templates, or display integration needs to change. Default to preserving structure and require explicit write approval for each new artifact. |
| `visual-only` | An existing CPT display whose visual behavior should change without touching server-side CPT, taxonomy, ACF, WPGB, template, registration, or content structure. Route immediately to `cpt-frontend-qa`. |
| `unclear` | The request cannot be classified yet. Stop and ask before any write or read-only audit beyond Project Context. |

Route after classification:

- `new-cpt-foundation` -> continue with this Skill.
- `existing-cpt-remodel` -> continue with this Skill, but follow the remodel protections in section 4.
- `visual-only` -> stop server-side work and route to `cpt-frontend-qa`. Document the visual-only route in the handoff.
- `unclear` -> stop and ask.

## 2. Identify The Environment And Write Scope

The handoff draft must record:

| Field | Allowed values |
| --- | --- |
| `Environment` | `local`, `dev`, `staging`, `live`, `unknown` |
| `Server write scope` | List of `files`, `database/acf`, `wpgb`, `content`, `cache`, or `none` |
| `Frontend route` | `cpt-frontend-qa`, `frontend-section-qa`, `not-needed-yet`, or `blocked` |

When `Environment` is `live` or `unknown`, do not perform any write until the maintainer explicitly confirms:

- exact environment,
- exact files or DB/ACF/WPGB objects to change,
- exact write scope,
- expected rollback path,
- whether a cache flush is part of the scope.

Use a concrete confirmation prompt such as `May I change exactly these files/ACF/WPGB objects on <environment>?`. Do not use broad prompts like `ok?`.

Cache flush on a live or unknown environment requires its own explicit confirmation, even if other server writes were already approved.

## 3. Mandatory Preflight As Write Gate

Run the bundled `grill-me` preflight before implementation starts. The preflight output is a dedicated CPT handoff draft created from `plugins/wst-builder/handoffs/cpt-handoff.template.md`, not only a chat summary and not the Section handoff template.

The CPT preflight is evidence-first and intentionally short. After `Work type` is classified, ask the maintainer only:

1. `Please send the source brief, Figma link, or reference for this CPT display.`
2. `Should this CPT have public detail pages? If yes, what URL slug or slug policy should it use?`

Then inspect project-local context, existing CPT patterns, rendered markup when available, WPGB setup, and ACF/WST references before asking anything else. Do not ask the maintainer to specify HTML structure, wrapper classes, field names, selectors, spacing, responsive behavior, or interaction details that are visible in the source design or inferable from existing project patterns.

If a detail is not discoverable and a project-default pattern exists, use that assumption in the handoff. Ask a follow-up only when the missing answer blocks a safe server-side write, such as an unknown environment, handoff storage location, CPT name, URL slug policy, taxonomy decision, ACF/WPGB reference, target URL, or explicit live/unknown write scope.

The draft must record:

- `Work type` and routing decision.
- `Environment` and `Server write scope`.
- `Frontend route`.
- Project-configured handoff storage location.
- CPT slug, registered post type, singular and plural labels, admin visibility, and source brief.
- Public detail-page decision, URL slug or explicit no-detail-page decision, archive/search behavior, and unresolved detail-page questions.
- Taxonomy decision, taxonomy name, labels, hierarchy, public archive decision, purpose, and unresolved taxonomy questions.
- ACF field group, field names, field ownership decisions, generated field references, and unresolved field questions.
- WP Grid Builder grid/card IDs or explicit no-WPGB decision, plus display target: grid, carousel, existing Section, or dedicated Section.
- Card, archive/grid, optional single, and optional Section integration template paths.
- Expected card, archive/grid, optional single, and Section integration selectors to preserve.
- Expected desktop, tablet, mobile, content variation, filtering, linking, and interaction behavior.
- Server phase status, cache status, known risks, open questions, local frontend responsibilities, and unresolved placeholders.
- `Preflight gate status`, source status, and `Protected existing artifacts` for remodels.
- `CSS status` from `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

Do not proceed past the preflight while the draft contains unresolved placeholders for `Work type`, `Environment`, `Server write scope`, `Frontend route`, CPT slug/name, detail-page decision, taxonomy decision, target URL or planned verification URL, ACF/WPGB references required for the approved write, template paths, CSS path, local frontend responsibilities, or storage location.

If the storage location itself is unknown, stop and ask. Do not invent a path.

## 4. Existing CPT Remodel Protections

When `Work type` is `existing-cpt-remodel`, the default position is to preserve everything that exists.

Before any write:

- Read the existing CPT registration, taxonomy registration, ACF field group, WPGB grid/card setup, template paths, CSS path, rendered HTML classes, and related handoff if present.
- Record those values in the handoff draft under `Protected existing artifacts`.
- Propose the exact write scope to the maintainer and wait for explicit approval before changing files, ACF/DB/WPGB objects, content, or cache state.

While remodeling, by default:

- Preserve existing registered post type, labels, rewrite behavior, archive/search behavior, and supports.
- Preserve existing taxonomy name, hierarchy, and public archive behavior.
- Preserve existing ACF field keys and field ownership.
- Preserve existing WPGB grid/card IDs and source settings.
- Preserve existing card, archive/grid, Section integration, and single template paths.
- Preserve public selectors that templates, scripts, WPGB behavior, styles, or tests rely on.

Do not create a new CPT registration, taxonomy, ACF field group, WPGB grid/card, template file, CSS file, or style loader entry during a remodel unless the confirmed handoff explicitly says the remodel requires that new artifact.

If the desired change only affects visual spacing, typography, color, responsive behavior, hover/focus state, carousel appearance, filter appearance, or card layout CSS, switch `Work type` to `visual-only` and route to `cpt-frontend-qa` instead of continuing server-side work.

## 5. New CPT Foundation Steps

Run these steps only when `Work type` is `new-cpt-foundation` and the handoff draft is filled.

Track progress with this checklist:

```text
New WST CPT Foundation:
- [ ] Preflight produced concrete handoff draft (no unresolved blockers)
- [ ] Work type confirmed as `new-cpt-foundation`
- [ ] Environment recorded; live/unknown writes explicitly approved
- [ ] Register CPT
- [ ] Register taxonomy if needed
- [ ] Create ACF field group for CPT fields
- [ ] Create WP Grid Builder card and grid foundation
- [ ] Create card template foundation
- [ ] Create optional single template foundation
- [ ] Document CSS hooks and CSS path in handoff (no final CPT CSS is created over Remote-SSH)
- [ ] Flush project caches and verify server state
- [ ] Emit or update CPT foundation handoff
```

### 5.1 Decide Foundation Shape

Before writing files or changing WordPress configuration, decide:

- Whether the CPT has public detail pages.
- Whether it needs a taxonomy for filtering, grouping, or card labels.
- Which data belongs in post title, featured image, excerpt/editor, ACF fields, or taxonomy terms.
- Whether WP Grid Builder should render a grid, carousel, or card only.
- Whether a dedicated WST Flexible Content Section is required, or an existing grid/slider Section can consume the grid.

Record unresolved decisions in the CPT handoff draft rather than guessing.

### 5.2 Register CPT

Register the CPT through the project's established CPT UI or equivalent registration path.

Default invariants:

- Registered post type should follow the project convention, usually `wso_<resource>`.
- `show_ui` and `show_in_rest` should be enabled unless the maintainer gives a reason not to.
- `supports` should include only fields the content model actually uses.
- Public detail-page CPTs need query, search, archive, and rewrite settings reviewed together.
- Non-detail CPTs should not create public URLs accidentally.

Use `reference.md` for reusable CPT UI settings.

### 5.3 Register Taxonomy If Needed

Add a taxonomy only when the content model requires grouping, filtering, admin columns, or card labels.

Default invariants:

- Taxonomy name usually follows `wso_tax_<resource>`.
- Attach it to the new CPT's registered post type.
- Choose hierarchical behavior deliberately: category-style for controlled groups, tag-style for loose labels.
- Disable public rewrites unless taxonomy archives are part of the brief.
- Enable REST support when editor tooling or block/admin integrations need it.

Use `reference.md` for reusable taxonomy settings.

### 5.4 Create ACF Field Group

Create an ACF field group whose location rule targets the new CPT.

Recommended structure:

- A tab field for admin organization.
- Content fields specific to the CPT.
- Optional tabs for complex CPTs.
- Field names prefixed with the CPT naming convention when the project does that already.

Use ACF field group posts or the project's established ACF tooling. Do not place `acf_add_local_field()` snippets in `functions.php`; that file is forbidden for agent edits. Only use `theme-functions.php` or MU plugin files for local PHP field registration after explicit prior user confirmation for that exact change.

Use `reference.md` for generic ACF field group and field-shape guidance.

### 5.5 Create WP Grid Builder Foundation

Create the WP Grid Builder card and grid through the project's established admin workflow.

Default WST pattern:

- The WPGB card exists so the grid can select a card.
- The visual card builder is usually left empty when WST PHP card templates render the card markup.
- The WPGB grid source should point at the new CPT.
- Record generated grid and card IDs in Project Context or the handoff.

Do not hardcode generated WPGB IDs into reusable plugin content. Treat them as project-local values.

### 5.6 Create Card Template Foundation

Create card template files in the project-owned WST source inside the active child theme. Resolve the absolute path against the active theme:

```text
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php
```

Do not place card templates, ACF includes, or any project-owned CPT artifact under `wp-content/plugins/weseo-smart-template-builder/`. That folder is the WST runtime/library and is off-limits unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath.

Common shapes:

- Single-part card: one template for the full card.
- Multi-part card: `-part-1`, `-part-2`, or more parts when WPGB expects multiple card areas.
- Custom Section rendering: a dedicated WST Section queries or embeds the grid instead of relying only on a card template.

Template invariants:

- Use stable `.wso-<resource>-card` style hooks.
- Render core post data and ACF fields through the project's WST shortcodes or established helpers.
- Use conditionals for optional fields so empty data does not leave broken markup.
- Include accessible link labels when the card has empty or full-card links.

Use `examples.md` for generic card structures.

### 5.7 Create Optional Single Template Foundation

Only create a single template when the CPT is publicly queryable and the brief requires detail pages.

Template invariants:

- Place the single template inside the active child theme at `wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/singles/<resource>-single.php`. Do not place CPT singles under `wp-content/plugins/weseo-smart-template-builder/`.
- Keep markup compatible with existing WST element, row, wrap, and typography patterns.
- Establish stable `.wso-<resource>-single` hooks.
- Record any required local CSS and QA expectations in the CPT handoff draft.

If no public detail page exists, record that decision in the CPT handoff draft so later frontend QA does not look for a single view.

## 6. CSS Hooks Belong In The Handoff

WST Builder records CSS hook expectations in the CPT handoff. WST Builder does not create or edit final CPT CSS or SCSS over Remote-SSH.

In the handoff, capture:

- `Card selector`: usually `.wso-<resource>-card`.
- `Archive/grid selector`: the wrapper selector for CPT lists.
- `Single selector`: usually `.wso-<resource>-single` when detail pages exist.
- `Wrapper/classes`: any wrapper or item classes that template markup relies on.
- `CSS custom properties`: initial custom property names if the CPT needs local styling tokens.
- `Selectors to preserve`: any selectors that templates, scripts, WPGB behavior, or tests rely on.
- `CSS status`: `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

When the project requires a new CSS file or style loader entry for the CPT display, record that requirement in the handoff so `cpt-frontend-qa` can create or register the file in tracked local source. Do not create or edit CPT CSS files, generated CSS files, or style loader registrations over Remote-SSH from this Skill.

If the project requires server-side style loader registration as a strict prerequisite for any frontend visibility, treat that as an exception that must be explicitly approved in the handoff under `Server write scope` and limited to that registration only.

## 7. Flush And Verify Server State

Only run the project-local cache flush command when it is inside the approved `Server write scope` and, on `live` or `unknown` environments, explicitly confirmed.

Then verify:

- The CPT appears in the WordPress admin.
- Taxonomy UI appears only when expected.
- ACF fields appear on the CPT edit screen.
- WP Grid Builder can select the new CPT and card.
- Card template markup renders without PHP errors.
- Public single URLs work only when the CPT is intended to have detail pages.
- Expected card or single CSS hooks exist in rendered markup.

Record the cache and verification results in the handoff `Cache state` and `QA notes` fields.

## 8. Emit Or Update CPT Foundation Handoff

Update the dedicated CPT handoff draft on the same branch or PR as the CPT foundation work. Use the bundled reusable template at `plugins/wst-builder/handoffs/cpt-handoff.template.md` as the canonical CPT handoff source, and keep the filled project handoff separate from the Section handoff template because CPT work has its own detail-page, taxonomy, WP Grid Builder, card, archive, carousel, and optional single-template decisions.

Fill these handoff areas before local frontend work starts:

- Handoff carrier, owner, project-configured storage location, and server phase status.
- `Work type`, `Environment`, `Server write scope`, `Frontend route`, `Preflight gate status`, and `CSS status`.
- CPT name, labels, detail-page decision, taxonomy decision, and display target.
- Template files for card, optional single, and any Section integration.
- ACF field group, field names, and unresolved field questions.
- WP Grid Builder grid/card IDs as project-local values.
- Expected card, archive/grid, and optional single selectors.
- `Protected existing artifacts` for remodels.
- Expected desktop, tablet, mobile, content variation, filtering, linking, and interaction behavior.
- Server responsibilities completed, cache state, known risks, open questions, and unresolved placeholders.
- Local frontend responsibilities for final CSS, responsive checks, Chrome Local Overrides spikes, local Playwright MCP browser QA, and optional project-local Playwright regression verification.

If the consuming project provides a handoff validator, run that project-local command. Do not depend on repository-local scripts that are not bundled with this plugin.

Completed CPT handoffs route to the Frontend Design QA `cpt-frontend-qa` Skill. Treat the filled CPT handoff as the shared workflow contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership. If the CPT display becomes primarily a dedicated WST Section layout, record the split between `frontend-section-qa` for Section-level behavior and `cpt-frontend-qa` for CPT card, archive/grid, and optional single-template QA in the same CPT handoff.

## Generic Examples

### Example A: New `team` CPT foundation

- `Work type`: `new-cpt-foundation`.
- Registered post type: `wso_<resource>`.
- Detail pages: explicit yes/no decision recorded before rewrites or single templates are created.
- WPGB: grid/card IDs recorded as project-local values after creation.
- `CSS status`: `new-needed-for-frontend`. The handoff records the CSS path as work for `cpt-frontend-qa`, not as a file this Skill writes.

### Example B: Existing `team` CPT remodel

- `Work type`: `existing-cpt-remodel`.
- `Protected existing artifacts`: existing registered post type, rewrite settings, taxonomy name, ACF field keys, WPGB IDs, template paths, CSS path, and public selectors.
- Approved `Server write scope`: only the existing card template change required by the remodel; no new CPT, no new taxonomy, no new ACF group, no new WPGB grid/card, no new CSS file.
- `CSS status`: `existing`. Visual refinements are routed to `cpt-frontend-qa`.

### Example C: Visual-only CPT card change

- `Work type`: `visual-only`.
- `Server write scope`: `none`.
- This Skill stops server-side work and routes directly to `cpt-frontend-qa`.
- Handoff records the existing CPT identity, display URL, selectors, and visual-only routing decision so `cpt-frontend-qa` can pick up the request without re-asking server-side questions.

### Example D: Live or unknown environment

- `Environment`: `live` or `unknown`.
- No write occurs until the maintainer explicitly confirms the exact files, ACF/WPGB objects, content, and cache scope to change.
- Cache flushes require their own explicit confirmation, even when other server writes were already approved.
- Handoff records the confirmed scope and any deferred actions as open questions.
