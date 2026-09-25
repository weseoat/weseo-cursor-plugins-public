---
name: wst-section-workflow
description: Plan, classify, and execute WST Flexible Content Section work in the local SmartFlow workspace as a productive implementation workflow with safety stops. Use for any new Section, existing Section remodel, or Section-related preflight before frontend CSS work. Visual-only Section changes route to the bundled frontend-section-qa Skill with a minimal work record (executed by the cpt-visual-implementer runner, never by the main chat). The slug is confirmed only after classification in the Foundation confirmation block; an existing same-purpose Section makes the work type unclear until the remodel-vs-new decision is taken; an active Section preview harness makes the fixture export per Section mandatory. Section artifacts are authored as tracked source (templates plus ACF Local JSON field groups) and reach the server only through the bundled deploy pass; field-definition changes go live after a human-confirmed sync in the admin.
---

# WST Section Workflow

This Skill is the single entry point for any WST Flexible Content Section work. It classifies the request, drives the implementation, protects existing Section artifacts during remodels, and maintains the Section work record in the project docs layer that the frontend QA pass consumes.

Everything happens in one workspace: the wp-content-level repository checkout. There is no server shell and no server/local phase split. Section templates, ACF Local JSON field groups (`acf-json/` in the child theme), Flexible Content wiring, and registrations are authored as tracked source; they reach the server only through the bundled deploy pass of the `deploy-and-branches` Rule (commit, hard stop, the user pushes, the deploy delivers the child theme, the status bridge verifies `deployed_commit`). Field-definition changes additionally need the human sync click in the admin per the `acf-local-json` Rule before they are live.

The Skill is a productive implementation workflow with safety stops, not a preflight write gate. Reads and discovery are always allowed. Repository writes proceed when scope is clear and safe; the workflow stops and asks only at concrete risk points.

This Skill does not own Section CSS or SCSS. It documents CSS paths, stable classes, hooks, and measurable visual expectations in the work record; CSS implementation belongs to `frontend-section-qa`. On every start — direct start included, not only under package orchestration — the main chat acts as the orchestrator: classification, the pattern-discovery decision, work-record creation, and all hard stops stay in the main chat, while the execution run (template and ACF JSON writes) is executed through the `wst-shortcode-implementer` runner per the `agent-routing` and `wst-php-authoring-route` Rules. Hard stops that surface inside the run come back to the main chat as `OPEN DECISION` in the runner's return format; this Skill's semantics are identical either way. The runner prompt carries the confirmed work type as a field (for a foundation: confirmed through the Foundation confirmation block), the preview-harness state, and the distilled discovery — a runner that receives a foundation task without the confirmation returns `OPEN DECISION: foundation not confirmed`.

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
- Choosing between a **new Section/FC layout** and a **variant on an existing same-purpose Section** (brief "alternatives Intro" while `layout_intro` exists). That is a structural ACF/FC decision: hard stop with a recommendation, never decided implicitly by proposing a slug. The Foundation confirmation block below is the only place where a foundation gets approved.
- Preparing content changes that overwrite existing page content (the row plan for the admin must be explicit about replace vs append).
- Backend review of prefilled variant rows before fixtures are exported for the Section preview pages. When the harness is active and this Section has no fixtures yet, the review stop is mandatory — not only inside a voluntarily entered preview path (see "Section preview pages").
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

1. Read `PROJECT-CONTEXT.md`, the project docs layer, and the `acf-local-json` Rule. When `PROJECT-CONTEXT.md` records a Confluence anchor, re-read the anchored PL page **fresh at run start** over the Atlassian MCP and pull only the section relevant to this Section — the matching task row, module notes, and the Section's Figma link — into the work record (`confluence-source` Rule). No anchor, or no usable Atlassian MCP: skip cleanly, record `confluence-source: no anchor` (or `MCP unavailable`) in the work record, and continue from the mirror. Never re-read mid-run; runners receive the distilled extract in their prompt and never call Confluence.
2. Run the Start question block in one compact message — classification-neutral questions only (design link, test placement, variants); skip any question whose answer is already in project context or the run-start Confluence extract. The slug is **not** asked here.
3. Inspect Figma/source, search similar Sections (the brief's wording is discovery input: "alternatives Intro" means search for `intro` first), identify the work type, and record `Discovery and safety status` in the work record.
4. If a structural ambiguity remains — including an existing Section with the same purpose — run one Structural question block. For `new-section-foundation`, run the Foundation confirmation block (this is where the slug is proposed and the foundation is approved as such). Otherwise continue.
5. Announce a short Execution Plan before any repository write.
6. Implement the Section artifacts as tracked source: template, ACF JSON group, Flexible Content layout and clone child wiring, registration. Prove every new WST shortcode form with the four-source proof (`wst-shortcodes`), per nesting context.
7. Bundle everything deploy-needing into one pass: pull-before-deploy on `acf-json/` (pre-commit hook or manual pull, incl. the `modified` guard), commit, hard stop, hand over. After the user pushes, verify `deployed_commit` over the status bridge; field-definition changes then need the human sync in the admin (hand-over text from the `acf-local-json` Rule).
8. Served verification (function and existence only), test content, and Section preview pages — with an active harness, the fixture configuration and export for this Section are mandatory.
9. Complete the work record with the Frontend QA Brief and route to `frontend-section-qa`.

## Question budget

Maximum three compact rounds. Each round is a single message. After the third round the Skill either applies a documented recommendation or stops and records a blocker in the work record. It does not keep interviewing.

Questions are structural and operational. Do not ask for HTML tags, CSS classes, spacing, typography, colors, or responsive behavior when those can be derived from Figma, existing project patterns, or belong to the frontend QA pass.

### Start question block (after context check)

Ask in one message, only the values that project context did not already supply. These questions are classification-neutral — nothing in this block presumes a new Section:

1. Figma or source design link.
2. Test placement: on which page should the Section be visible for verification?
3. Are there server-relevant variants or states? If none mentioned, the Skill derives them from Figma.

The Section slug is deliberately not part of this block: proposing a slug before discovery is a silent foundation classification, and a "passt" on it would approve a decision that was never stated. The slug is confirmed in the Foundation confirmation block after the work type is known.

### Structural question block (only when needed)

Ask only after Figma analysis and pattern discovery if a structural choice remains ambiguous. Always include a recommendation:

```text
Recommendation: <X> because <Y>. Confirm or correct.
```

When discovery found an existing Section with the same purpose, the block reads:

```text
Existing same-purpose Section found: <section> (layout <layout>, group <group>).
Option 1: remodel <section> — new layout value / variant on the existing group and template.
Option 2: new Section <derived-slug> — new template, new ACF group, new FC layout.
Recommendation: remodel, because <reason>. Confirm or correct.
```

Do not ask about visual styling, classes, or design details that the frontend QA pass owns.

### Foundation confirmation block (only for `new-section-foundation`)

Runs after classification, never before. It names the decision for what it is:

```text
Work type new-section-foundation — new template, new ACF group, new FC layout.
Proposed slug: <derived-slug> (from the Figma frame / brief).
Confirm, replace the slug, or redirect to a remodel of <candidate or none>.
```

A yes here is recognizably a foundation approval. The confirmed work type goes into the runner prompt as a field (`Work type: new-section-foundation (confirmed <date>)`); without it the `wst-shortcode-implementer` returns `OPEN DECISION: foundation not confirmed` instead of writing.

### Failsafe question block (last resort)

If a write would otherwise risk a wrong Section structure, ask one more compact round. Otherwise stop and document the blocker in the work record.

## Work type classification

Classify based on discovery, not on the wording of the request — but treat the wording as **discovery input**, never discard it: a brief that names an existing Section type ("alternatives Intro", "zweiter Teaser") tells you what to search for, not what to do.

| Work type | Trigger |
| --- | --- |
| `new-section-foundation` | No suitable existing Section/layout/template is found; Figma or brief requires a new reusable Section. Confirmed only through the Foundation confirmation block. |
| `existing-section-remodel` | A matching existing Section exists and the change touches template markup, the ACF JSON group, Flexible Content wiring, or registration. Visual change alone is not enough. |
| `visual-only` | Template, field group, and Flexible Content already fit; only CSS, spacing, typography, colors, responsive behavior, or interaction states need to change. |
| `unclear` | The Skill cannot decide after discovery — **by default also whenever discovery finds an existing Section with the same purpose** (same Section type in the brief, same role on the page, same base group) while the design differs. Use the Structural question block with the remodel-vs-new recommendation. |

Routing:

- `new-section-foundation` -> Foundation confirmation block, then continue with this Skill.
- `existing-section-remodel` -> continue with this Skill under the in-place protections below.
- `visual-only` -> no template/ACF/FC work; create a minimal work record and spawn the `cpt-visual-implementer` with `frontend-section-qa` (see "Visual-only routing").
- `unclear` -> ask one Structural question block; if still unclear, stop and record the blocker.

### Reclassification rule

If the work type changes during discovery or implementation:

- Stop before any further write.
- Explain why the previous classification no longer fits.
- Propose the new classification and the new write scope.
- Wait for explicit confirmation.
- Update the work record. Because the execution run is delegated on every start, reclassification is a hard stop for the main chat, not for the runner; the runner surfaces it as `OPEN DECISION`.

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

Only ask when no usable reference is found, when multiple references would change the structural model differently, when the maintainer might prefer a specific reference Section — or when **exactly one strong reference with the same purpose** exists. That last case is the most common one: a brief "alternatives Intro" with `layout_intro` in the repository, a second teaser next to an existing teaser Section, a design whose role on the page an existing Section already fills. It is never resolved by proposing a new slug. Default the work type to `unclear` and run the Structural question block with the recommendation "remodel `<section>` (variant / layout value on the existing group) vs. new Section" — the remodel is usually the right answer because stored content, selectors, and CSS already exist.

This discovery deliberately stays in the main chat: one reference Section and one design frame do not justify a discovery leaf, and the pattern-discovery decision is a main-chat responsibility (routing paragraph above). Delegate the Figma read to the `cpt-figma-analyst` only when a full raw design spec is needed — a pixel-parity target or a genuinely complex new component, per its agent file. The `cpt-codebase-analyst` is CPT-package-scoped and not part of Section runs (`agent-routing` Rule).

## Existing Section remodel: in-place default

`existing-section-remodel` defaults to in-place. By default:

- Reuse the existing template path.
- Preserve `layout` name, layout key, and `parent_layout`.
- Preserve existing field keys and clone child field keys (stored content references them).
- Preserve public selectors that templates, scripts, or styles rely on.

Do not create a new template file, ACF JSON group, Flexible Content layout, clone child field, or style loader entry during a remodel unless the confirmed work record explicitly approves that new artifact.

If a desired change only affects spacing, typography, color, responsive behavior, or hover/focus, apply the reclassification rule to switch to `visual-only` and route to `frontend-section-qa`.

Record protected artifacts in the work record under `Protected existing artifacts`.

### Existing Section remodel steps

Run when `Work type` is `existing-section-remodel` and the work record names the matched Section and its protected artifacts. Same discipline as the foundation checklist, without new artifacts:

```text
Existing WST FC Section remodel:
- [ ] Discovery sources recorded (Figma, matched Section, rules applied, test placement)
- [ ] Protected existing artifacts recorded (layout name/key, field keys, template path, public selectors)
- [ ] Execution Plan announced (in-place scope; any new artifact explicitly approved)
- [ ] Template change in place; every new WST shortcode form four-source-proven per nesting context (wst-shortcodes)
- [ ] ACF JSON change in place: existing keys preserved, new fields with fresh keys, modified = real current UTC epoch (acf-local-json Rule); variant switches on clones follow the tab pattern (no conditional_logic on seamless clones)
- [ ] Deploy pass: pull-before-deploy, commit with trailer, HARD STOP, user pushes, bridge-verify deployed_commit; hand over the admin sync text
- [ ] Flush caches through the bridge
- [ ] Served verification (function and existence only)
- [ ] Test content: exact row plan for the new variant rows
- [ ] Preview pages: harness active and this Section unconfigured → configure the export, HARD STOP backend review, export fixtures, record preview URLs (do not re-offer the setup); harness absent → offer once; declined → n/a (declined)
- [ ] Visual QA Targets matrix updated for the new variants
- [ ] Complete the work record with the Frontend QA Brief and route to frontend-section-qa
```

## WST paths in the repository

The repository root is the wp-content level. Resolve before any write:

```text
themes/<child-theme>/smart-template-builder/sections/<section-slug>.php   (templates)
themes/<child-theme>/acf-json/                                            (ACF JSON field groups)
```

Read `PROJECT-CONTEXT.md` for the child theme name and any project deviations. The WST plugin folder `plugins/weseo-smart-template-builder/` is hard off-limits for project-owned artifacts (see hard safety stops). Verify paths with a quick directory listing before writing.

## Slug, derived names, and work record

For a new Section, the Skill proposes a slug derived from the Figma frame or brief in the Foundation confirmation block — after classification, never in the Start question block — and asks the maintainer to confirm.

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

- The Section field group is one JSON file under `acf-json/` with a stable fresh `group_<unique>` key and stable `field_<unique>` keys for every field, the ACFE autosync opt-in containing `"json"` (nested `acfe.autosync` by default, or the shape `PROJECT-CONTEXT.md` records), and `modified` = the **real current UTC epoch from a command** (`acf-local-json` Rule 3 — never estimated, never `Get-Date -UFormat %s`, never in the future; the example values in `reference.md` are placeholders to regenerate at write time).
- The Flexible Content layout entry and the seamless clone child field are added by editing the JSON file of the Page-Builder Flexible Content container (with its own fresh `modified`; if the container has no JSON source yet — the bridge reports it `local: false` — stop: the `setup-acf-local-json` Skill must run first).
- Generate the layout key once and record it immediately in the work record. The clone child field references it exactly through `parent_layout`.
- Standard clone settings: `type=clone`, `clone=[<section-field-group-key>]`, `display=seamless`, `prefix_name=1`, `prefix_label=0`, `parent_layout=<layout-key>`, `acfe_save_meta=1` when the project uses ACF Extended save-meta behavior.
- **Variant switches on `[TMPL]` clones inside the Section group** (a layout select that should hide the content, button, or image clone for some values) follow the clone/tab pattern of the `acf-local-json` Rule: never `conditional_logic` on a seamless clone (dead rule, no wrapper); a local `tab` field carries the condition in exclusion form (`!=` rules in one group); the clone targets the source's field keys without its tab, not the group key. JSON shape in `reference.md`. The effect is editor-only and therefore reported as `implementation pass, backend check by colleague pending`, with the concrete click path in the hand-over.

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

Run only when `Work type` is `new-section-foundation`, the Foundation confirmation block was answered (slug confirmed), and the work record captures the discovery sources and scope.

```text
New WST FC Section:
- [ ] Discovery sources recorded (Figma, similar Sections incl. same-purpose check, rules applied, test placement)
- [ ] Foundation confirmed (Foundation confirmation block), Section slug confirmed and derived names recorded
- [ ] Execution Plan announced
- [ ] Create Section template at themes/<child-theme>/smart-template-builder/sections/<section-slug>.php (never under plugins/weseo-smart-template-builder/)
- [ ] Every new WST shortcode form four-source-proven per nesting context (wst-shortcodes); "proven except runtime" is named in the deploy hand-over
- [ ] Create the Section ACF JSON group file under acf-json/ (fresh stable keys, autosync opt-in includes "json" — nested acfe.autosync by default, modified = real current UTC epoch, no conditional_logic on seamless clones — acf-local-json Rule)
- [ ] Add the Flexible Content layout entry and clone child field in the FC container's JSON file (own fresh modified; parent_layout matches the layout key exactly)
- [ ] Register the Section in flexible-content.php
- [ ] Document CSS hooks and CSS path in the work record (no CSS file from this Skill)
- [ ] Deploy pass: pull-before-deploy (hook or manual, modified guard), commit with trailer, HARD STOP, user pushes, bridge-verify deployed_commit; hand over the admin sync text
- [ ] Flush caches through the bridge when templates or field definitions changed
- [ ] Served verification (function and existence only)
- [ ] Test content: hand the exact row plan to the user for the admin (or a project-documented route)
- [ ] Preview pages: harness active → configure the export for this Section, HARD STOP backend review, export fixtures, record preview URLs; harness absent → offer once (run section-preview-harness on yes; record `declined` on no); declined → n/a (declined)
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

For every built Section (`new-section-foundation` and `existing-section-remodel`), resolve the preview-pages state. There are exactly **three states**, and the harness is detected over **three sources** — never over the context key alone:

| State | Detected when | Action |
| --- | --- | --- |
| **active** | any of: `section-preview-pages: active` in `PROJECT-CONTEXT.md`; the file `section-preview-harness.php` exists in the child theme; `_export-fixtures.php` contains at least one real Section config (`source_page != 0`) | **use it** — run the mandatory block below for this Section. Files present but key missing → write the key now and report it (the first harness run of the project left prose instead of the key). |
| **declined** | `section-preview-pages: declined` in `PROJECT-CONTEXT.md` | skip; `Preview URLs: n/a (declined)`. Do not ask again. |
| **absent** | none of the above | **offer** the setup once (plain words, benefit stated: each variant gets its own preview URL, so QA checks one Section in isolation with fewer tokens). On `yes`: the **main chat** runs the bundled `section-preview-harness` Skill itself — the harness bootstrap is deliberately not routed to runners (`agent-routing` Rule) because it needs the `theme-functions.php` confirmation stop. On `no`: record `declined`, set `Preview URLs: n/a (declined)`. |

There is no fourth state "files exist, so neither offer nor use". "Do not re-install the harness" never means "do not use it". The setup **offer** is a recommendation, not a hard stop — a `no` never blocks the Section work. The **use** of an existing harness is not optional.

### Preview fixtures for this Section (mandatory when the harness is active)

Runs for every built Section while the state is **active**, foundation and remodel alike. A Section stub in `_export-fixtures.php` with `source_page: 0` and empty `rows` is exactly this case: configure it, do not treat it as "already handled".

1. **Export config:** if `_export-fixtures.php` has no real config for this Section, write it in the **main chat** (it is a tracked theme file without a bootstrap stop; only the harness bootstrap needs the `theme-functions.php` confirmation) — `source_page`, the row indices per variant, `variant_field`. Follow the "Configure a further Section" sub-workflow of `section-preview-harness`.
2. After the deploy pass is bridge-verified, have the variant rows entered on an unlinked test page (exact row plan for the admin; real design content, media attachment IDs recorded).
3. **HARD STOP:** the maintainer reviews the rows in the backend before fixtures are exported (content, images, variant assignment, line breaks). This stop lives here, not only inside an optional path.
4. Export fixtures over the harness export route (`GET /wp-json/wso-preview/v1/export/<section>`); write the returned fixtures into the repository. Never write fixture JSON by hand. Fixtures ship with the next deploy pass.
5. Run structural preview QA per variant (HTTP 200, `data-preview-variant`, variant root class, expected content/image, body classes incl. brand palettes). Record results in the work record.
6. Write the preview URLs into the work record (never invent them — a missing value stays `<unresolved: …>` and is resolved in this block). The Frontend QA Brief lists them as first browser targets. Full-page QA on the test page stays mandatory (previews are nocache and hide cache/Delay-JS bug classes).

`frontend-section-qa` starts only when `Preview URLs` holds real `/section-preview/…` addresses **or** `n/a (declined)` / `n/a (no preview pages)`. `Preview URLs: <unresolved: …>` while the harness is active and not declined is a **hard stop with a route back to this block**, never a silent switch of the CSS pass to the test page. When the fixture point surfaces inside a `wst-shortcode-implementer` run, the runner returns `OPEN DECISION: preview-fixtures` (it never edits harness files); the main chat runs config, review stop, and export. No orchestrator prompt may contain "do not touch the harness" unless the state is `declined`.

Brand or palette variants that depend on page context (for example `body.brand-<slug>` from a company taxonomy) are verified through the fixture `body_class`. On the test page all rows render in the page's own palette, and that is expected.

Brand or palette variants that depend on page context (for example `body.brand-<slug>` from a company taxonomy) are verified through the fixture `body_class`. On the test page all rows render in the page's own palette, and that is expected.

## CSS boundary

This Skill never writes or edits CSS or SCSS.

Allowed:

- Detect CSS needs from Figma and existing patterns.
- Document CSS paths, primary class, wrapper classes, custom properties, and selectors to preserve in the work record.
- Set stable, predictable hook classes in the template markup.
- Record `CSS status` (`existing`, `new-needed-for-frontend`, `unknown`, `not-applicable`).

Not allowed: creating or editing Section CSS/SCSS files, editing generated CSS, final responsive QA, pixel-level visual checks, or hover/focus styling. Those belong to `frontend-section-qa` (whose CSS/QA run executes through the `cpt-visual-implementer` runner on every start), which works in the same repository on tracked CSS source: injection-proof iteration first, served pass only after a bridge-verified deploy.

When a new CSS file or style loader entry is needed, record it under `CSS status = new-needed-for-frontend` so the frontend pass creates or registers it in tracked source.

## Media handling

After Figma or source analysis, perform a lightweight Media Library check over the WordPress REST API whether the required assets already exist. Keep this token-efficient.

- If matches are quickly identifiable, use the existing attachment IDs and record them in the work record.
- If assets are missing and the Section needs them to be testable, import them over the WordPress REST media route with the `.env` application password (safe filenames, alt text, verification), or hand the asset list to the user for admin upload. Record the imported attachment IDs.
- Ask before importing when asset rights are unclear or many candidate assets exist.

## Deploy pass and served verification

Everything deploy-needing from this run goes into one pass (`deploy-and-branches` Rule): template, ACF JSON group, FC wiring, registration, fixtures, work record. Then:

1. Pull-before-deploy: the pre-commit hook (`acf_json_pull_hook: active` in `PROJECT-CONTEXT.md`) mirrors the server `acf-json/` into the repository on the commit and stops on a future `modified`; without the hook, pull the complete listing over read-only FTP manually and run the `modified` check yourself (`acf-local-json` Rule 1). Either way the deploy cannot overwrite or delete newer server JSONs.
2. Commit with the `Made with: SmartFlow` trailer. HARD STOP: hand over with the commit hash, what the deploy will deliver, and every `proven except runtime (<context>)` shortcode form as a named runtime risk ("the first deploy is the runtime test — please check output X on page Y"). The agent never pushes.
3. After the user reports pushing, verify `deployed_commit` over the status bridge with the bounded retry budget (`status-bridge` Rule). No served result counts while the hashes differ.
4. Field-definition sync: when the pass changed ACF JSON, hand over for the human sync with the fixed text from "Admin Sync — Hand-Over Text For Colleagues" in the `acf-local-json` Rule — a colleague reviews the diff and clicks Sync. Structural changes are not live before that click. Editor-only effects (tabs, conditional logic) additionally stay `implementation pass, backend check by colleague pending` until the colleague confirms them.
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
4. **Spawn exactly one `cpt-visual-implementer` with `frontend-section-qa`** (Skill name, Section, work-record path, CSS file scope, proof mode, QA profile) and stop until it returns. The main chat never executes the CSS/Playwright pass itself — "nur Styling", a small task, or design material already read are not reasons to skip the runner (`runner-gate` Rule).

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
- The Skill identifies the existing `layout_intro` first, writes a minimal work record, and spawns the `cpt-visual-implementer` with `frontend-section-qa`.

### Example D: "Alternatives Intro" with `layout_intro` already in the repository

- Brief: "Das ist ein alternatives Intro" plus a Figma frame with three images. Discovery uses "Intro" as search input and finds `sections/intro.php`, `layout_intro`, group `[TMPL] Intro`, `.wso-section-intro`.
- Work type after discovery: `unclear` (existing same-purpose Section). Structural question block: "remodel `intro` — new layout value `three` on the existing layout select — vs. new Section `intro-subpage`. Recommendation: remodel, because content, selectors, and `intro.css` already exist." No slug is proposed before this answer.
- Confirmed: `existing-section-remodel`. New layout value on the existing group, the image clone for the new variant grouped under a local conditional tab in exclusion form (clone/tab pattern, `acf-local-json` Rule), template branch in place, protected artifacts recorded.
- Harness state `active` (files present, key missing → key written): Intro export config filled, HARD STOP backend review, fixtures exported, `/section-preview/intro/three` recorded before `frontend-section-qa` starts.
