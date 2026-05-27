---
name: wst-section-workflow
description: Plan, classify, and execute server-side WST Flexible Content Section work. Use for any new Section, existing Section remodel, or Section-related preflight before local frontend work. Section visual-only changes route directly to Frontend Design QA `frontend-section-qa`.
---

# WST Section Workflow

This Skill is the single entry point for any WST Flexible Content Section work owned by the server phase. It classifies the request, runs the preflight as a write gate, protects existing Section artifacts during remodels, and emits or updates the Section handoff that Frontend Design QA consumes.

This Skill does not own final Section CSS or SCSS. Server-side WST Builder may document CSS paths, stable classes, hooks, and expected behavior in the handoff. Final CSS/SCSS implementation belongs to the local frontend phase via `frontend-section-qa`.

Older guidance referred to this Skill as `wst-new-fc-section`. That name is replaced by `wst-section-workflow` so the workflow can cover new, remodel, and visual-only Section requests without forcing every request into a new-Section path.

## Hard Stop Rules

Apply these rules before any other action.

Do not perform any write operation before the Section preflight has produced a concrete handoff draft.
Write operations include PHP, ACF, Flexible Content, CSS/SCSS, style loader registration, WP-CLI, cache flushes, and WordPress content edits.

If work type is `unclear`, stop and ask for the missing decision.
If work type is `visual-only`, stop server-side work and route to Frontend Design QA `frontend-section-qa`.
If environment is `live` or `unknown`, do not write until the maintainer explicitly confirms the exact write scope.

For `existing-section-remodel`, do not create a new field group, Flexible Content layout, clone child field, template file, CSS file, or style loader entry unless the confirmed handoff explicitly says the remodel requires a new artifact.
Preserve existing layout name, layout key, parent_layout, field keys, template path, and selectors by default.

WST Builder may document CSS paths, stable classes, hooks, and expected behavior in the handoff.
WST Builder must not create or edit final Section CSS/SCSS over Remote-SSH. Missing CSS entry points are recorded as local frontend work for `frontend-section-qa`.

## Quick Start

1. Classify the request into a `Work type`.
2. Immediately ask for the Figma link or source design for the Section and whether the Section has multiple variants.
3. Identify the `Environment`.
4. Run the shortened Section preflight to produce a prefilled Section handoff draft at the project-configured storage location.
5. For `new-section-foundation` and confirmed `existing-section-remodel`, perform only the explicitly approved server-side steps.
6. For `visual-only`, route to `frontend-section-qa` without server-side writes.
7. Emit or update the Section handoff and hand off to `frontend-section-qa`.

Before asking the maintainer for technical values, search the project-local context for:

- WST template path and theme CSS path.
- Flexible Content field key and field post ID.
- Standard clone group keys for content, button, and layout.
- WP-CLI command and cache flush command.
- Section handoff storage location and target dev or staging URL.
- Existing selectors, ACF references, layout names, page IDs, source references, and project workflow notes.

If any blocking value is missing, stop and ask the maintainer or record an explicit unresolved placeholder in the prefilled handoff draft. Do not invent ACF keys, field post IDs, project paths, URLs, selectors, storage locations, or theme values.

## 1. Classify The Work

The very first server-phase decision is the `Work type`. Until this is recorded in the handoff draft, no write is allowed.

| Work type | Meaning |
| --- | --- |
| `new-section-foundation` | A WST Section that does not exist yet. Create template, ACF section field group, Flexible Content layout entry, clone child field, registration, and CSS hook documentation. |
| `existing-section-remodel` | An existing WST Section whose markup, ACF shape, or registration needs to change. Default to preserving structure and require explicit write approval for each new artifact. |
| `visual-only` | An existing WST Section whose visual behavior should change without touching server-side template, ACF, Flexible Content, or registration. Route immediately to `frontend-section-qa`. |
| `unclear` | The request cannot be classified yet. Stop and ask before any write or read-only audit beyond Project Context. |

Route after classification:

- `new-section-foundation` -> continue with this Skill.
- `existing-section-remodel` -> continue with this Skill, but follow the remodel protections in section 4.
- `visual-only` -> stop server-side work and route to `frontend-section-qa`. Document the visual-only route in the handoff.
- `unclear` -> stop and ask.

## 2. Identify The Environment And Write Scope

The handoff draft must record:

| Field | Allowed values |
| --- | --- |
| `Environment` | `local`, `dev`, `staging`, `live`, `unknown` |
| `Server write scope` | List of `files`, `database/acf`, `content`, `cache`, or `none` |
| `Frontend route` | `frontend-section-qa`, `not-needed-yet`, or `blocked` |

When `Environment` is `live` or `unknown`, do not perform any write until the maintainer explicitly confirms:

- exact environment,
- exact files or DB/ACF objects to change,
- exact write scope,
- expected rollback path,
- whether a cache flush is part of the scope.

Use a concrete confirmation prompt such as `May I change exactly these files/ACF objects on <environment>?`. Do not use broad prompts like `ok?`.

Cache flush on a live or unknown environment requires its own explicit confirmation, even if other server writes were already approved.

## 3. Mandatory Preflight As Write Gate

Run the shortened Section preflight before any implementation. The preflight output must be a concrete Section handoff draft at the project-configured storage location, not only a chat summary.

The Section preflight is evidence-first and intentionally short. After `Work type` is classified, ask the maintainer only:

1. `Please send the Figma link or source design for this Section.`
2. `Does this Section have multiple variants/states that should be implemented? If yes, which ones?`

Then inspect the Figma design, project-local context, existing Section patterns, rendered markup when available, and ACF/WST references before asking anything else. Do not ask the maintainer to specify HTML structure, wrapper classes, field names, selectors, spacing, responsive behavior, or interaction details that are visible in Figma or inferable from existing project patterns.

If a detail is not visible in Figma and not discoverable in project-local context, choose the project-default pattern when there is one and record the assumption in the handoff. Ask a follow-up only when the missing answer blocks a safe server-side write, such as an unknown environment, handoff storage location, target page URL, ACF/Flexible Content reference, or explicit live/unknown write scope.

If the agent cannot invoke Figma tooling, continue from the supplied source design or written brief and record the Figma access blocker in the handoff. Do not replace the short Section preflight with a broad design interview.

The draft must record:

- `Work type` and routing decision.
- `Environment` and `Server write scope`.
- `Frontend route`.
- Project-configured handoff storage location.
- Target URL or planned verification URL.
- Section name, layout name, source design or written brief, and variants/states.
- WST template path and CSS path.
- ACF section field group, Flexible Content field, layout key/name, clone child field, and Section-specific fields.
- Primary section class, wrapper classes, custom properties, and selectors to preserve.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Server phase status, cache status, known risks, open questions, and unresolved placeholders.
- `Preflight gate status`, Figma/source status, and `Protected existing artifacts` for remodels.
- `CSS status` from `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

Do not proceed past the preflight while the draft contains unresolved placeholders for `Work type`, `Environment`, `Server write scope`, `Frontend route`, target URL, ACF or WST references required for the approved write, CSS path, local frontend responsibilities, or storage location.

Visual behavior, markup shape, selectors, and Section-specific fields may be recorded as design-derived assumptions when the Figma/source design and project patterns provide enough evidence. They do not require separate maintainer confirmation unless the assumption changes server-side data shape, removes an existing public selector, or creates a live/unknown write risk.

If the storage location itself is unknown, stop and ask. Do not invent a path.

## 4. Existing Section Remodel Protections

When `Work type` is `existing-section-remodel`, the default position is to preserve everything that exists.

Before any write:

- Read the existing Section template, ACF section field group, Flexible Content layout entry, clone child field, registration, CSS path, and the rendered HTML classes.
- Record those values in the handoff draft under `Protected existing artifacts`.
- Propose the exact write scope to the maintainer and wait for explicit approval before changing files or ACF/DB objects.

While remodeling, by default:

- Preserve existing `layout` name, generated layout key, and `parent_layout`.
- Preserve existing ACF field keys and clone child field keys.
- Preserve the existing Section template path.
- Preserve public selectors that templates, scripts, or styles rely on.

Do not create a new template file, ACF field group, Flexible Content layout, clone child field, CSS file, or style loader entry during a remodel unless the confirmed handoff explicitly says the remodel requires that new artifact.

If the desired change only affects markup inside the existing WST template structure, leave ACF and Flexible Content untouched.

If the desired change only affects visual spacing, typography, color, responsive behavior, or hover/focus state, switch `Work type` to `visual-only` and route to `frontend-section-qa` instead of continuing server-side work.

## 5. New Section Foundation Steps

Run these steps only when `Work type` is `new-section-foundation` and the handoff draft is filled.

```text
New WST FC Section:
- [ ] Preflight produced concrete handoff draft (no unresolved blockers)
- [ ] Work type confirmed as `new-section-foundation`
- [ ] Environment recorded; live/unknown writes explicitly approved
- [ ] Create Section template
- [ ] Create ACF section field group
- [ ] Add Flexible Content layout entry
- [ ] Create clone child field with matching parent_layout
- [ ] Register Section in flexible-content.php
- [ ] Document CSS hooks and CSS path in handoff (no CSS file is created over Remote-SSH)
- [ ] Flush project caches (only when in scope and approved)
- [ ] Emit or update Section handoff and route to frontend-section-qa
```

### 5.1 Create Section Template

Create `smart-template-builder/sections/<section-slug>.php` or the equivalent project-local WST sections path.

Required template invariants:

- Guard direct access with `if (! defined('ABSPATH')) exit;`.
- Wrap the template with `{{conditional_logic_start}}` and `{{conditional_logic_end}}` when the project uses those WST placeholders.
- Use the primary section class `.wso-section-<section-slug>`.
- Include the project layout, section ID, and tabindex WST elements when present in the existing WST templates.
- Keep custom markup inside WST row, wrap, column, and column attribute classes that match the project pattern.

### 5.2 Create ACF Section Field Group

Create an `acf-field-group` for the Section. It is cloned into the Flexible Content layout and normally has no direct location rule.

Standard structure:

- Tab field for the Section label.
- Optional editor message.
- Content clone, using the project-local content clone group key.
- Button clone, using the project-local button clone group key.
- Layout clone, using the project-local layout clone group key.
- Section-specific custom fields from menu order `5` onward.

Use ACF field group posts or the project's established ACF tooling. Do not place `acf_add_local_field()` snippets in `functions.php`; that file is forbidden for agent edits. Only use `theme-functions.php` or MU plugin files for local PHP field registration after explicit prior user confirmation for that exact change.

Use `reference.md` for reusable FC Section field group, clone group, and field-shape guidance. Treat all keys, IDs, paths, labels, and URLs in that reference as placeholders supplied by Project Context or the Section handoff draft.

### 5.3 Add Flexible Content Layout

Update the project Flexible Content field using the project-local field key or field post ID.

Add a layout entry with:

- `key`: generated layout key.
- `name`: `layout_<section_slug_with_underscores>`.
- `label`: human-readable Section label.
- `display`: usually `block`.

Record the generated layout key immediately. The clone child field must reference it exactly.

Use `reference.md` for generic Flexible Content layout wiring and `parent_layout` guidance.

### 5.4 Create Clone Child Field

Create an `acf-field` child under the Flexible Content field post.

Required clone settings:

- `type`: `clone`.
- `clone`: the new Section field group key.
- `display`: `seamless`.
- `prefix_name`: `1`.
- `prefix_label`: `0`.
- `parent_layout`: the generated layout key from step 5.3.
- `acfe_save_meta`: `1` when the project uses ACF Extended save-meta behavior.

The `parent_layout` value is the critical binding between the Flexible Content layout and the cloned Section fields. If it does not match the layout key, the editor fields will appear under the wrong layout or not appear at all.

### 5.5 Register The Section

Add the Section include inside the project's `[wst_acf_flexible_content]` block:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

Keep the registration order consistent with the project's existing editor grouping.

## 6. CSS Hooks Belong In The Handoff

WST Builder records CSS hook expectations in the handoff. WST Builder does not create or edit final Section CSS or SCSS over Remote-SSH.

In the handoff, capture:

- `Primary section class`: usually `.wso-section-<section-slug>`.
- `Wrapper/classes`: any wrapper or item classes that template markup relies on.
- `CSS custom properties`: initial custom property names if the Section needs local styling tokens.
- `Selectors to preserve`: any selectors that templates, scripts, or tests rely on.
- `CSS status`: `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

When the project requires a new CSS file or style loader entry for the Section, record that requirement in the handoff so `frontend-section-qa` can create or register the file in tracked local source. Do not create or edit Section CSS files, generated CSS files, or style loader registrations over Remote-SSH from this Skill.

If the project requires server-side style loader registration as a strict prerequisite for any frontend visibility, treat that as an exception that must be explicitly approved in the handoff under `Server write scope` and limited to that registration only.

## 7. Flush And Verify Server State

Only run the project-local cache flush command when it is inside the approved `Server write scope` and, on `live` or `unknown` environments, explicitly confirmed.

Then verify:

- The target page loads.
- The editor can select the new or modified layout.
- The template renders without PHP errors.
- The expected section class exists in the page markup.

Record the cache and verification results in the handoff `Cache state` and `QA notes` fields.

## 8. Emit Or Update Section Handoff

Use the bundled `handoffs/section-handoff.template.md` as the reusable contract source, then create or update the concrete handoff at the project-configured storage location from Project Context. Keep the concrete handoff on the same branch or PR as the Section work.

Before local frontend work starts, the handoff must record:

- Handoff carrier, owner, project-configured storage location, and server phase status.
- `Work type`, `Environment`, `Server write scope`, `Frontend route`, `Preflight gate status`, and `CSS status`.
- Section name, layout name, target page URL, and source reference.
- Template file, CSS file, ACF section field group, Flexible Content field, layout key/name, clone child field, and ACF fields.
- Primary section class, wrapper classes, custom properties, and selectors to preserve.
- `Protected existing artifacts` for remodels.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Server responsibilities completed, cache state, known risks, and open questions.

If the consuming project provides a handoff validator, run that project-local command. Do not depend on repository-local scripts that are not bundled with this plugin.

Completed Section handoffs route to the Frontend Design QA `frontend-section-qa` Skill. Treat the filled Section handoff as the shared workflow contract between WST Builder server-side ownership and Frontend Design QA local implementation ownership.

## Generic Examples

### Example A: New `feature-cards` Section foundation

- `Work type`: `new-section-foundation`.
- Template: `smart-template-builder/sections/feature-cards.php`.
- Layout name: `layout_feature_cards`.
- Primary class: `.wso-section-feature-cards`.
- ACF group: `<section-field-group-key>` from the project-local ACF setup.
- Flexible Content field: `<fc-field-key>` or `<fc-post-id>` from project context.
- `CSS status`: `new-needed-for-frontend`. The handoff records `styles/sections/feature-cards.css` as a path `frontend-section-qa` will create or register, not as a file this Skill writes.
- Handoff: records the generated layout key, created fields, target URL, cache state, local frontend checklist, and the next Skill route to `frontend-section-qa`.

### Example B: Existing `intro` Section remodel

- `Work type`: `existing-section-remodel`.
- `Protected existing artifacts`: existing layout name `layout_intro`, layout key, ACF field keys, template path `smart-template-builder/sections/intro.php`, CSS path `styles/sections/intro.css`, and the `.wso-section-intro` selector and any wrapper classes.
- Read-only audit confirms existing template, ACF, and registration before proposing the write scope.
- Approved `Server write scope`: only the existing template file change required by the remodel; no new field group, no new layout, no new clone child field, no new CSS file.
- `CSS status`: `existing`. Visual refinements are routed to `frontend-section-qa`.
- Handoff: records the exact server-side change, preserved artifacts, and the next Skill route to `frontend-section-qa`.

### Example C: Visual-only `intro` change

- `Work type`: `visual-only`.
- `Server write scope`: `none`.
- This Skill stops server-side work and routes directly to `frontend-section-qa`.
- Handoff records the visual-only routing decision and the existing Section identity so `frontend-section-qa` can pick up the request without re-asking server-side questions.

### Example D: Live or unknown environment

- `Environment`: `live` or `unknown`.
- No write occurs until the maintainer explicitly confirms the exact files, ACF objects, content, and cache scope to change.
- Cache flushes require their own explicit confirmation, even when other server writes were already approved.
- Handoff records the confirmed scope and any deferred actions as open questions.
