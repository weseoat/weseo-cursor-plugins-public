---
name: section-preview-harness
description: Set up and operate project-local WordPress/WST Section preview pages (a "preview harness") that render a single WST Section in isolation under a stable preview URL from exported JSON fixtures. Each Section variant gets its own URL so the local frontend phase can check one Section quickly, without reading a whole customer page (fewer tokens, stable QA hooks). Use when a project wants preview pages, Storybook-like WST previews, fixture-driven Section QA, variant verification for a Section remodel, or first browser targets for frontend-design-qa. Proven end-to-end in a full Section remodel pilot (2026-06).
---

# Section Preview Pages (Preview Harness)

Use this Skill to set up project-local preview pages for WST Sections. In plain terms, it gives each Section variant its own small page that shows only that one Section, under a stable address:

```text
/section-preview/<section>/<variant>
e.g. /section-preview/intro/split
```

Why this helps the workflow: the local frontend QA phase can open one Section in isolation and check it fast, instead of loading and parsing a full customer page to find the Section. That keeps QA focused, gives stable hooks to select the Section, and reads far less markup (fewer tokens) per check.

The mechanism is called a "preview harness" (the technical term used in the code and PROJECT-CONTEXT block). It renders ONE real Section template with fixed fixture data through the real WST path (`get_header()` + real Section template + `get_footer()`, real theme CSS, real tokens), so browser QA verifies variants without parsing a full customer page. No preview adapter layer is needed: WST templates read their data through ACF, and `acf_setup_meta()` (ACF Local Meta, the same mechanism ACF Blocks use) feeds them fixture data through the identical code path.

The preview pages are project-local infrastructure inside the active child theme. They are not a replacement for `wst-builder` server work, `frontend-section-qa`, or final full-page verification on dev/staging.

## Theme assumption (Astra child theme)

The bundled preview-page template (`reference-implementation/_preview-template.php`) calls Astra theme functions directly (`astra_primary_class()`, `astra_primary_content_top()`, `astra_primary_content_bottom()`), because WESEO projects run an Astra child theme. This is an explicit project assumption, not a generic WordPress contract. On a non-Astra theme, replace those calls with the active theme's primary-content wrapper and hooks before relying on the preview output. Keep the `#primary` element plus the `data-preview-section` / `data-preview-variant` attributes regardless of theme, because the QA hooks depend on them.

## Architecture (proven reference)

Three PHP files, shipped in `reference-implementation/` and copied into the child theme per project, plus the fixtures they read:

| File | Location in child theme | Role |
|---|---|---|
| `section-preview-harness.php` | `smart-template-builder/` | Route, query vars, access gate, fixture loader, query-lifecycle hardening, ACF shim, render function |
| `_preview-template.php` | `smart-template-builder/section-previews/` | `get_header()` + one Section + `get_footer()`, with `data-preview-section`/`data-preview-variant` QA hooks on `#primary` |
| `_export-fixtures.php` | `smart-template-builder/section-previews/` | WP-CLI fixture exporter (see below) |

Fixtures live next to them: `section-previews/<section>/<variant>.json`. One file = one variant. Unknown section/variant = 404 (resolution by fixture file existence, no separate allowlist to maintain).

Loading: one `require_once` from the theme's snippet loader (`theme-functions.php` or the project's include file). The harness registers its own hooks. Editing `theme-functions.php` is a sensitive bootstrap change: confirm it explicitly with the user first (per the file-edit boundary rule) and never touch `functions.php`. After deploying to a new environment run `php wp-cli.phar rewrite flush` once.

Protection (always on): requests are allowed only for logged-in users or `*.weseo.dev` hosts; responses send `X-Robots-Tag: noindex, nofollow`, `nocache_headers()`, and `DONOTCACHEPAGE`. On live the route never renders publicly. Adjust the host gate if a project uses a different dev/staging host convention.

## Fixture format

```json
{
    "label": "Split variant",
    "description": "Exported from \"All Sections\" (page <source-page-id>, row 3) on <date>. Figma <node-id>. Re-export: php wp-cli.phar eval-file .../_export-fixtures.php intro",
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

- `data` keys are the **expanded** clone field names of the layout (`intro_content_title`, not `title`). WST layouts use seamless clones with `prefix_name=1`; the expanded names are what is stored in postmeta and what `get_sub_field()` resolves. See `REFERENCE.md` for how to dump a layout's expanded structure.
- Repeaters are nested arrays of row objects (update-field shape), not flat count+index keys.
- `body_class` carries page-level context the Section depends on (for example a `brand-<slug>` palette from a company taxonomy). The harness appends it to `<body>` plus the stable `wso-section-preview` QA hook.
- Fixture content is fake or exported demo content. Never customer secrets.

## Fixture exporter (do not write fixtures by hand)

`_export-fixtures.php` reads the Flexible Content rows of a configured source page **raw from postmeta** (exactly the state that renders) and writes one fixture per configured row. Per-section config maps row index -> `slug`, `label`, `body_class`, `expect_variant`, `design` reference.

- Validation before each write: row layout must match, and the row's variant field must equal `expect_variant` (wrong row order produces a SKIP, never a broken fixture).
- Repeaters are converted from flat meta (count + indexed keys) to nested arrays generically.
- Re-export after any content change: same command. If the source page moves (for example test page -> catalog page), only `source_page` + row indexes change in the config.
- Fixtures are Git-tracked snapshots. The preview never reads the source page at request time.

Recommended content lifecycle (from the pilot):

1. Server phase builds variants on an unlinked test page (1 FC row per variant, prefilled programmatically with real design content).
2. Maintainer reviews rows in the backend (human stop).
3. Export fixtures from the test page; run preview QA per variant.
4. Frontend phase consumes preview URLs; full-page QA happens on the test page.
5. After acceptance: migrate rows append-only to the project's canonical catalog page (for example "All Sections"), re-point the exporter config, re-export, delete the test page. Byte-identical `data` blocks after re-export prove migration fidelity.

## Setup workflow

Ask only for values not already in `PROJECT-CONTEXT.md` or the active handoff:

1. Which child-theme path may contain harness code? (Default: `smart-template-builder/`)
2. First target Section and variants to cover?
3. Access gating beyond the default logged-in-or-`*.weseo.dev` rule?
4. URL prefix? Default `/section-preview/<section>/<variant>`.

Then: copy the three reference files, adjust the function prefix and host gate if the project differs, add the `require_once` (confirm the `theme-functions.php` edit explicitly with the user), flush rewrites, create or export the first fixtures, verify one URL end-to-end (200 + section class + fixture content), and document the preview-pages block in `PROJECT-CONTEXT.md` (record it under a stable key such as `section-preview-pages` so `wst-section-workflow` can detect it).

Before relying on rendering details, read `REFERENCE.md` - it documents the WordPress query-lifecycle hardening (`is_home`, phantom-post leaks, WST archive-404) and the ACF resolution pitfalls (multiple fields named `flexible_content`, ACFE hybrid mode) that cost the pilot the most debugging time.

## QA integration

- Preview QA is structural and cheap: HTTP status, `data-preview-variant`, variant root class, expected content/image per variant, body classes. Run it via curl/PHP (see `reference-implementation/structural-preview-qa-example.php`); evaluate ONLY the section between its anchor and the first real end tag - never anchor checks on HTML comments (minifiers strip them) and beware global template instances outside `#primary` (for example reference popups).
- Previews are nocache by design: they hide page-cache and Delay-JS bug classes. Final full-page QA on a real cached page (including pre-interaction measurements) stays mandatory.
- `frontend-section-qa` uses preview URLs as first browser targets and the QA-hook attributes for stable selection.

## Safety boundary

Allowed: project-local harness files in the child theme, fixture JSON, exporter runs, handoff notes with preview URLs and QA targets.

Forbidden: customer content edits, ACF/FC/CPT/WPGB setup (that is `wst-builder` server work), cache/deployment changes beyond documented flush commands, secrets or real customer data in fixtures, preview-only production CSS, and treating a preview pass as a final pass. Editing `functions.php` is forbidden; `theme-functions.php` and MU plugin files require explicit prior user confirmation for the exact change.

## Completion checklist

- [ ] Harness include + preview template + exporter in the child theme, loaded via snippet loader (with confirmed `theme-functions.php` edit).
- [ ] Rewrites flushed once on the environment.
- [ ] Preview route works for one Section and its variants; unknown section/variant returns 404.
- [ ] Valid previews return 200 (no 404 status with rendered body - that means the query hardening is missing).
- [ ] Fixtures exported from a reviewed source page, Git-tracked, no secrets.
- [ ] `body_class` covers palette/context variants where the design needs them.
- [ ] Structural preview QA passes for every variant.
- [ ] PROJECT-CONTEXT documents the preview-pages block; the active Section handoff records preview URLs and QA targets.
- [ ] Final full-page QA on a real page remains listed as mandatory.
