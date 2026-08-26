---
name: figma-page-builder
description: Builds or remodels WordPress/WST pages from Figma by mapping the design to existing Flexible Content Sections, WP Grid Builder surfaces, media, and global elements, then safely populating the complete ACF page structure over REST and verifying the rendered page. Use for requests such as "Seite laut Figma aufbauen", "bestehende Seite befüllen", landing pages, overview pages, and other non-CPT page compositions.
---

# Figma Page Builder

Build a complete WordPress page from a Figma page frame by reusing the
project's existing WST Sections and WPGB surfaces. This Skill owns page
composition and content population, not unrestricted Section development.

The SmartFlow workspace has no server shell: content writes go over the
WordPress REST API per the `wordpress-content-editing` Rule and the
`content` Skill, cache flushes go through the status bridge, and any
tracked source change reaches the server only through the
commit-and-hand-over flow (`deploy-and-branches` Rule).

## Delegation contract

Model routing, the pre-spawn availability check, and the shared return
contract come from the `agent-routing` Rule. Do not silently substitute a
missing model; report it.

Use a maximum of these leaf agents:

1. `cpt-figma-analyst` — extract the ordered Figma page surface index,
   exact copy, media, variants, and responsive evidence.
2. `explore` — inspect the current page, reusable WST layouts, work
   records in the project `docs/` layer, WPGB grids/cards/facets, options,
   media, and precedents.
3. `wst-shortcode-implementer` — only if a confirmed structural WST/PHP or
   ACF-definition change is required. It never owns ordinary page-content
   value writes.
4. `cpt-visual-implementer` (running the bundled `frontend-section-qa` or
   `cpt-frontend-qa` Skill) — only when the page needs actual CSS work
   after composition.

Launch the Figma and codebase analysis agents in parallel. Keep all writes
serialized. Leaf agents never spawn other agents.

## Required input

- A node-specific Figma URL.
- The target WordPress page or explicit permission to create one.
- The intended language.

Derive the page ID, URL, environment, style paths, REST credentials
(environment variables per the `secrets` Rule), and the bridge base URL
from `PROJECT-CONTEXT.md` and runtime inspection. Ask only when the target
page or an overwrite decision remains ambiguous.

## Mandatory guidance

Before any write, read and apply:

- `PROJECT-CONTEXT.md`
- the installed `figma-design-to-code` skill
- the bundled `wst-section-workflow` Skill
- the `wordpress-content-editing` Rule and the `content` Skill
- the `file-edit-boundary` and `webroot-safety` Rules
- the relevant Section/CPT work records in the project `docs/` layer for
  every reused surface

If CSS work is required, also read the bundled `frontend-section-qa` or
`cpt-frontend-qa` Skill and the `css-guideline`/`figma-to-code` Rules. If
ACF definitions must change, the `acf-local-json` Rule applies: JSON files
are the only agent write path, structural ACF database writes are
forbidden entirely.

## Workflow

### 1. Preflight

1. Confirm the target is dev/staging per `PROJECT-CONTEXT.md`. Stop before
   writes on live or unknown environments.
2. Read the complete target page ACF Flexible Content value over REST.
3. Record:
   - page ID, URL, language, status
   - current ordered FC layouts
   - assigned brand/audience terms
   - global header, booking, menu, and footer behavior
4. When `PROJECT-CONTEXT.md` records a Confluence anchor, re-read the
   anchored PL page fresh over the Atlassian MCP and pull only the section
   relevant to this page — the matching task row, page notes, and the
   page's Figma link — into the run context (`confluence-source` Rule).
   No anchor, or no usable Atlassian MCP: skip cleanly, note
   `confluence-source: no anchor` (or `MCP unavailable`), and continue
   from the mirror. Never re-read mid-run; leaf agents receive the
   distilled extract in their prompt and never call Confluence.
5. Confirm git status. Do not commit unless the composition changes
   tracked source; never push (`deploy-and-branches` Rule).
6. Treat existing page-content replacement as authorized only when the user
   clearly asked to rebuild/populate that exact page. Otherwise ask once.

### 2. Parallel discovery

Launch both read-only agents in one parallel call, with models per the
`agent-routing` Rule.

#### Figma agent deliverable

Return an ordered page-surface table:

- position and Figma node ID
- exact copy and labels in the page language
- image/icon asset references
- interaction and responsive states
- global element vs page FC Section
- likely existing WST layout or WPGB surface
- unresolved editorial or link decisions

The Figma agent writes its detailed spec only to repo-level `tmp/`
(outside the deploy path, per `webroot-safety`).

#### Codebase agent deliverable (the `explore` leaf)

Page composition needs a broad, light inventory of the current install, not
the CPT-package analysis of `cpt-codebase-analyst` — hence the generic
`explore` leaf from the agent list above.

Return:

- current page FC row inventory
- reusable WST layouts and variants
- closest populated page/fixture row for each surface
- matching WPGB grid/card/facet IDs verified from the current install
  (status bridge readback or work records)
- matching Media Library IDs
- exact field names and accepted value shapes
- missing structural capabilities

Never infer IDs from Figma or another project.

### 3. Produce the binding page map

Create one ordered map before writing:

```text
Position | Figma node | Surface | Existing implementation | Source row/object
         | Action (keep/repurpose/clone/add/remove) | Content source
         | Link/media status | Owner
```

Classify every Figma surface:

- `global` — header, booking UI, menu, footer; never duplicate in FC.
- `existing-wst` — reuse an existing Section layout/variant.
- `existing-wpgb` — reuse a verified grid/card/facet object through the
  project's existing grid Section.
- `content-only` — ordinary copy/media/button population.
- `structural-gap` — the current project cannot represent the design.

Default to the smallest compatible existing implementation. Do not create a
new Section merely because the Figma component has a new name.

For `structural-gap`, run `wst-section-workflow` and route the write to
`wst-shortcode-implementer`. For visual-only gaps, route to the bundled
frontend QA Skill. Do not hide structural gaps inside page-population
writes.

### 4. Resolve blockers

Proceed with safe defaults for content that can be derived exactly.

Stop and ask only for decisions that materially affect the result:

- destination page does not exist
- Figma contains placeholder copy intended for publication
- multiple incompatible real WPGB/content sources exist
- required media is absent and import approval is needed (route imports
  through the bundled `wp-media-import` Skill)
- an existing page row would be destructively removed without clear scope
- a structural gap requires new Section/ACF/template artifacts

Never invent final copy, URLs, post IDs, media IDs, grid IDs, or taxonomy
assignments. Preserve explicit placeholders and report them when the user
requested immediate population despite missing editorial input.

### 5. Prepare the content write

Content population follows the `content` Skill and the
`wordpress-content-editing` Rule: REST round-trip of the complete
Flexible Content array, mandatory pre-write backup, UTF-8 JSON.

Before apply:

1. Snapshot the target page's complete FC value to a restore-capable
   backup under repo-level `tmp/` (never inside the deploy path).
2. Print a dry-run summary: old order, new order, reused rows, new rows,
   removed rows, links, media IDs, and grids.
3. Preserve each complete row and mutate only intended fields.
4. Clone proven rows from the current install when adding layouts; do not
   synthesize unknown ACF shapes.
5. Use attachment IDs and internal page IDs for ACF values where the
   project expects them.
6. Keep `acf_fc_layout`, ACFE metadata, layout settings, conditionals,
   animation flags, and nested structures unless the page map changes them.
7. Write the complete FC array back in one REST update; never patch
   fragments of the array.
8. Re-read the current FC value immediately before the write and refuse to
   apply if its layout fingerprint differs from the preflight fingerprint.

Confirm the dry-run summary against the binding page map before applying.

### 6. Populate in design order

Apply the binding page map exactly:

- Keep or repurpose matching rows.
- Clone verified source rows for additional layouts.
- Reorder the complete array to Figma's vertical order.
- Populate exact Figma copy.
- Connect existing real destination pages and media only when verified.
- Let dynamic grids pull real posts; do not overwrite CPT records merely to
  mimic Figma sample cards.
- Preserve brand/audience fallback unless the design and project taxonomy
  contract require an assignment.

Flush caches once after the successful write through the status bridge
(`status-bridge` Rule).

### 7. Structural acceptance

Verify both ACF and fresh rendered HTML:

- expected FC row count and exact ordered layout list
- expected headings and CTAs
- expected Section classes
- expected WPGB grid/card/facet markers
- expected media IDs/URLs
- no raw `[wst_*]` tokens
- HTTP 200
- global elements occur once in their intended theme location
- no unintended brand/audience term change

If the remote read is stale, flush through the status bridge, use a
cache-busting query parameter, and confirm served markers before
diagnosing the content write.

### 8. Visual acceptance

If browser QA is needed, claim exactly one Playwright server per the
`playwright-browser-claim` Rule, use only that server, and always release
its lock.

For composition-only work:

- smoke-check desktop and mobile for order, missing media, overflow, and
  interactive controls.

For any CSS or visual implementation:

- route through the bundled `frontend-section-qa`/`cpt-frontend-qa` Skill
- check the full project viewport ladder from `PROJECT-CONTEXT.md`
- distinguish injection-proof from the bridge-verified served check
- never call a page visually final while new CSS is not served
  (`implementation pass, deployed verification pending` until the bridge
  confirms the deployed commit)

### 9. Closeout

Delete temporary artifacts under `tmp/` after success. Keep the restore
backup only until acceptance is complete, then delete it unless the user
requests it.

Report:

- target page and URL
- final ordered Section list
- reused vs added/repurposed rows
- WPGB objects and media used
- structural and rendered acceptance result
- visual verification level (proof mode)
- explicit editorial placeholders and follow-up owners

Update the affected work records in the project `docs/` layer when the
page composition changes their recorded status. Do not invent a new
record storage convention.

## Non-goals

- Do not edit `functions.php`.
- Do not edit `theme-functions.php` or MU plugins without exact user approval.
- Do not edit third-party plugins, WordPress core, uploads directly, or the
  WST plugin runtime.
- Do not perform unrelated CPT migrations.
- Do not create CSS to compensate for incorrect WPGB configuration.
- Do not claim pixel parity from content population alone.

## Trigger examples

- “Baue die Über-uns-Seite nach diesem Figma auf.”
- “Befülle die Karriere-Seite wie im Design.”
- “Setze diese Landingpage mit den vorhandenen Sections um.”
- “Übertrage den Figma-Seitenaufbau in die bestehende WordPress-Seite.”
