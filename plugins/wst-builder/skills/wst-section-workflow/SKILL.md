---
name: wst-section-workflow
description: Plan, classify, and execute server-side WST Flexible Content Section work as a productive implementation workflow with safety stops. Use for any new Section, existing Section remodel, or Section-related preflight before local frontend work. Visual-only Section changes route to Frontend Design QA `frontend-section-qa` with a minimal handoff.
---

# WST Section Workflow

This Skill is the single entry point for any WST Flexible Content Section work owned by the server phase. It classifies the request, drives the implementation, protects existing Section artifacts during remodels, and emits or updates the Section handoff that Frontend Design QA consumes.

The Skill is a productive implementation workflow with safety stops, not a preflight write gate. Reads and discovery are always allowed. Writes proceed when scope is clear and safe; they stop and ask only at concrete risk points.

This Skill does not own final Section CSS or SCSS. WST Builder may document CSS paths, stable classes, hooks, and expected behavior in the handoff. Final CSS/SCSS implementation belongs to the local frontend phase via `frontend-section-qa`, which runs locally and not over Remote-SSH.

Older guidance referred to this Skill as `wst-new-fc-section`. That name is replaced by `wst-section-workflow` so the workflow covers new, remodel, and visual-only Section requests without forcing every request into a new-Section path.

## Skill character

- Productive implementation by default. Read project context, apply the WST/ACF rules, search existing patterns, then implement inside the approved scope.
- Compact and recommendation-driven. Ask in compact rounds with a clear recommendation, not open-ended interviews.
- Safety stops only at concrete risks. Live/unknown writes, work-type reclassification, structural ambiguity, content overwrites, and cache flushes on live/unknown require explicit confirmation.
- CSS/SCSS file writes on the server are never allowed from this Skill.

## Hard safety stops

Apply these rules before any other action.

Stop and confirm before:

- Writing on `live` or `unknown` environments (files, ACF/DB, content, cache, style loader).
- Flushing cache on `live` or `unknown`, even if other writes were already approved.
- Changing the classified work type during the task. Reclassification is always a stop-and-confirm point, including when it becomes safer (for example `new-section-foundation -> visual-only`).
- Touching public selectors, layout names, layout keys, or ACF field keys that templates, scripts, or styles rely on.
- Overwriting existing page content.
- Creating new artifacts during an `existing-section-remodel` (new template file, ACF field group, Flexible Content layout, clone child field, or style loader entry) unless explicitly approved.
- Making a structural ACF/FC decision that discovery cannot resolve.

Always allowed regardless of environment:

- Reading project context, files, rendered markup, WST/ACF references.
- Read-only WP-CLI inspection.
- Reading and analyzing Figma/source.
- Lightweight Media Library lookup.
- Updating the Section handoff with discovery findings and proposed scope.

CSS/SCSS file writes on the server, edits to generated CSS, and final visual QA are never allowed from this Skill, regardless of environment.

## Workflow at a glance

1. Read project context and the WST/ACF rules.
2. Run the Start question block in one compact message; skip any question whose answer is already in project context.
3. Inspect Figma/source, search similar Sections, identify the work type, and update the handoff with `Discovery and safety status`.
4. If a structural ambiguity remains, run one Structural question block. Otherwise continue.
5. Announce a short Execution Plan before any write.
6. Implement server-side WST/ACF/FC artifacts inside the approved scope.
7. Verify server-side function and existence only.
8. Update the Section handoff with the Frontend QA Brief and route to `frontend-section-qa`.

## Question budget

Maximum three compact rounds. Each round is a single message. After the third round the Skill either applies a documented recommendation or stops and records a blocker in the handoff. It does not keep interviewing.

Questions are structural and operational. Do not ask for HTML tags, CSS classes, spacing, typography, colors, or responsive behavior when those can be derived from Figma, existing project patterns, or belong to local frontend QA.

### Start question block (after context check)

Ask in one message, only the values that project context did not already supply:

1. Figma or source design link.
2. Test placement: on which page should the Section be visible for verification?
3. For a new Section: confirm the proposed Section slug. The Skill may propose `<derived-slug>` from the Figma frame or brief; the maintainer confirms or replaces it.
4. Are there server-relevant variants or states? If none mentioned, the Skill derives them from Figma.

### Structural question block (only when needed)

Ask only after Figma analysis and pattern discovery if a server-side structural choice remains ambiguous. Always include a recommendation:

```text
Recommendation: <X> because <Y>. Confirm or correct.
```

Do not ask about visual styling, classes, or design details that local frontend QA owns.

### Failsafe question block (last resort)

If a write would otherwise risk a wrong Section structure or a risky server change, ask one more compact round. Otherwise stop and document the blocker in the handoff.

## Work type classification

Classify based on discovery, not on the wording of the request.

| Work type | Trigger |
| --- | --- |
| `new-section-foundation` | No suitable existing Section/layout/template is found; Figma or brief requires a new reusable Section. |
| `existing-section-remodel` | A matching existing Section exists and the change touches template markup, ACF, Flexible Content, or registration. Visual change alone is not enough. |
| `visual-only` | Template, ACF, and Flexible Content already fit; only CSS, spacing, typography, colors, responsive behavior, or interaction states need to change. |
| `unclear` | The Skill cannot decide after discovery. Use the Structural question block. |

Routing:

- `new-section-foundation` -> continue with this Skill.
- `existing-section-remodel` -> continue with this Skill under the in-place protections below.
- `visual-only` -> stop server-side work, create a minimal handoff, route to `frontend-section-qa`.
- `unclear` -> ask one Structural question block; if still unclear, stop and record the blocker.

### Reclassification rule

If the work type changes during discovery or implementation:

- Stop before any further write.
- Explain why the previous classification no longer fits.
- Propose the new classification and the new `Server write scope`.
- Wait for explicit confirmation.
- Update the handoff. If the handoff filename no longer matches the new work type, create a new handoff for the new task.

## Environment and write scope

Record on the handoff:

| Field | Allowed values |
| --- | --- |
| `Environment` | `local`, `dev`, `staging`, `live`, `unknown` |
| `Server write scope` | List of `files`, `database/acf`, `content`, `cache`, or `none` |
| `Frontend route` | `frontend-section-qa`, `not-needed-yet`, or `blocked` |
| `Discovery and safety status` | `context-checking`, `ready-for-safe-writes`, `write-approved`, or `blocked` |

On `live` or `unknown`:

- Reads and discovery remain allowed.
- No file, ACF/DB, content, cache, or style-loader write until the maintainer confirms exactly which artifacts may change and how to roll back.
- Cache flushes need their own explicit confirmation.

Use concrete confirmation prompts, not broad approvals:

```text
On <environment>, may I change exactly <files-or-acf-or-content>?
Rollback: <plan>
```

## Pattern discovery and WST language safety

Before any new template or remodel, orient in the project's WST dialect.

Reading order:

1. Apply [`../../rules/acf-wst-patterns.mdc`](../../rules/acf-wst-patterns.mdc) before any WST template, Flexible Content, clone field, or ACF field group write.
2. Use [`../../rules/acf-wst-patterns-reference.md`](../../rules/acf-wst-patterns-reference.md) when concrete examples or field-shape details are needed.
3. Inspect at least one similar existing Section in the project to match local conventions: `conditional_logic_start/end` placeholders, `wst_include` registration, row/wrap/column classes, section ID and tabindex elements, `get_sub_field` and clone-prefix patterns, content/button/layout clone usage.

Conflict resolution priority:

1. Explicit user decision for this task.
2. Project-local context, existing site conventions, and project examples that satisfy the invariants.
3. `acf-wst-patterns.mdc` invariants.
4. `acf-wst-patterns-reference.md` examples.
5. Generic Skill examples.

If a local example contradicts a hard invariant, do not copy it blindly. Record the conflict in the handoff as a risk and propose a corrected approach. Ask before correcting unrelated existing Sections; corrections outside the current scope are not implicit approvals.

### Searching for similar Sections

The Skill searches for a structural reference by itself before asking. Sources include Section template files under the project's `smart-template-builder/sections/` path (or equivalent), ACF section field groups, Flexible Content layouts, and rendered markup on existing pages when accessible.

Only ask when no usable reference is found, when multiple references would change the server model differently, or when the maintainer might prefer a specific reference Section. Suggested fallback prompt when needed:

```text
I did not find a clear structural reference. Do you want me to model this Section after a specific existing Section (name/file/URL)?
Otherwise I will derive it from project defaults and the WST/ACF rule.
```

## Existing Section remodel: in-place default

`existing-section-remodel` defaults to in-place. By default:

- Reuse the existing template path.
- Preserve `layout` name, generated layout key, and `parent_layout`.
- Preserve existing ACF field keys and clone child field keys.
- Preserve public selectors that templates, scripts, or styles rely on.

Do not create a new template file, ACF field group, Flexible Content layout, clone child field, or style loader entry during a remodel unless the confirmed handoff explicitly approves that new artifact.

If a desired change only affects spacing, typography, color, responsive behavior, or hover/focus, apply the reclassification rule to switch to `visual-only` and route to `frontend-section-qa`.

Record protected artifacts in the handoff under `Protected Existing Artifacts`.

## Slug, derived names, and handoff filename

For a new Section, the Skill proposes a slug derived from the Figma frame or brief and asks the maintainer to confirm.

Once the slug is confirmed, derive the rest deterministically:

| Derived | Pattern |
| --- | --- |
| Layout name | `layout_<section_slug_with_underscores>` |
| Primary class | `.wso-section-<section-slug>` |
| Template file | `smart-template-builder/sections/<section-slug>.php` (or the project's equivalent path) |
| Handoff filename | `<section-slug>-<work-type>-handoff.md` |

For `existing-section-remodel`, do not re-confirm the slug if a single existing Section is unambiguously matched. Ask only if multiple candidates apply or the maintainer wants a rename.

## Test placement

If a test placement / target page is not known from project context, ask once in the Start question block. Without a test placement:

- Foundation work (template, ACF, FC, clone child, registration) may still proceed inside scope.
- Content writes that depend on a target page must be deferred and recorded as open in the handoff.

## Execution Plan before writes

Before performing server writes, output a short Execution Plan:

```text
Plan:
- Work type: <classification>
- Server writes: <files / ACF / FC / content / cache>
- CSS: not written by this Skill (handed off to frontend-section-qa)
- Media: <reuse existing IDs / run wp-media-import / not needed>
- Handoff: <handoff-filename>
- Frontend route: frontend-section-qa
```

On `dev`, `staging`, or `local`, proceed after the plan. On `live` or `unknown`, require explicit confirmation of the plan before any write.

## New Section foundation steps

Run only when `Work type` is `new-section-foundation`, the slug is confirmed, and the handoff captures the Discovery Sources, Environment, and Server write scope.

```text
New WST FC Section:
- [ ] Discovery sources recorded (Figma, similar Sections, rules applied, test placement)
- [ ] Section slug confirmed and derived names recorded
- [ ] Execution Plan announced
- [ ] Create Section template at smart-template-builder/sections/<section-slug>.php
- [ ] Create ACF section field group
- [ ] Add Flexible Content layout entry
- [ ] Create clone child field with matching parent_layout
- [ ] Register Section in flexible-content.php
- [ ] Document CSS hooks and CSS path in handoff (no CSS file is created over Remote-SSH)
- [ ] Optionally create representative test content on the target page (in scope only)
- [ ] Flush project caches (in scope only; explicit confirmation on live/unknown)
- [ ] Update Section handoff with Frontend QA Brief and route to frontend-section-qa
```

Follow `acf-wst-patterns.mdc` for invariants and `acf-wst-patterns-reference.md` for field shapes, clone group usage, layout wiring, and registration patterns. Do not invent ACF keys, field post IDs, project paths, URLs, selectors, storage locations, or theme values.

Do not place `acf_add_local_field()` snippets in `functions.php`; that file is forbidden for agent edits. Edit `theme-functions.php` or MU plugin files only with explicit prior user confirmation for the exact change.

### Template invariants

- Guard direct access with `if (! defined('ABSPATH')) exit;`.
- Preserve `{{conditional_logic_start}}` and `{{conditional_logic_end}}` when the project uses those WST placeholders.
- Use the primary section class `.wso-section-<section-slug>`.
- Include the project layout, section ID, and tabindex WST elements when present in nearby templates.
- Keep custom markup inside WST row, wrap, column, and column attribute classes that match the project pattern.

### Flexible Content wiring

- Add the layout entry under the project Flexible Content field using the project-local field key or field post ID.
- Generate the layout key once and record it immediately. The clone child field must reference it exactly through `parent_layout`.
- Standard clone settings on the child field: `type=clone`, `clone=<section-field-group-key>`, `display=seamless`, `prefix_name=1`, `prefix_label=0`, `parent_layout=<generated-layout-key>`, `acfe_save_meta=1` when the project uses ACF Extended save-meta behavior.

### Registration

Add the Section include inside the project's `[wst_acf_flexible_content]` block:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

Keep the registration order consistent with the project's existing editor grouping.

## CSS boundary

`wst-section-workflow` never writes or edits CSS or SCSS over Remote-SSH.

Allowed:

- Detect CSS needs from Figma and existing patterns.
- Document CSS paths, primary class, wrapper classes, custom properties, and selectors to preserve in the handoff.
- Set stable, predictable hook classes in the template markup.
- Record `CSS status` (`existing`, `new-needed-for-frontend`, `unknown`, `not-applicable`).

Not allowed:

- Creating or editing Section CSS/SCSS files on the server.
- Editing generated CSS.
- Final responsive QA, pixel-level visual checks, or hover/focus styling.

Style loader registration on the server is allowed only as a documented exception when a project strictly requires it for frontend visibility, and only after explicit `Server write scope` confirmation limited to that registration.

When a new CSS file or style loader entry is needed, record it in the handoff under `CSS status = new-needed-for-frontend` so `frontend-section-qa` can create or register it locally in tracked source.

## Media handling

After Figma or source analysis, perform a lightweight Media Library check whether the required assets already exist. Keep this token-efficient, not an exhaustive audit.

- If matches are quickly identifiable, use the existing Media IDs and record them in the handoff under `Discovery Sources`.
- If matches are not quickly identifiable and the Section needs concrete assets to be testable, invoke `wp-media-import` as an explicit sub-flow inside an approved Media write scope. Document the imported Media IDs in the handoff.
- Ask before importing on `live` or `unknown` environments, when asset rights are unclear, or when many candidate assets exist.
- `wp-media-import` is an external Skill bundled with `wordpress-server-ops`. The Section workflow may invoke it as a documented sub-flow; it does not duplicate Media import logic itself.

## Server verification (function and existence only)

After server-side work, verify only the server-side function:

- Target page loads without PHP fatal errors or new warnings.
- Section markup is present in the rendered page.
- The primary class `.wso-section-<section-slug>` is present in the page markup.
- The layout is selectable in the editor or present in ACF where checkable.

Pixel-perfect rendering, responsive layout, spacing, typography, colors, and interaction states belong to `frontend-section-qa` and are not part of this Skill's verification.

Record server verification results in the handoff under `QA Notes`.

## Visual-only routing

If `Work type` is `visual-only`:

1. Identify the existing Section first using project discovery (template path, layout key/name, primary class) before asking.
2. Do not perform server writes.
3. Create a minimal handoff `<section-slug>-visual-only-handoff.md` with the existing Section identity, Figma/source link, target URL, stable classes/hooks, CSS status, and a clear `No server writes required` note.
4. Route to `frontend-section-qa`.

If discovery cannot identify the existing Section unambiguously, ask one compact Structural question with candidate Sections listed. Do not write until the maintainer confirms which Section is the target.

## Handoff lifecycle

The Section handoff is the live contract between server WST work and local Frontend Design QA. It is created during this Skill on the Remote-SSH WordPress workspace and consumed locally by `frontend-section-qa`. The project-configured handoff storage location is on the `.gitignore` allowlist that `setup-orientation` installs, so the handoff is tracked in Git and travels between the two workspaces through a normal commit, push, and pull cycle.

- One new handoff per Section task: `<section-slug>-<work-type>-handoff.md` at the project-configured handoff storage location.
- Commit and push the handoff immediately after it is created, so `frontend-section-qa` can pull it locally without waiting on a manual transfer.
- Pass the original Figma/source link unchanged so `frontend-section-qa` can re-read the design.
- Update the handoff progressively during discovery, decisions, writes, and verification. After each meaningful update (Discovery and safety status, Discovery Sources, Frontend QA Brief, server verification outcome), commit and push the handoff so the local side sees the latest contract on its next `git pull`.
- For remodels, fill `Protected Existing Artifacts`.
- For visual-only, fill the minimal Visual-Only path of the template.
- Do not put secrets, tokens, application passwords, SSH keys, token-bearing URLs, dumps, or full media inventories into the handoff, because it travels through the shared Git repository.

Cleanup is owned by `frontend-section-qa`:

- After successful local frontend QA, it writes a short permanent project note (for example in `LEARNINGS.md` or the project's context doc) summarizing what was built or changed.
- It then removes the active handoff file with `git rm`, commits the removal, and pushes so the server-side workspace sees the closed task on its next `git pull`.

This Skill must include those completion instructions in the handoff so the cleanup is not forgotten.

## Frontend QA Brief in the handoff

When server work or visual-only routing is complete, write a compact `Frontend QA Brief` into the handoff so `frontend-section-qa` can start without re-asking server-side questions:

```text
## Frontend QA Brief

- Use `frontend-section-qa` locally (not over Remote-SSH).
- Target URL: <dev-or-staging-url>
- Section selector: .wso-section-<section-slug>
- Figma/source link: <figma-url-or-brief>
- CSS status: existing / new-needed-for-frontend / unknown / not-applicable
- Required viewports and expected behavior: <summary>
- Stable hooks to preserve: <selectors>
- Server contract: do not change ACF/WST artifacts from the local phase. Report a server blocker in the handoff if a server-side discrepancy is found.
- On completion: write a short permanent project note and remove this active handoff with `git rm`, commit, and push so both workspaces converge on the closed task.
```

The Frontend QA Brief is a verifiable starting point, not a blind directive. `frontend-section-qa` re-reads the Figma link and the rendered page locally, and may report contradictions back into the handoff instead of silently working around them.

## Package boundary

When editing this Skill or related plugin files, follow [`../../rules/plugin-package-boundary.mdc`](../../rules/plugin-package-boundary.mdc). Required workflow files must be bundled inside the plugin package. References to `frontend-section-qa` and `wp-media-import` remain explicit external Skills.

See [`../../handoffs/section-handoff.template.md`](../../handoffs/section-handoff.template.md) for the bundled handoff template that this Skill fills.

## Generic Examples

### Example A: New `feature-cards` Section foundation

- `Work type`: `new-section-foundation`.
- Slug confirmed: `feature-cards`.
- Template: `smart-template-builder/sections/feature-cards.php`.
- Layout name: `layout_feature_cards`. Primary class: `.wso-section-feature-cards`.
- ACF group, FC layout, and clone child field created with matching `parent_layout`.
- `CSS status`: `new-needed-for-frontend`. The handoff records `styles/sections/feature-cards.css` as a path that `frontend-section-qa` will create or register locally.
- Handoff filename: `feature-cards-new-section-foundation-handoff.md`.

### Example B: Existing `intro` Section remodel

- `Work type`: `existing-section-remodel`.
- `Protected Existing Artifacts`: `layout_intro`, existing layout key, ACF field keys, `smart-template-builder/sections/intro.php`, `styles/sections/intro.css`, `.wso-section-intro` and wrapper classes.
- Approved scope: only the in-place template change required by the remodel; no new ACF group, layout, clone child, or CSS file.
- Handoff filename: `intro-existing-section-remodel-handoff.md`.

### Example C: Visual-only `intro` change

- `Work type`: `visual-only`.
- `Server write scope`: `none`.
- The Skill identifies the existing `layout_intro` first, writes a minimal handoff, and routes to `frontend-section-qa`.
- Handoff filename: `intro-visual-only-handoff.md`.

### Example D: Live or unknown environment

- `Environment`: `live` or `unknown`.
- Reads and discovery proceed; no writes until explicit, concrete confirmation per artifact.
- Cache flushes require their own explicit confirmation.
- Handoff records the confirmed scope and any deferred actions.
