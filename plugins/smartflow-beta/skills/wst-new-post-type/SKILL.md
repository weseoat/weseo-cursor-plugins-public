---
name: wst-new-post-type
description: Plan, classify, and execute WST Custom Post Type work in the local SmartFlow workspace. Use for any new CPT foundation, existing CPT remodel, CPT/WPGB/card preflight, taxonomy or ACF CPT changes, optional single templates, or the CPT work record. Templates and ACF Local JSON field groups are authored as tracked source and reach the server through the bundled deploy pass (field definitions go live after the human sync in the admin); CPT UI and WPGB configuration are prepared as exact apply-specs for the user. CPT visual-only changes route to the bundled cpt-frontend-qa Skill.
---

# WST New Post Type

This Skill is the single entry point for WST Custom Post Type work. It classifies the request, runs the preflight as a write gate, protects existing CPT artifacts during remodels, and maintains the CPT work record in the project docs layer that the frontend QA pass consumes.

Everything happens in one workspace: the wp-content-level repository checkout. There is no server shell. The CPT foundation splits into three delivery paths:

- **Tracked source** (card templates, optional single templates, ACF Local JSON field groups in `acf-json/`, Section integrations): authored in the repository, delivered through the bundled deploy pass of the `deploy-and-branches` Rule, verified over the status bridge; field-definition changes additionally need the human sync click in the admin (`acf-local-json` Rule).
- **Admin-managed objects** (CPT UI registration, taxonomy registration, WP Grid Builder grids/cards): prepared as exact apply-specs in the work record and applied in the WordPress admin by the user. WPGB config is read back over the status bridge. For WPGB grids/cards there is a validatable alternative: when the installed bridge is version 1.1.1 or newer and `PROJECT-CONTEXT.md` records the bridge WPGB write route as project-validated, the `wpgb-specialist` applies and creates the configuration over the `wso/v1/wpgb/*` routes (per the `status-bridge` Rule) instead of handing an admin apply-spec to the user; without that validation, the admin apply-spec stays the standard route.
- **Bridge actions** (cache flush, permalink flush after registration or rewrite changes): `POST /flush-cache` and `POST /flush-permalinks` per the `status-bridge` Rule.

This Skill does not own CPT CSS or SCSS. It documents CSS paths, stable classes, hooks, and expected behavior in the work record; CSS implementation belongs to `cpt-frontend-qa` (Section-level CPT displays route Section behavior to `frontend-section-qa`). On every start — direct start included, not only under package orchestration — the main chat acts as the orchestrator: classification, the preflight, work-record creation, and all hard stops stay in the main chat, while the foundation execution run (template and ACF JSON writes) executes through the `wst-shortcode-implementer` runner per the `agent-routing` Rule; WPGB work routes to the `wpgb-specialist`, and the preflight's discovery evidence comes from the `cpt-codebase-analyst` and — when a design source exists — the `cpt-figma-analyst` (see the preflight below). Hard stops that surface inside the run come back to the main chat as `OPEN DECISION` in the runner's return format; this Skill's semantics are identical either way.

## Hard stop rules

Apply these rules before any other action.

Do not perform any write operation before the CPT preflight has produced a concrete work-record draft. Write operations include template files, ACF JSON field groups, prepared registration or WPGB apply-specs handed to the user, content plans, commits, and bridge flushes.

- If work type is `unclear`, stop and ask for the missing decision.
- If work type is `visual-only`, route to `cpt-frontend-qa` without structural work.
- For `existing-cpt-remodel`, do not create a new CPT, taxonomy, ACF JSON group, WPGB grid/card, card template, archive integration, single template, CSS file, or style loader entry unless the confirmed work record explicitly says the remodel requires a new artifact. Preserve existing registered post type, rewrite behavior, taxonomy names, field keys, WPGB IDs, template paths, and selectors by default.
- Structural ACF database writes are forbidden without exception (`acf-local-json` Rule): field definitions are JSON files in `acf-json/`, never `acf-field`/`acf-field-group` posts; structural changes go live only through deploy plus the human sync click. Field key or name changes on saved fields are data migrations needing an explicit user decision.
- `functions.php` is forbidden; `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change (`file-edit-boundary` Rule).
- Never write project-owned CPT artifacts under `plugins/weseo-smart-template-builder/`; they live in the child theme under `themes/<child-theme>/smart-template-builder/`.
- Built-in hard stop of every deploy pass: after committing, stop and hand over per the `deploy-and-branches` Rule. The agent never pushes.

## Quick start

1. Classify the request into a `Work type`.
2. Immediately ask for the source brief, Figma link, or display requirement for the CPT and whether public detail pages are required.
3. Run the preflight (below) to produce the work-record draft in the project docs layer.
4. For `new-cpt-foundation` and confirmed `existing-cpt-remodel`, perform only the explicitly approved steps.
5. For `visual-only`, route to `cpt-frontend-qa` without structural work.
6. Bundle everything deploy-needing into one pass; hand registration and WPGB apply-specs to the user; verify over the bridge and the rendered site.
7. Complete the CPT work record and route to `cpt-frontend-qa`, with `frontend-section-qa` noted when a dedicated Section owns layout behavior.

Before asking the maintainer for technical values, search `PROJECT-CONTEXT.md` and the project docs layer for:

- Child theme name and WST template path (`themes/<child-theme>/smart-template-builder/`; the WST plugin folder is off-limits).
- CPT naming conventions, URL slug policy, and whether the CPT has a detail page.
- Existing ACF JSON groups under `themes/<child-theme>/acf-json/`, the installation's JSON filename convention, and field key naming expectations.
- WP Grid Builder grid/card conventions and where grid/card IDs are recorded (`GET /status` lists the existing grids).
- Target dev or staging URL and the docs-layer conventions.
- Existing selectors, CPT references, taxonomy references, card/grid/single template paths, and project workflow notes.

If any blocking value is missing, stop and ask or record an explicit unresolved placeholder in the work-record draft. Do not invent CPT names, rewrite slugs, taxonomy names, field keys, WPGB IDs, paths, URLs, selectors, or theme values.

## 1. Classify the work

The very first decision is the `Work type`. Until this is recorded in the work-record draft, no write is allowed.

| Work type | Meaning |
| --- | --- |
| `new-cpt-foundation` | A WST CPT that does not exist yet. Prepare CPT registration and optional taxonomy for the user, create the ACF JSON field group, prepare the WPGB card/grid foundation, create card templates, optional archive/grid integration, optional single template, and CSS hook documentation. |
| `existing-cpt-remodel` | An existing WST CPT whose registration, taxonomy, ACF shape, WPGB setup, templates, or display integration needs to change. Default to preserving structure and require explicit approval for each new artifact. |
| `visual-only` | An existing CPT display whose visual behavior should change without touching CPT, taxonomy, ACF, WPGB, template, registration, or content structure. Route immediately to `cpt-frontend-qa`. |
| `unclear` | The request cannot be classified yet. Stop and ask before any write or read-only audit beyond project context. |

## 2. Mandatory preflight as write gate

The preflight is evidence-first and intentionally short. After `Work type` is classified, ask the maintainer only:

1. `Please send the source brief, Figma link, or reference for this CPT display.`
2. `Should this CPT have public detail pages? If yes, what URL slug or slug policy should it use?`

When `PROJECT-CONTEXT.md` records a Confluence anchor, re-read the anchored PL page **fresh at preflight start** over the Atlassian MCP and pull only the section relevant to this CPT — the matching task row, module notes, content source, and Figma link — into the work-record draft (`confluence-source` Rule). No anchor, or no usable Atlassian MCP: skip cleanly, record `confluence-source: no anchor` (or `MCP unavailable`) in the draft, and continue from the mirror. Never re-read mid-run; runners receive the distilled extract in their prompt and never call Confluence.

Then inspect project context, existing CPT patterns (templates, ACF JSON groups, rendered markup, `GET /status` for ACF groups and WPGB grids) before asking anything else. Do not ask the maintainer to specify HTML structure, wrapper classes, field names, selectors, spacing, responsive behavior, or interaction details that are visible in the source design or inferable from existing project patterns. On every start — direct start included — the `cpt-codebase-analyst` supplies this evidence as the read-only discovery leaf (reference baseline, Project Layout Profile, precedence, minimal file scope, evidenced route, per the `agent-routing` Rule) instead of the main chat running the inventory inline; its return lands in the work-record draft. Skip the spawn only when the project docs layer or `PROJECT-CONTEXT.md` already records a verified reference baseline and Project Layout Profile for this project — then the main chat reads only the delta for this CPT from those records. When the source brief carries a Figma or design-file reference, the `cpt-figma-analyst` extracts the design facts (surface index mapped against the reference baseline, deviations, raw specs only where needed); without a design source it has nothing to extract and does not spawn.

If a detail is not discoverable and a project-default pattern exists, use that assumption in the draft. Ask a follow-up only when the missing answer blocks a safe write: an unknown CPT name, URL slug policy, taxonomy decision, ACF/WPGB reference, or target URL.

The preflight output is the CPT work-record draft in the project docs layer (default `docs/post-types/<resource>.md`). It must record:

- `Work type` and routing decision; write scope (which of the three delivery paths the run touches).
- CPT slug, registered post type, singular and plural labels, admin visibility, and source brief.
- Public detail-page decision, URL slug or explicit no-detail-page decision, archive/search behavior, and unresolved detail-page questions.
- Taxonomy decision per the `wordpress-taxonomies` Rule (English `wso_tax_*` machine name, labels, hierarchy, public archive decision, purpose), or an explicit no-taxonomy decision.
- ACF JSON group file, field names, field ownership decisions, and unresolved field questions.
- WPGB grid/card IDs or explicit no-WPGB decision, plus display target: grid, carousel, existing Section, or dedicated Section.
- Card, archive/grid, optional single, and optional Section integration template paths.
- Expected card, archive/grid, optional single, and Section integration selectors to preserve.
- Expected desktop, tablet, mobile, content variation, filtering, linking, and interaction behavior.
- `Preflight gate status`, known risks, open questions, `Protected existing artifacts` for remodels, and `CSS status` (`existing`, `new-needed-for-frontend`, `unknown`, `not-applicable`).

Do not proceed past the preflight while the draft contains unresolved placeholders for `Work type`, CPT slug/name, detail-page decision, taxonomy decision, target URL, ACF/WPGB references required for the approved work, template paths, CSS path, or frontend responsibilities.

## 3. Existing CPT remodel protections

When `Work type` is `existing-cpt-remodel`, the default position is to preserve everything that exists.

Before any write:

- Read the existing CPT registration (over the rendered admin apply-spec history in the docs layer, `GET /wp-json/wp/v2/types`, or the user), the taxonomy registration, the ACF JSON group, the WPGB setup (`GET /status`), template paths, CSS path, and rendered HTML classes.
- Record those values under `Protected existing artifacts` in the work record.
- Propose the exact scope to the maintainer and wait for explicit approval.

While remodeling, by default preserve: registered post type, labels, rewrite behavior, archive/search behavior, supports; taxonomy name, hierarchy, and public archive behavior; field keys and field ownership; WPGB grid/card IDs and source settings; template paths; and public selectors that templates, scripts, WPGB behavior, styles, or tests rely on.

If the desired change only affects visual spacing, typography, color, responsive behavior, hover/focus state, carousel appearance, filter appearance, or card layout CSS, switch `Work type` to `visual-only` and route to `cpt-frontend-qa`.

## 4. New CPT foundation steps

Run these steps only when `Work type` is `new-cpt-foundation` and the work-record draft is filled.

```text
New WST CPT Foundation:
- [ ] Preflight produced concrete work-record draft (no unresolved blockers)
- [ ] Work type confirmed as `new-cpt-foundation`
- [ ] Prepare CPT registration apply-spec for the user (CPT UI)
- [ ] Prepare taxonomy apply-spec if needed (wordpress-taxonomies Rule)
- [ ] Create the ACF JSON field group for CPT fields
- [ ] Prepare WP Grid Builder card and grid apply-spec (wpgb-specialist under orchestration)
- [ ] Create card template foundation (four-source proof for new shortcode forms)
- [ ] Create optional single template foundation
- [ ] Document CSS hooks and CSS path in the work record (no CSS from this Skill)
- [ ] Deploy pass: commit with trailer, HARD STOP, user pushes, bridge-verify deployed_commit
- [ ] User applies CPT UI / taxonomy / WPGB apply-specs in the admin
- [ ] Flush permalinks through the bridge after registration; flush caches after template/field changes
- [ ] Served verification
- [ ] Complete the CPT work record and route to cpt-frontend-qa
```

Sequence note: the registration apply-spec can go to the user while the deploy pass is pending; but WPGB grid setup needs the CPT and its content to exist, and served verification needs both the deploy and the admin steps done.

### 4.1 Decide foundation shape

Before preparing anything, decide:

- Whether the CPT has public detail pages.
- Whether it needs a taxonomy for filtering, grouping, or card labels.
- Which data belongs in post title, featured image, excerpt/editor, ACF fields, or taxonomy terms.
- Whether WP Grid Builder should render a grid, carousel, or card only.
- Whether a dedicated WST Flexible Content Section is required, or an existing grid/slider Section can consume the grid.

Record unresolved decisions in the work-record draft rather than guessing.

### 4.2 Prepare CPT registration (apply-spec for the user)

CPT registration goes through CPT UI so clients and colleagues can manage it without a developer. The Skill prepares the exact settings in the work record and hands them to the user; see [`reference.md`](reference.md) for the settings shapes.

Default invariants:

- Registered post type follows the project convention, usually `wso_<resource>`.
- `show_ui` and `show_in_rest` enabled unless the maintainer gives a reason not to.
- `supports` includes only fields the content model actually uses.
- Public detail-page CPTs need query, search, archive, and rewrite settings reviewed together.
- Non-detail CPTs must not create public URLs accidentally.

After the user applies the spec: flush permalinks through the bridge and verify the CPT appears in `GET /wp-json/wp/v2/types` (when `show_in_rest` is on) and the admin.

### 4.3 Prepare taxonomy if needed (apply-spec for the user)

Add a taxonomy only when the content model requires grouping, filtering, admin columns, or card labels. Follow the `wordpress-taxonomies` Rule: English `wso_tax_<resource>` machine name, German labels allowed, CPT UI managed, deliberate hierarchy choice, rewrites disabled unless taxonomy archives are part of the brief, fixed terms listed in the apply-spec. Code only for what CPT UI cannot do (for example a `body_class` filter), which lands in `theme-functions.php` only with explicit prior user confirmation.

### 4.4 Create the ACF JSON field group

Create one JSON group file under `themes/<child-theme>/acf-json/` (named per the installation's filename convention from `PROJECT-CONTEXT.md`) whose location rule targets the new CPT, per the `acf-local-json` Rule: fresh stable `group_`/`field_` keys, `acfe_autosync` containing `"json"`, and a `modified` timestamp so the admin offers the sync. See `reference.md` for the shape. If the project has no `acf-json/` setup yet, run the bundled `setup-acf-local-json` Skill first.

Recommended structure: a tab field for admin organization, content fields specific to the CPT, optional tabs for complex CPTs. Field names always carry the `wso_<resource>_` prefix per the `acf-local-json` Rule: a salary field on a Job CPT is `wso_job_salary`, never `job_salary` or `salary`. Prefer core post title, thumbnail, editor, excerpt, and taxonomy terms before duplicating data in ACF fields.

The field group ships with the deploy pass. After the bridge-verified deploy and the human sync in the admin, `GET /status` must list it with `local: "json"`.

### 4.5 Prepare the WP Grid Builder foundation

WPGB grids and cards are admin-managed objects. Default WST pattern:

- The WPGB card exists so the grid can select a card; the visual card builder usually stays empty because WST PHP card templates render the markup.
- The WPGB grid source points at the new CPT.
- The apply-spec names grid title, source post type, selected card, and the settings that deviate from the closest existing baseline grid (read the baseline over `GET /status`). The `wpgb-specialist` owns this spec on every start (per the routing paragraph above and the `agent-routing` Rule) and derives the config values from the design matrix.
- After the user creates the objects, read the generated grid and card IDs back over `GET /status` and record them in the work record and `PROJECT-CONTEXT.md`.

Do not hardcode generated WPGB IDs into reusable plugin content. WPGB objects can be created programmatically over the bridge `wso/v1/wpgb/*` routes — but only when the project has validated that write route (bridge >= 1.1.1 plus the `PROJECT-CONTEXT.md` entry, per the `status-bridge` Rule); the `wpgb-specialist` then clones the closest precedent, creates card before grid, and records the new IDs. Never attempt any other undocumented write route.

### 4.6 Create the card template foundation

Create card template files in the repository:

```text
themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php
```

Common shapes: single-part card; multi-part card (`-part-1`, `-part-2`) when WPGB expects multiple card areas; or a dedicated WST Section that embeds the grid.

Template invariants:

- Use stable `.wso-<resource>-card` style hooks.
- Render core post data and ACF fields through the project's WST shortcodes; every form new to the project needs the four-source proof through the bundled `wst-shortcodes` Skill. Conditionals follow the `wst-conditional-nesting` Rule.
- Use conditionals for optional fields so empty data does not leave broken markup.
- Include accessible link labels when the card has empty or full-card links.
- WPGB placeholder caution: the catalog and a working project implementation disagree on `{{post.id}}` vs `{{post_id}}` in card context. This is an open validation — prove the resolving spelling on the project (rendered card HTML) before relying on it, and record the outcome.

Use [`examples.md`](examples.md) for generic card structures.

### 4.7 Create the optional single template foundation

Only create a single template when the CPT is publicly queryable and the brief requires detail pages:

```text
themes/<child-theme>/smart-template-builder/post-types/<resource>/singles/<resource>-single.php
```

Keep markup compatible with existing WST element, row, wrap, and typography patterns; establish stable `.wso-<resource>-single` hooks; record required local CSS and QA expectations in the work record. If no public detail page exists, record that decision so the frontend pass does not look for a single view.

## 5. CSS hooks belong in the work record

This Skill records CSS hook expectations; it does not create or edit CSS or SCSS. Capture in the work record:

- `Card selector` (usually `.wso-<resource>-card`), `Archive/grid selector`, `Single selector` when detail pages exist, wrapper/item classes, initial CSS custom property names, and `Selectors to preserve`.
- `CSS status`: `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

When the project requires a new CSS file or style loader entry, record that requirement so `cpt-frontend-qa` creates or registers it in tracked source (same repository; injection-proof iteration, served pass only after a bridge-verified deploy).

## 6. Deploy, flush, and verify

Bundle everything deploy-needing into one pass (`deploy-and-branches` Rule): card/single templates, ACF JSON group, Section integrations, work record. Before committing, pull the complete `acf-json/` listing from the server over read-only FTP (`acf-local-json` Rule, pull-before-deploy). Commit with the `Made with: SmartFlow` trailer, HARD STOP, the user pushes, then verify `deployed_commit` over the status bridge with the bounded retry budget. When the pass changed ACF JSON, hand over for the human sync in the admin — structural field changes are not live before that click.

After the deploy, the sync, and the admin apply-specs are done:

- Flush permalinks through the bridge after CPT/taxonomy registration or rewrite changes; verify affected URLs respond without a 404.
- Flush caches through the bridge after template, field, or content changes.
- Verify: the CPT appears in the admin (user confirms) and in `GET /wp-json/wp/v2/types` when REST-exposed; taxonomy UI appears only when expected; `GET /status` lists the field group with `local: "json"` and the WPGB grid; card template markup renders without PHP errors on a page embedding the grid; public single URLs work only when intended; expected card/single CSS hooks exist in rendered markup.

Record the results in the work record under `QA notes`. Until the bridge confirms the deployed commit, results stay `implementation pass, deployed verification pending`.

## 7. Complete the CPT work record

The CPT work record (default `docs/post-types/<resource>.md`) is the durable contract of the run. It is separate from Section work records because CPT work has its own detail-page, taxonomy, WPGB, card, archive, carousel, and optional single-template decisions. Before frontend work starts it must carry everything from the preflight list plus: applied registration and WPGB specs with the generated IDs, deploy state (commit hash, bridge verification), verification results, and the frontend responsibilities (final CSS, responsive checks, Playwright browser QA per the QA viewport ladder).

Completed CPT work routes to `cpt-frontend-qa`. If the CPT display becomes primarily a dedicated WST Section layout, record the split: `frontend-section-qa` owns Section-level behavior, `cpt-frontend-qa` owns card, archive/grid, and optional single-template QA. Findings that need template, field, registration, or WPGB changes come back to this workflow through the work record — that is the normal path, not an exception.

## Generic examples

### Example A: New `team` CPT foundation

- `Work type`: `new-cpt-foundation`. Registered post type `wso_<resource>` prepared as a CPT UI apply-spec; detail-page decision recorded before rewrites or single templates exist.
- ACF JSON group in `acf-json/` per the project filename convention; card template `post-types/<resource>/cards/<resource>-card.php`; one deploy pass plus the admin sync.
- WPGB grid/card created by the user from the apply-spec; IDs read back over `GET /status`.
- `CSS status`: `new-needed-for-frontend`; the work record names the CSS path as `cpt-frontend-qa` work.

### Example B: Existing `team` CPT remodel

- `Work type`: `existing-cpt-remodel`.
- `Protected existing artifacts`: registered post type, rewrite settings, taxonomy name, field keys, WPGB IDs, template paths, CSS path, public selectors.
- Approved scope: only the existing card template change; no new CPT, taxonomy, field group, WPGB objects, or CSS file. One deploy pass, bridge-verified.

### Example C: Visual-only CPT card change

- `Work type`: `visual-only`. No structural work.
- The work record captures the existing CPT identity, display URL, selectors, and the visual-only routing so `cpt-frontend-qa` can start without re-asking structural questions.
