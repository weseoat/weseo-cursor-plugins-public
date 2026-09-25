---
name: section-preview-harness
description: Set up and operate project-local WordPress/WST Section preview pages (a "preview harness") that render a single WST Section in isolation under a stable preview URL from Git-tracked JSON fixtures. Each Section variant gets its own URL so browser QA can check one Section quickly, without reading a whole customer page (fewer tokens, stable QA hooks). Two distinct jobs - the one-time bootstrap (harness files, include, PROJECT-CONTEXT key section-preview-pages as a gate) and the per-Section sub-workflow "Configure a further Section" (export config, backend review stop, fixture export - no bootstrap). Use when a project wants preview pages, Storybook-like WST previews, fixture-driven Section QA, variant verification for a Section remodel, fixtures for a Section in an already active harness, or first browser targets for frontend-section-qa. Proven end-to-end in a full Section remodel pilot (2026-06); the fixture export route is adapted for the shell-less SmartFlow workspace.
---

# Section Preview Pages (Preview Harness)

Use this Skill to set up project-local preview pages for WST Sections. In plain terms, it gives each Section variant its own small page that shows only that one Section, under a stable address:

```text
/section-preview/<section>/<variant>
e.g. /section-preview/intro/split
```

Why this helps the workflow: browser QA can open one Section in isolation and check it fast, instead of loading and parsing a full customer page to find the Section. That keeps QA focused, gives stable hooks to select the Section, and reads far less markup (fewer tokens) per check.

The mechanism is called a "preview harness" (the technical term used in the code and the `PROJECT-CONTEXT.md` block). It renders ONE real Section template with fixed fixture data through the real WST path (`get_header()` + real Section template + `get_footer()`, real theme CSS, real tokens), so browser QA verifies variants without parsing a full customer page. No preview adapter layer is needed: WST templates read their data through ACF, and `acf_setup_meta()` (ACF Local Meta, the same mechanism ACF Blocks use) feeds them fixture data through the identical code path.

The preview pages are project-local infrastructure inside the active child theme. They are not a replacement for the WST workflow Skills, `frontend-section-qa`, or final full-page verification on a real page.

## Two jobs: bootstrap once, configure per Section

This Skill has two separate entry points. Confusing them is the known failure mode ("harness files exist, so neither offer nor use it"):

| Job | When | What it touches | Confirmation stops |
|---|---|---|---|
| **Bootstrap** ("Setup workflow" below) | the project has no harness | three harness PHP files, `require_once` in the snippet loader, `PROJECT-CONTEXT.md` key | `theme-functions.php` edit needs explicit user confirmation |
| **Configure a further Section** (sub-workflow below) | the harness is active and the current Section has no fixtures | the Section's entry in the exporter config (tracked theme file), fixtures under `section-previews/<section>/`, the Section work record | backend review of the rows before export (hard stop) — no bootstrap stop |

Offering the bootstrap stays a recommendation the user may decline. **Using** an active harness for a Section is not optional: `wst-section-workflow` routes every Section in an active harness through "Configure a further Section" before frontend QA starts.

## Harness state detection (three states, three sources)

Decide the state before any Section work, with these sources in this order:

1. `PROJECT-CONTEXT.md` key `section-preview-pages` — `active` or `declined` — the fastest source and the only one that carries `declined`.
2. File `smart-template-builder/section-preview-harness.php` in the child theme.
3. `_export-fixtures.php` with at least one real Section config (`source_page` not `0`, `rows` not empty).

| State | Condition | Action |
|---|---|---|
| **missing** | none of the three sources | offer the bootstrap (recommendation, not a hard stop); on no, write `section-preview-pages: declined` |
| **active** | any one of the three sources | the harness counts as installed. Files present but key missing → write `section-preview-pages: active` into `PROJECT-CONTEXT.md` now and report it; then run "Configure a further Section" for every Section without fixtures |
| **declined** | key `section-preview-pages: declined` | skip previews; `frontend-section-qa` records `Preview URLs: n/a (declined)` |

There is no fourth state. "Files present, do not touch the setup" is the **active** state with an unconfigured Section — configure it, do not re-offer or skip.

## One workspace, one delivery path

All harness files and fixtures are tracked source in the wp-content-level repository. There is no server shell: nothing is written on the server directly. Changes reach the server only through the bundled deploy pass of the `deploy-and-branches` Rule — commit, hard stop, the user pushes, the deploy delivers the child theme, and the status bridge confirms `deployed_commit` before any served preview URL counts as verified.

Consequences to plan around:

- A preview URL renders a fixture only after the commit containing that fixture is bridge-verified as deployed. Bundle harness code and the first fixtures into one deploy pass whenever possible.
- Rewrite flushes go through the status bridge (`POST /wp-json/wso/v1/flush-permalinks`), not WP-CLI.
- Fixture export runs over the harness's gated REST export route (below); the agent writes the returned fixtures into the repository, never onto the server filesystem.

## Theme assumption (Astra child theme)

The bundled preview-page template (`reference-implementation/_preview-template.php`) calls Astra theme functions directly (`astra_primary_class()`, `astra_primary_content_top()`, `astra_primary_content_bottom()`), because WESEO projects run an Astra child theme. This is an explicit project assumption, not a generic WordPress contract. On a non-Astra theme, replace those calls with the active theme's primary-content wrapper and hooks before relying on the preview output. Keep the `#primary` element plus the `data-preview-section` / `data-preview-variant` attributes regardless of theme, because the QA hooks depend on them.

## Architecture (proven reference)

Three PHP files, shipped in `reference-implementation/` and copied into the repository's child theme per project, plus the fixtures they read:

| File | Location in child theme | Role |
|---|---|---|
| `section-preview-harness.php` | `smart-template-builder/` | Route, query vars, access gate, fixture loader, query-lifecycle hardening, ACF shim, render function; loads the export route |
| `_preview-template.php` | `smart-template-builder/section-previews/` | `get_header()` + one Section + `get_footer()`, with `data-preview-section`/`data-preview-variant` QA hooks on `#primary` |
| `_export-fixtures.php` | `smart-template-builder/section-previews/` | Gated REST fixture export route (see below) |

Fixtures live next to them: `section-previews/<section>/<variant>.json`. One file = one variant. Unknown section/variant = 404 (resolution by fixture file existence, no separate allowlist to maintain).

Loading: one `require_once` from the theme's snippet loader (`theme-functions.php` or the project's include file). The harness registers its own hooks and loads the export route itself. Editing `theme-functions.php` is a sensitive bootstrap change: confirm it explicitly with the user first (per the `file-edit-boundary` Rule) and never touch `functions.php`. After the deploy that first delivers the harness to an environment, flush rewrites once through the status bridge (`POST /flush-permalinks`).

Protection (always on): preview requests are allowed only for logged-in users or `*.weseo.dev` hosts; responses send `X-Robots-Tag: noindex, nofollow`, `nocache_headers()`, and `DONOTCACHEPAGE`. On live the route never renders publicly. The export route requires `manage_options`. Adjust the host gate if a project uses a different dev/staging host convention.

## Fixture format

```json
{
    "label": "Split variant",
    "description": "Exported from \"All Sections\" (page <source-page-id>, row 3) on <date>. Figma <node-id>. Re-export: GET /wp-json/wso-preview/v1/export/intro",
    "template": "sections/intro.php",
    "layout": "layout_intro",
    "body_class": "brand-a",
    "data": {
        "intro_variant": "split",
        "intro_content_title": "Example headline",
        "intro_image_img": "<attachment-id>"
    }
}
```

Critical rules:

- `data` keys are the **expanded** clone field names of the layout (`intro_content_title`, not `title`). WST layouts use seamless clones with `prefix_name=1`; the expanded names are what is stored in postmeta and what `get_sub_field()` resolves. Derive them from the ACF JSON sources under `themes/<child-theme>/acf-json/` (definitions are tracked files in SmartFlow projects, `acf-local-json` Rule) or from an existing page row over the export route. See `REFERENCE.md`.
- Repeaters are nested arrays of row objects (update-field shape), not flat count+index keys.
- `body_class` carries page-level context the Section depends on (for example a `brand-<slug>` palette from a company taxonomy). The harness appends it to `<body>` plus the stable `wso-section-preview` QA hook.
- Fixture content is fake or exported demo content. Never customer secrets.

## Fixture export route (do not write fixtures by hand)

`_export-fixtures.php` registers `GET /wp-json/wso-preview/v1/export/<section>` (`manage_options`, authenticated like the status bridge with the application password from the project `.env`). It reads the Flexible Content rows of a configured source page **raw from postmeta** (exactly the state that renders) and returns one fixture per configured row. Per-section config maps row index -> `slug`, `label`, `body_class`, `expect_variant`, `design` reference; the config lives in the tracked file, so config changes are commits.

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" "<site-url>/wp-json/wso-preview/v1/export/intro"
```

- The route validates before returning each fixture: row layout must match, and the row's variant field must equal `expect_variant` (wrong row order produces a `skipped` entry, never a broken fixture).
- Repeaters are converted from flat meta (count + indexed keys) to nested arrays generically.
- The agent writes each returned fixture to `section-previews/<section>/<slug>.json` in the repository (pretty-printed, trailing newline), reviews the diff, and commits. The next deploy delivers the fixtures; only then do the preview URLs render them.
- Re-export after any content change: same request, rewrite the repository fixtures, commit. If the source page moves (for example test page -> catalog page), only `source_page` + row indexes change in the config; that config change must be deployed before the re-export reads the new page.
- Fixtures are Git-tracked snapshots. The preview never reads the source page at request time.

Recommended content lifecycle (from the pilot, adapted to the shell-less workspace):

1. The `wst-section-workflow` run prepares an exact variant row plan (one Flexible Content row per variant, real design content, media attachment IDs); the rows are entered on an unlinked test page in the admin by the user, or through a project-documented programmatic route when one exists.
2. Maintainer reviews the rows in the backend (human stop).
3. Export fixtures from the test page over the export route; write, commit, deploy; run structural preview QA per variant.
4. Preview URLs become the first browser targets for the frontend QA pass; full-page QA happens on the test page.
5. After acceptance: migrate rows append-only to the project's canonical catalog page (for example "All Sections"), re-point the exporter config, re-export, delete the test page. Byte-identical `data` blocks after re-export prove migration fidelity. See `wst-section-workflow` for the migration stops.

## Setup workflow

Ask only for values not already in `PROJECT-CONTEXT.md` or the project docs layer:

1. Which child-theme path may contain harness code? (Default: `smart-template-builder/`)
2. First target Section and variants to cover?
3. Access gating beyond the default logged-in-or-`*.weseo.dev` rule?
4. URL prefix? Default `/section-preview/<section>/<variant>`.

Then: copy the three reference files into the repository child theme, adjust the function prefix and host gate if the project differs, add the `require_once` (confirm the `theme-functions.php` edit explicitly with the user), fill the exporter config, and write the preview-pages block into `PROJECT-CONTEXT.md` with the stable key line `section-preview-pages: active` (plus the URL prefix and the source page). The key line is a **gate** of this Skill, not a courtesy note: `wst-section-workflow` greps for it, and prose alone ("preview URLs exist as a separate path") leaves the next run blind. Deploy pass: commit, hard stop, user pushes, bridge-verify `deployed_commit`, flush rewrites through the bridge once, then verify one URL end-to-end (200 + section class + fixture content).

Before relying on rendering details, read `REFERENCE.md` — it documents the WordPress query-lifecycle hardening (`is_home`, phantom-post leaks, WST archive-404) and the ACF resolution pitfalls (multiple fields named `flexible_content`, ACFE hybrid mode) that cost the pilot the most debugging time.

## Sub-workflow: Configure a further Section

Run this whenever the harness is **active** and the current Section has no fixtures (no `section-previews/<section>/*.json`, or an exporter stub such as `source_page: 0` / `rows: []`). It runs in the main chat — runners do not edit harness files; they return `OPEN DECISION: preview-fixtures` per the `agent-routing` Rule and the main chat continues here. No bootstrap step is repeated and no `theme-functions.php` confirmation is needed: the exporter config is an ordinary tracked theme file.

1. **Row plan.** Take the variant row plan from the `wst-section-workflow` run (one Flexible Content row per variant on the unlinked test page, real design content, media attachment IDs). If the rows are not on the page yet, they are entered first (admin by the user, or the project's documented programmatic route).
2. **Exporter config.** Add or complete the Section's entry in `_export-fixtures.php`: `source_page` = test page ID, `variant_field` = the expanded variant field name, one row entry per variant (`slug`, `label`, `body_class`, `expect_variant`, `design`). Replace stubs; never leave `source_page: 0` behind.
3. **HARD STOP — backend review.** The maintainer reviews the rows in the backend (layout, variant value, content, media) before anything is exported. Do not continue on your own.
4. **Export.** After the go: the config change must be served (it goes with the Section's deploy pass, bridge-verified), then call `GET /wp-json/wso-preview/v1/export/<section>` and write every returned fixture to `section-previews/<section>/<slug>.json` (pretty-printed, trailing newline). Review the diff; `skipped` entries mean wrong row order or variant value — fix the rows or the config, never the fixture by hand.
5. **Work record.** Write the preview URLs (`/section-preview/<section>/<variant>` per variant) into the Section work record as the first Visual QA Targets; the fixtures travel with the next deploy pass and render only after it is bridge-verified.
6. **Hand-over.** Report: config entry, fixtures written, preview URLs, deploy pass pending. `frontend-section-qa` starts only once these URLs are real `/section-preview/…` addresses (or the project is `declined`).

Re-runs (content changed, source page moved) repeat steps 2–5 with the review stop in place.

## QA integration

- Preview QA is structural and cheap: HTTP status, `data-preview-variant`, variant root class, expected content/image per variant, body classes. Run it from the local workspace via curl, Playwright MCP, or a small script (see `reference-implementation/structural-preview-qa-example.php` for the check logic); evaluate ONLY the section between its anchor and the first real end tag — never anchor checks on HTML comments (minifiers strip them) and beware global template instances outside `#primary` (for example reference popups).
- Previews are nocache by design: they hide page-cache and Delay-JS bug classes. Final full-page QA on a real cached page (including pre-interaction measurements) stays mandatory.
- `frontend-section-qa` uses preview URLs as first browser targets and the QA-hook attributes for stable selection. CSS iteration against previews is injection-proof by default; a served pass additionally requires the bridge-verified `deployed_commit` match.

## Safety boundary

Allowed: project-local harness files in the repository child theme, fixture JSON in the repository, export-route reads, work-record notes with preview URLs and QA targets in the project docs layer.

Forbidden: customer content edits, ACF/FC/CPT/WPGB setup (that belongs to the WST workflow Skills), commits or pushes outside the `deploy-and-branches` flow, secrets or real customer data in fixtures, preview-only production CSS, and treating a preview pass as a final pass. Editing `functions.php` is forbidden; `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change.

## Completion checklist

- [ ] Harness include + preview template + export route in the repository child theme, loaded via snippet loader (with confirmed `theme-functions.php` edit).
- [ ] Deploy pass done: committed, user pushed, bridge-verified `deployed_commit` match.
- [ ] Rewrites flushed once through the status bridge on the environment.
- [ ] Preview route works for one Section and its variants; unknown section/variant returns 404.
- [ ] Valid previews return 200 (no 404 status with rendered body — that means the query hardening is missing).
- [ ] Fixtures exported from a reviewed source page over the export route, Git-tracked, no secrets.
- [ ] `body_class` covers palette/context variants where the design needs them.
- [ ] Structural preview QA passes for every variant.
- [ ] `section-preview-pages: active` key line present in `PROJECT-CONTEXT.md` (gate — prose without the key does not count); the block names URL prefix and source page.
- [ ] The Section work record in the project docs layer records preview URLs and QA targets.
- [ ] No exporter stub left behind (`source_page: 0`, empty `rows`) for a Section that has rows on the source page.
- [ ] Final full-page QA on a real page remains listed as mandatory.

For "Configure a further Section" runs, the checklist reduces to: config entry complete, backend review stop held, fixtures exported over the route and tracked, preview URLs in the work record, deploy pass pending noted.
