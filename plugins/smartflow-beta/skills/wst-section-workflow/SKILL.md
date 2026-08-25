---
name: wst-section-workflow
description: Plan, classify, and execute WST Flexible Content Section work in the local SmartFlow workspace as a productive implementation workflow with safety stops. Use for any new Section, existing Section remodel, or Section-related preflight before frontend CSS work. Visual-only Section changes route to the bundled frontend-section-qa Skill with a minimal work record. Section artifacts are authored as tracked source (templates plus ACF Local JSON field groups) and reach the server only through the bundled deploy pass; field-definition changes go live after a human-confirmed sync in the admin.
---

# WST Section Workflow

This Skill is the single entry point for any WST Flexible Content Section work. It classifies the request, drives the implementation, protects existing Section artifacts during remodels, and maintains the Section work record in the project docs layer that the frontend QA pass consumes.

Everything happens in one workspace: the wp-content-level repository checkout. There is no server shell and no server/local phase split. Section templates, ACF Local JSON field groups (`acf-json/` in the child theme), Flexible Content wiring, and registrations are authored as tracked source; they reach the server only through the bundled deploy pass of the `deploy-and-branches` Rule (commit, hard stop, the user pushes, the deploy delivers the child theme, the status bridge verifies `deployed_commit`). Field-definition changes additionally need the human sync click in the admin per the `acf-local-json` Rule before they are live.

The Skill is a productive implementation workflow with safety stops, not a preflight write gate. Reads and discovery are always allowed. Repository writes proceed when scope is clear and safe; the workflow stops and asks only at concrete risk points.

This Skill does not own Section CSS or SCSS. It documents CSS paths, stable classes, hooks, and measurable visual expectations in the work record; CSS implementation belongs to `frontend-section-qa`. When work runs under the package orchestration, one Section run is executed through the `wst-shortcode-implementer` runner per the `agent-routing` and `wst-php-authoring-route` Rules; this Skill's semantics are identical either way.

## Skill character

- Productive implementation by default. Read `PROJECT-CONTEXT.md` and the project docs layer, apply the WST/ACF rules, search existing patterns, then implement inside the approved scope.
- Compact and recommendation-driven. Ask in compact rounds with a clear recommendation, not open-ended interviews.
- Safety stops only at concrete risks: work-type reclassification, structural ambiguity, protected-artifact changes, content overwrites, bootstrap-file edits, and the built-in commit-and-hand-over stop.
- CSS/SCSS files are never written from this Skill.

## Hard safety stops

Apply these rules before any other action.

Stop and confirm before:

- Changing the classified work type during the task. Reclassification is always a stop-and-confirm point, including when it becomes safer (for example `new-section-foundation -> visual-only`).
- Touching public selectors, layout names, layout keys, or ACF field keys that templates, scripts, styles, or stored content rely on. Key or name changes on saved fields are data migrations and need an explicit user decision (per the `acf-local-json` Rule).
- Creating new artifacts during an `existing-section-remodel` (new template file, ACF JSON group file, Flexible Content layout, clone child field, or style loader entry) unless explicitly approved.
- Making a structural ACF/FC decision that discovery cannot resolve.
- Preparing content changes that overwrite existing page content (the row plan for the admin must be explicit about replace vs append).
- Backend review of prefilled variant rows before fixtures are exported for the Section preview pages.
- Replace-or-keep decision for existing catalog page entries before migrating preview variants there.
- Test page deletion: only after the migration spot-check and explicit maintainer confirmation.
- Editing `theme-functions.php` (explicit confirmation for the exact change; `functions.php` is forbidden entirely, per the `file-edit-boundary` Rule).
- Writing any Section, template, ACF, or Flexible Content artifact inside the WST plugin folder `plugins/weseo-smart-template-builder/`. Project-owned WST artifacts always live in the child theme under `themes/<child-theme>/smart-template-builder/`. Treat the plugin folder as a hard off-limits target unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath.

Built-in hard stop of every deploy pass: after committing server-relevant work, stop and hand over per the `deploy-and-branches` Rule. The agent never pushes.

Always allowed:

- Reading project context, repository files, rendered markup over HTTP, and server state over the status bridge.
- Reading and analyzing Figma/source.
- Reading the WST shortcode catalog through the bundled `wst-shortcodes` Skill.
- Lightweight Media Library lookup over the WordPress REST API.
- Updating the Section work record with discovery findings and proposed scope.

Structural ACF database writes are forbidden without exception (`acf-local-json` Rule): field definitions are JSON files in `acf-json/`, never `acf-field`/`acf-field-group` posts. Structural changes go live only through deploy plus the human sync click.

## Workflow at a glance

1. Read `PROJECT-CONTEXT.md`, the project docs layer, and the `acf-local-json` Rule.
2. Run the Start question block in one compact message; skip any question whose answer is already in project context.
3. Inspect Figma/source, search similar Sections, identify the work type, and record `Discovery and safety status` in the work record.
4. If a structural ambiguity remains, run one Structural question block. Otherwise continue.
5. Announce a short Execution Plan before any repository write.
6. Implement the Section artifacts as tracked source: template, ACF JSON group, Flexible Content layout and clone child wiring, registration. Prove every new WST shortcode form with the four-source proof (`wst-shortcodes`).
7. Bundle everything deploy-needing into one pass: pull-before-deploy on `acf-json/`, commit, hard stop, hand over. After the user pushes, verify `deployed_commit` over the status bridge; field-definition changes then need the human sync in the admin.
8. Served verification (function and existence only), test content, and Section preview pages.
9. Complete the work record with the Frontend QA Brief and route to `frontend-section-qa`.

## Question budget

Maximum three compact rounds. Each round is a single message. After the third round the Skill either applies a documented recommendation or stops and records a blocker in the work record. It does not keep interviewing.

Questions are structural and operational. Do not ask for HTML tags, CSS classes, spacing, typography, colors, or responsive behavior when those can be derived from Figma, existing project patterns, or belong to the frontend QA pass.

### Start question block (after context check)

Ask in one message, only the values that project context did not already supply:

1. Figma or source design link.
2. Test placement: on which page should the Section be visible for verification?
3. For a new Section: confirm the proposed Section slug. The Skill may propose `<derived-slug>` from the Figma frame or brief; the maintainer confirms or replaces it.
4. Are there server-relevant variants or states? If none mentioned, the Skill derives them from Figma.

### Structural question block (only when needed)

Ask only after Figma analysis and pattern discovery if a structural choice remains ambiguous. Always include a recommendation:

```text
Recommendation: <X> because <Y>. Confirm or correct.
```

Do not ask about visual styling, classes, or design details that the frontend QA pass owns.

### Failsafe question block (last resort)

If a write would otherwise risk a wrong Section structure, ask one more compact round. Otherwise stop and document the blocker in the work record.

## Work type classification

Classify based on discovery, not on the wording of the request.

| Work type | Trigger |
| --- | --- |
| `new-section-foundation` | No suitable existing Section/layout/template is found; Figma or brief requires a new reusable Section. |
| `existing-section-remodel` | A matching existing Section exists and the change touches template markup, the ACF JSON group, Flexible Content wiring, or registration. Visual change alone is not enough. |
| `visual-only` | Template, field group, and Flexible Content already fit; only CSS, spacing, typography, colors, responsive behavior, or interaction states need to change. |
| `unclear` | The Skill cannot decide after discovery. Use the Structural question block. |

Routing:

- `new-section-foundation` -> continue with this Skill.
- `existing-section-remodel` -> continue with this Skill under the in-place protections below.
- `visual-only` -> no template/ACF/FC work; create a minimal work record and route to `frontend-section-qa`.
- `unclear` -> ask one Structural question block; if still unclear, stop and record the blocker.

### Reclassification rule

If the work type changes during discovery or implementation:

- Stop before any further write.
- Explain why the previous classification no longer fits.
- Propose the new classification and the new write scope.
- Wait for explicit confirmation.
- Update the work record. Under package orchestration, reclassification is a hard stop for the main chat, not for the runner.

## Pattern discovery and WST language safety

Before any new template or remodel, orient in the project's WST dialect.

Reading order:

1. Apply the `acf-local-json` Rule before any field-group or Flexible Content wiring write.
2. Use [`reference.md`](reference.md) for the JSON group shapes, Flexible Content wiring, template invariants, and registration patterns.
3. Use the bundled `wst-shortcodes` Skill as the catalog entry for shortcode forms; every new WST shortcode form needs the four-source proof (catalog, installed runtime, project precedent, rendered HTML).
4. Inspect at least one similar existing Section in the project to match local conventions: `conditional_logic_start/end` placeholders, `wst_include` registration, row/wrap/column classes, section ID and tabindex elements, `get_sub_field` and clone-prefix patterns, content/button/layout clone usage.

Conflict resolution priority:

1. Explicit user decision for this task.
2. Installed runtime behavior and rendered HTML evidence.
3. Project-local context, existing site conventions, and project examples that satisfy the invariants.
4. The `acf-local-json` Rule and this Skill's reference invariants.
5. The catalog snapshot (`wst-shortcodes`).
6. Generic Skill examples.

If a local example contradicts a hard invariant, do not copy it blindly. Record the conflict in the work record as a risk and propose a corrected approach. Ask before correcting unrelated existing Sections; corrections outside the current scope are not implicit approvals.

### Searching for similar Sections

The Skill searches for a structural reference by itself before asking. Sources include Section template files under `themes/<child-theme>/smart-template-builder/sections/`, the ACF JSON groups under `themes/<child-theme>/acf-json/`, Flexible Content layouts, and rendered markup on existing pages. Do not search inside `plugins/weseo-smart-template-builder/` for project-owned references; it is the WST runtime/library and contains framework code, not the project's Sections.

Only ask when no usable reference is found, when multiple references would change the structural model differently, or when the maintainer might prefer a specific reference Section.

## Existing Section remodel: in-place default

`existing-section-remodel` defaults to in-place. By default:

- Reuse the existing template path.
- Preserve `layout` name, layout key, and `parent_layout`.
- Preserve existing field keys and clone child field keys (stored content references them).
- Preserve public selectors that templates, scripts, or styles rely on.

Do not create a new template file, ACF JSON group, Flexible Content layout, clone child field, or style loader entry during a remodel unless the confirmed work record explicitly approves that new artifact.

If a desired change only affects spacing, typography, color, responsive behavior, or hover/focus, apply the reclassification rule to switch to `visual-only` and route to `frontend-section-qa`.

Record protected artifacts in the work record under `Protected existing artifacts`.

## WST paths in the repository

The repository root is the wp-content level. Resolve before any write:

```text
themes/<child-theme>/smart-template-builder/sections/<section-slug>.php   (templates)
themes/<child-theme>/acf-json/                                            (ACF JSON field groups)
```

Read `PROJECT-CONTEXT.md` for the child theme name and any project deviations. The WST plugin folder `plugins/weseo-smart-template-builder/` is hard off-limits for project-owned artifacts (see hard safety stops). Verify paths with a quick directory listing before writing.

## Slug, derived names, and work record

For a new Section, the Skill proposes a slug derived from the Figma frame or brief and asks the maintainer to confirm.

Once the slug is confirmed, derive the rest deterministically:

| Derived | Pattern |
| --- | --- |
| Layout name | `layout_<section_slug_with_underscores>` |
| Primary class | `.wso-section-<section-slug>` |
| Template file | `themes/<child-theme>/smart-template-builder/sections/<section-slug>.php` |
| ACF JSON group file | `themes/<child-theme>/acf-json/<per the installation's filename convention from PROJECT-CONTEXT.md>` |
| Work record | `docs/sections/<section-slug>.md` (or the project docs convention from `PROJECT-CONTEXT.md`) |

For `existing-section-remodel`, do not re-confirm the slug if a single existing Section is unambiguously matched. Ask only if multiple candidates apply or the maintainer wants a rename.

## ACF JSON group and Flexible Content wiring

All ACF work is Local JSON authoring per the `acf-local-json` Rule; see [`reference.md`](reference.md) for the concrete shapes.

- The Section field group is one JSON file under `acf-json/` with a stable fresh `group_<unique>` key and stable `field_<unique>` keys for every field, `acfe_autosync` containing `"json"`, and a `modified` timestamp above the database state.
- The Flexible Content layout entry and the seamless clone child field are added by editing the JSON file of the Page-Builder Flexible Content container (with its own `modified` bump; if the container has no JSON source yet — the bridge reports it `local: false` — stop: the `setup-acf-local-json` Skill must run first).
- Generate the layout key once and record it immediately in the work record. The clone child field references it exactly through `parent_layout`.
- Standard clone settings: `type=clone`, `clone=[<section-field-group-key>]`, `display=seamless`, `prefix_name=1`, `prefix_label=0`, `parent_layout=<layout-key>`, `acfe_save_meta=1` when the project uses ACF Extended save-meta behavior.

## Registration

Add the Section include inside the project's `[wst_acf_flexible_content]` block in `flexible-content.php`:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

Keep the registration order consistent with the project's existing editor grouping.

### Template invariants

- Guard direct access with `if (! defined('ABSPATH')) exit;`.
- Preserve `{{conditional_logic_start}}` and `{{conditional_logic_end}}` when the project uses those WST placeholders.
- Use the primary section class `.wso-section-<section-slug>`.
- Include the project layout, section ID, and tabindex WST elements when present in nearby templates.
- Keep custom markup inside WST row, wrap, column, and column attribute classes that match the project pattern.
- Compose existing WST shortcodes; do not replace WST rendering with freely authored PHP/HTML (`wst-php-authoring-route` Rule). Conditionals follow the `wst-conditional-nesting` Rule.
- For filled WYSIWYG title/subline fields, use `[wst_acf field='...' format_value='0']` to avoid `wpautop` wrapping `<p>` inside headings. `[wst_acf_wysiwyg]` returns empty in FC loop context, so do not use it for FC sub-fields. Treat installation-wide autop cleanup as its own work package.

## Execution Plan before writes

Before writing repository files, output a short Execution Plan:

```text
Plan:
- Work type: <classification>
- Repository writes: <template / ACF JSON group / FC wiring / registration / fixtures>
- Content plan: <admin row plan for the user / REST route / not needed>
- Media: <reuse existing IDs / REST media import / hand to user / not needed>
- CSS: not written by this Skill (routed to frontend-section-qa)
- Work record: <docs path>
- Deploy: one bundled pass, then commit + hand-over stop
```

## New Section foundation steps

Run only when `Work type` is `new-section-foundation`, the slug is confirmed, and the work record captures the discovery sources and scope.

```text
New WST FC Section:
- [ ] Discovery sources recorded (Figma, similar Sections, rules applied, test placement)
- [ ] Section slug confirmed and derived names recorded
- [ ] Execution Plan announced
- [ ] Create Section template at themes/<child-theme>/smart-template-builder/sections/<section-slug>.php (never under plugins/weseo-smart-template-builder/)
- [ ] Every new WST shortcode form four-source-proven (wst-shortcodes)
- [ ] Create the PHP Section field group (fresh stable keys)
- [ ] Add the Flexible Content layout entry and clone child field in the PHP FC container (parent_layout matches the layout key exactly)
- [ ] Register the Section in flexible-content.php
- [ ] Document CSS hooks and CSS path in the work record (no CSS file from this Skill)
- [ ] Deploy pass: commit with trailer, HARD STOP, user pushes, bridge-verify deployed_commit
- [ ] Flush caches through the bridge when templates or field definitions changed
- [ ] Served verification (function and existence only)
- [ ] Test content: hand the exact row plan to the user for the admin (or a project-documented route)
- [ ] Offer Section preview pages if the project has none (run section-preview-harness on yes; record `declined` on no); record preview URLs or `n/a`
- [ ] Fill the Visual QA Targets matrix (viewport mapping, all base variants answered or n/a, mobile rows sourced from Design mobile)
- [ ] Complete the work record with the Frontend QA Brief and route to frontend-section-qa
```

Do not invent field keys, group keys, layout keys, project paths, URLs, selectors, or theme values. Missing project values are asked for or recorded as explicit unresolved placeholders.

## Test placement and content

The Skill has no direct content-write path. If a test placement / target page is not known from project context, ask once in the Start question block. For test content:

- Prepare an exact row plan (page, position, one Flexible Content row per variant, field values with expanded clone names, media attachment IDs) and hand it to the user for the admin.
- Use a programmatic route (for example the WordPress REST API) only when `PROJECT-CONTEXT.md` documents it as approved for content writes.
- Content that would overwrite existing rows needs its own explicit confirmation.

Foundation work may proceed without a test placement; dependent content steps are recorded as open in the work record.

## Section preview pages (offer, then use)

Section preview pages render one Section in isolation under a stable URL (`/section-preview/<section>/<variant>`) from Git-tracked JSON fixtures — the mechanism of the bundled `section-preview-harness` Skill. This is optional project-local infrastructure; never assume it exists.

For every built Section (`new-section-foundation` and `existing-section-remodel`), resolve the preview-pages state from `PROJECT-CONTEXT.md`:

1. Look for a preview-pages block (stable key `section-preview-pages`).
2. If it is present and active, use the preview route for variant work and write the resulting preview URLs into the work record.
3. If it is absent and there is no `section-preview-pages: declined` marker, actively offer to set it up (plain words, benefit stated: each variant gets its own preview URL, so QA checks one Section in isolation with fewer tokens). On `yes`: run `section-preview-harness` as a sub-flow, then record the preview URLs. On `no`: record `section-preview-pages: declined` in `PROJECT-CONTEXT.md` and set `Preview URLs: n/a (declined)`. Do not ask again for this project.
4. If the preview-pages block is missing values needed to build URLs, keep `Preview URLs: <unresolved: ...>` rather than inventing them.

The offer is a recommendation, not a hard stop. A `no` never blocks the Section work.

### Preview-pages-backed variant workflow (recommended for variants)

1. After the deploy pass is bridge-verified, have the variant rows entered on an unlinked test page (exact row plan for the admin; real design content, media attachment IDs recorded).
2. HARD STOP: the maintainer reviews the rows in the backend before fixtures are exported (content, images, variant assignment, line breaks).
3. Export fixtures over the harness export route (`GET /wp-json/wso-preview/v1/export/<section>`); write the returned fixtures into the repository. Never write fixture JSON by hand. Fixtures ship with the next deploy pass.
4. Run structural preview QA per variant (HTTP 200, `data-preview-variant`, variant root class, expected content/image, body classes incl. brand palettes). Record results in the work record.
5. The Frontend QA Brief lists the preview URLs as first browser targets. Full-page QA on the test page stays mandatory (previews are nocache and hide cache/Delay-JS bug classes).

Brand or palette variants that depend on page context (for example `body.brand-<slug>` from a company taxonomy) are verified through the fixture `body_class`. On the test page all rows render in the page's own palette, and that is expected.

## CSS boundary

This Skill never writes or edits CSS or SCSS.

Allowed:

- Detect CSS needs from Figma and existing patterns.
- Document CSS paths, primary class, wrapper classes, custom properties, and selectors to preserve in the work record.
- Set stable, predictable hook classes in the template markup.
- Record `CSS status` (`existing`, `new-needed-for-frontend`, `unknown`, `not-applicable`).

Not allowed: creating or editing Section CSS/SCSS files, editing generated CSS, final responsive QA, pixel-level visual checks, or hover/focus styling. Those belong to `frontend-section-qa` (and under package orchestration to the `cpt-visual-implementer` runner), which works in the same repository on tracked CSS source: injection-proof iteration first, served pass only after a bridge-verified deploy.

When a new CSS file or style loader entry is needed, record it under `CSS status = new-needed-for-frontend` so the frontend pass creates or registers it in tracked source.

## Media handling

After Figma or source analysis, perform a lightweight Media Library check over the WordPress REST API whether the required assets already exist. Keep this token-efficient.

- If matches are quickly identifiable, use the existing attachment IDs and record them in the work record.
- If assets are missing and the Section needs them to be testable, import them over the WordPress REST media route with the `.env` application password (safe filenames, alt text, verification), or hand the asset list to the user for admin upload. Record the imported attachment IDs.
- Ask before importing when asset rights are unclear or many candidate assets exist.

## Deploy pass and served verification

Everything deploy-needing from this run goes into one pass (`deploy-and-branches` Rule): template, ACF JSON group, FC wiring, registration, fixtures, work record. Then:

1. Pull-before-deploy: pull the complete `acf-json/` listing from the server over read-only FTP into the repository so the deploy cannot overwrite newer server JSONs (`acf-local-json` Rule).
2. Commit with the `Made with: SmartFlow` trailer. HARD STOP: hand over with the commit hash and what the deploy will deliver. The agent never pushes.
3. After the user reports pushing, verify `deployed_commit` over the status bridge with the bounded retry budget (`status-bridge` Rule). No served result counts while the hashes differ.
4. Field-definition sync: when the pass changed ACF JSON, hand over for the human sync — the admin shows "Sync available", a colleague reviews the diff and clicks Sync. Structural changes are not live before that click.
5. Flush caches through the bridge (`POST /flush-cache`) when templates or field definitions changed.
6. Served verification (function and existence only):
   - Target or preview page loads without PHP fatal errors or new warnings.
   - Section markup and the primary class `.wso-section-<section-slug>` are present in the rendered page.
   - `GET /status` lists the Section field group with `local: "json"`.
   - The layout is selectable in the editor (user confirms, or evident from saved test content).

Pixel-perfect rendering, responsive layout, spacing, typography, colors, and interaction states belong to `frontend-section-qa` and are not part of this Skill's verification. Record the results in the work record under `QA notes`.

## Finalization: migrating preview variants to the canonical content page

When QA has passed and the project keeps a customer-facing catalog page (for example "All Sections") plus a temporary test page that fed the preview fixtures, finish the run by migrating the variant rows there:

1. Decision stop: replace or keep existing legacy entries of the Section on the catalog page? Delete the test page afterwards? Ask the maintainer.
2. The migration itself is a content write: prepare the exact row plan for the admin, or use a project-documented programmatic route. Append-only unless the maintainer chose replace. See the harness `REFERENCE.md` for the update-field mechanics and the stale-meta caveat when a programmatic route exists.
3. Verify: rendered section inventory before/after (counts per section type), variant order and contents, browser pre-interaction check on the cached page.
4. Re-point the fixture exporter config to the catalog page rows (config change is a commit + deploy) and re-export: byte-identical fixture `data` blocks prove migration fidelity.
5. Delete the test page only after maintainer confirmation; the catalog page is the canonical content source from then on. Update `PROJECT-CONTEXT.md` and the work record.

## Visual-only routing

If `Work type` is `visual-only`:

1. Identify the existing Section first using project discovery (template path, layout key/name, primary class) before asking.
2. Do not touch templates, field groups, or FC wiring.
3. Create a minimal work record with the existing Section identity, Figma/source link, target URL, stable classes/hooks, CSS status, and a clear `No template/ACF changes required` note.
4. Route to `frontend-section-qa`.

If discovery cannot identify the existing Section unambiguously, ask one compact Structural question with candidate Sections listed.

## The Section work record

The work record is the durable contract of a Section run. It lives in the project docs layer (default `docs/sections/<section-slug>.md`), is tracked in the repository, and never reaches the server (`docs/` is outside the deploy path).

Maintain it progressively during discovery, decisions, writes, and verification. It carries:

- `Work type`, write scope, `Discovery and safety status` (`context-checking`, `ready-for-safe-writes`, `write-approved`, `blocked`), and `Frontend route`.
- Section identity: slug, label, layout name and key, group and field keys, template path, registration entry.
- Discovery sources: Figma links (desktop and mobile frame, unchanged so the frontend pass can re-read them), reference Sections, catalog sections consulted, four-source proofs for new shortcode forms.
- `Protected existing artifacts` for remodels.
- CSS hooks: primary class, wrapper classes, custom properties, selectors to preserve, CSS path, `CSS status`.
- `Preview URLs` (real URLs, `n/a (no preview pages)`, `n/a (declined)`, or `<unresolved: ...>`).
- Visual QA Targets matrix (below), QA notes, deploy state (commit hash, bridge verification result), open questions, and blockers.

Do not put secrets, tokens, application passwords, token-bearing URLs, or dumps into the work record.

After the frontend pass completes, the work record stays the permanent Section documentation in the docs layer; there is no separate handoff file to clean up.

### Visual QA Targets

Before routing, fill the `Visual QA Targets` matrix. One row = one yes/no-checkable expectation across variant and viewport, with a per-row `Result` column the frontend pass fills:

- Map viewport roles (desktop/tablet/mobile) to the pixel widths from `PROJECT-CONTEXT.md`; keep `<unresolved: ...>` instead of inventing widths.
- Answer every mandatory base variant (default, long headline/copy, optional field empty, many repeats, mobile stack, interaction states) with at least one expectation row or an explicit `n/a: <reason>`.
- Each expectation is yes/no-checkable, names theme tokens where they define a value, and uses the stable selectors from CSS hooks.
- Source mobile rows from the `Design mobile` frame. When no mobile design exists, record `no-mobile-design: derived-from-desktop` so the frontend pass knows where interpretation latitude exists.

### Frontend QA Brief

When the Section work (or visual-only routing) is complete, write a compact `Frontend QA Brief` into the work record so `frontend-section-qa` can start without re-asking structural questions:

```text
## Frontend QA Brief

- Target URL: <dev-or-staging-url>
- Preview URLs: <one per variant as first browser targets> or n/a
- Section selector: .wso-section-<section-slug>
- Figma/source links: <design-desktop-and-design-mobile>
- CSS status: existing / new-needed-for-frontend / unknown / not-applicable
- Required viewports and expected behavior: see the Visual QA Targets matrix
- Stable hooks to preserve: <selectors>
- Behavior already solved in the template (for example suppressed wraps, raw titles): <list> so the frontend pass does not rebuild it in CSS.
- Structural contract: do not change templates, field groups, or FC wiring from the CSS pass. Findings that need template or field changes come back to this workflow through the work record (documented evidence, expected markup, affected URLs). That is the normal path, not an exception.
- Proof modes: injection-proof for iteration; a served pass requires the bridge-verified deployed_commit match.
```

The Frontend QA Brief is a verifiable starting point, not a blind directive. The frontend pass re-reads the Figma link and the rendered page and may report contradictions back into the work record instead of silently working around them.

## Package boundary

When editing this Skill or related plugin files, follow the `plugin-package-boundary` Rule. The `section-preview-harness` and `wst-shortcodes` Skills are bundled inside this same plugin at [`../section-preview-harness/SKILL.md`](../section-preview-harness/SKILL.md) and [`../wst-shortcodes/SKILL.md`](../wst-shortcodes/SKILL.md).

## Generic examples

### Example A: New `feature-cards` Section foundation

- `Work type`: `new-section-foundation`. Slug confirmed: `feature-cards`.
- Template: `themes/<child-theme>/smart-template-builder/sections/feature-cards.php`; ACF JSON group file in `acf-json/` per the project filename convention; layout `layout_feature_cards`; primary class `.wso-section-feature-cards`.
- One deploy pass carries template, ACF JSON group, FC wiring, and registration; bridge-verified and admin-synced before served checks.
- `CSS status`: `new-needed-for-frontend`; the work record names `styles/sections/feature-cards.css` as frontend work.
- Work record: `docs/sections/feature-cards.md`.

### Example B: Existing `intro` Section remodel

- `Work type`: `existing-section-remodel`.
- `Protected existing artifacts`: `layout_intro`, existing layout key, field keys, `sections/intro.php`, `styles/sections/intro.css`, `.wso-section-intro` and wrapper classes.
- Approved scope: only the in-place template change; no new field group, layout, clone child, or CSS file.

### Example C: Visual-only `intro` change

- `Work type`: `visual-only`. No template/ACF/FC writes.
- The Skill identifies the existing `layout_intro` first, writes a minimal work record, and routes to `frontend-section-qa`.
