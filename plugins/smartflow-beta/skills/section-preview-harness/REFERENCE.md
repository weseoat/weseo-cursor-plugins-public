# Section Preview Pages - Technical Reference

Distilled from a full Section remodel pilot (2026-06: button harness replacement, intro tracer, breaker scaling proof, full intro remodel with 7 variant fixtures, migration to a catalog page). Every statement below was verified against a live WST install; the fixture export path has since been adapted from WP-CLI to the gated REST export route for the shell-less SmartFlow workspace. Keep project-specific page IDs, URLs, brand slugs, and Figma node IDs in `PROJECT-CONTEXT.md` or the project docs layer, not in this reference.

## 1. How WST templates get their data

WST Section templates are not plain PHP. They are a shortcode DSL (`[wst_acf]`, `[wst_include]`, `[wst_if]`, `{{smarttags}}`) parsed by `wst_do_shortcode()` (plugin `weseo-smart-template`, NOT registered through WordPress `add_shortcode`). All field access resolves through ACF (`have_rows()`, `get_sub_field()`) inside the Flexible Content loop.

Consequence: feeding a template fixed data does not require a template adapter. `acf_setup_meta($meta, $id, true)` registers virtual meta for an arbitrary (non-numeric IDs work) post ID; every ACF call inside the render resolves against it. The harness builds one virtual FC row:

```php
$row = array_merge(array('acf_fc_layout' => $fixture['layout']), $fixture['data']);
acf_setup_meta(array($fc_field['key'] => array($row)), $preview_id, true);
$html = wst_do_shortcode(sprintf(
    '[wst_acf_flexible_content field="flexible_content" id="%s"][wst_include template="%s" layout="%s"][/wst_acf_flexible_content]',
    $preview_id, $fixture['template'], $fixture['layout']
));
acf_reset_meta($preview_id);
```

This is the exact render path of a real page (including seamless clone resolution), just with one row and one template.

## 2. Expanded clone field names

WST layouts are built from chained seamless clones with `prefix_name=1`. A field `title` inside group "Content", cloned as `content` into "[TMPL] Intro", cloned as `intro` into the page FC layout, is stored and resolved as `intro_content_title`. Fixture `data` MUST use these expanded names.

Two ways to derive a layout's expanded structure in the SmartFlow workspace (no server shell):

- **From the PHP field-group sources.** In SmartFlow projects all field definitions live as code under `smart-template-builder/acf/field-groups/` (per the `acf-php-field-groups` Rule). Walk the clone chain in the PHP registrations: layout clone name + clone-group field names, prefixed per `prefix_name=1` level.
- **From an existing page row.** Fetch the export route for a page that already renders the layout (`GET /wp-json/wso-preview/v1/export/<section>?source_page=<id>`); the returned `data` keys are the expanded names as stored in postmeta (`flexible_content_{i}_{expanded_name}`).

## 3. Resolving the Flexible Content field

`acf_get_field('flexible_content')` by name is ambiguous — WST installs carry several fields with that name (page builder, flexblocks, multi-column). Resolve through the field group assigned to post type `page`:

```php
foreach (acf_get_field_groups(array('post_type' => 'page')) as $group) {
    foreach ((array) acf_get_fields($group) as $field) {
        if ($field['type'] === 'flexible_content' && $field['name'] === 'flexible_content') {
            return $field;
        }
    }
}
```

## 4. Query-lifecycle hardening (the 404/phantom-post trap)

A virtual rewrite route with no posts behaves like the blog index. Three hooks are mandatory:

| Hook | Fix | Without it |
|---|---|---|
| `parse_query` | `is_home = false` for preview requests | WST's `posts_page_template_redirect` 404s `is_home` when the post archive is disabled (`wst_post_disable_post_archive_page`) - valid previews return HTTP 404 with rendered body |
| `pre_get_posts` | `post__in = [0]`, `no_found_rows = true` | Main query loads latest posts; the first becomes global `$post`, and its title/thumbnail leak into the preview through `[wst_post_title]`/`[wst_post_thumbnail]` template fallbacks |
| `pre_handle_404` | return `true` for valid previews | WordPress generic 404 handling overrides the route |

Symptom checklist: "renders fine but HTTP 404" -> `parse_query` missing; "no-image variant shows a stranger's image" -> `pre_get_posts` missing.

## 5. Fixture export route mechanics

- The route reads row metas raw: `SELECT meta_key, meta_value WHERE post_id = %d AND meta_key LIKE 'flexible_content_{i}_%'`, strips the prefix. This exports exactly what renders (the page's source of truth), independent of ACF formatting.
- Repeaters: stored flat as `<name> = <count>` plus `<name>_<j>_<sub>` keys. Converted generically to nested arrays (detect numeric value + existing `<name>_0_` sibling keys; recurse for nested repeaters). Nested arrays are what `acf_setup_meta()` consumes for repeaters.
- Validation per row before returning: layout name AND expected variant value. A mismatch produces a `skipped` entry with the reason, never a silently wrong fixture. Treat any non-empty `skipped` list as a stop: fix the source rows or the config first.
- Label/body_class/design references live in the exporter config (tracked source); the route generates the description (source page, row, date, re-export request) — fixtures stay self-documenting.
- The route never writes server files. The agent writes the returned fixtures into the repository; the deploy delivers them. A fixture the export returned but that is not yet deployed does not render — check `deployed_commit` before diagnosing a 404.

## 6. Writing FC content programmatically (test page prefill, migration)

The SmartFlow workspace has no server shell, so content writes happen in the admin by the user (with an exact row plan prepared by the agent) unless the project documents an approved programmatic route. The mechanics below apply when such a route exists, and they explain what the export route reads:

- `update_field('<fc field key>', $rows, $post_id)` with rows keyed by **expanded names** writes the exact native meta shape of a real page and keeps the ACFE performance blob consistent (see 7).
- Limitation: doubly-nested clone fields (for example `image_img_align_alternative_img_align` chains) are silently ignored by `update_field` - omit them and let ACF defaults apply at render time.
- Append-only migration pattern for a big existing page (proven on a 50-row page):
  1. Full postmeta snapshot to a restore-capable file under the repository `tmp/` (never inside the deploy path).
  2. Load existing rows raw (`get_field($name, $id, false)` - rows keyed by field keys), splice in the new rows (expanded-name keys mix fine), `update_field` once.
  3. Verify: meta diff vs snapshot (classify changes; zero unexpected keys), rendered section inventory before/after (counts per section type identical except the target), variant order, browser pre-interaction heights on the cached page.
  4. Fidelity proof: re-export fixtures from the migrated page over the export route - `data` blocks must be byte-identical.
- **Stale-meta caveat:** ACF's FC rewrite does not delete foreign native metas at reused row indexes (leftovers from previous occupants). The ACFE blob is clean (rendering correct), but native-meta readers (the export route!) see the leftovers. After index-shifting rewrites, diff native metas per row against the expected field set and have the strays cleaned up before trusting a re-export.

## 7. ACFE performance mode ("hybrid")

- Values are stored natively AND in a compiled `_acf` blob per post; reads prefer the blob, fall back to native.
- `update_field()` keeps both consistent. Direct `$wpdb`/`update_post_meta` writes do NOT update the blob - avoid them for content, or accept that the blob wins on read.
- Field group *definitions* are a separate surface entirely: in SmartFlow projects they are PHP registrations per the `acf-php-field-groups` Rule, and structural ACF database writes (creating, editing, reordering, or deleting `acf-field`/`acf-field-group` posts) are forbidden without exception. Definition changes are file edits plus a bridge-verified deploy.

## 8. WST output formatting (autop)

- `[wst_acf]` on a filled WYSIWYG field applies ACF formatting incl. `wpautop` -> `<p>` wrappers inside headings/sublines break markup contracts (browser error-recovery even re-parents text out of `<p class="wso-subline"><p>...`).
- Fix per template: `[wst_acf field='...' format_value='0']` renders the raw value (no autop). Editor paragraph breaks collapse; `<br>` (Shift+Enter) survives - fine for headlines.
- `[wst_acf_wysiwyg]` returns EMPTY inside FC loop context (both DB and local-meta) - do not use it for FC sub-fields.
- Treat installation-wide autop cleanup as a separate work package: existing sections may have CSS built around the broken markup.

## 9. QA verification pitfalls

- HTML minifiers strip comments from the served output - never anchor structural checks on `<!-- ... -->` markers; use real tags (`</section>`) or attribute anchors (`data-preview-variant`).
- Global template instances can render a SECOND copy of a section outside `#primary` (for example reference-popup clones). Scope every check to the target section's chunk.
- Previews are nocache: page-cache staleness and Delay-JS effects (for example `--vh` set only after first interaction) only reproduce on real cached pages. Full-page QA must include pre-interaction measurements on a fresh load.
- Lazy regex quantifiers across megabyte-sized HTML can blow the PCRE backtrack limit and silently return no-match - prefer `strpos`-windowing before regex.

## 10. Per-environment setup

- `POST /wp-json/wso/v1/flush-permalinks` (status bridge) once after the deploy that first delivers the harness to an environment.
- Host gate defaults to logged-in OR `*.weseo.dev`; adjust per hosting convention. The export route is gated on `manage_options` and authenticated like the bridge.
- Adding a new section variant = adding one fixture JSON in the repository (+ export config row) and deploying it. No rewrite flush (proven with a second section: it rendered on first request from JSON only).
- Theme coupling: the preview-page template assumes an Astra child theme (`astra_primary_*` calls). On a different theme, swap those for the active theme's primary-content wrapper and keep the `#primary` + `data-preview-*` QA hooks.
