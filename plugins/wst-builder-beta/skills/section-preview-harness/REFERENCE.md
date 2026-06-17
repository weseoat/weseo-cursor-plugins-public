# Section Preview Pages - Technical Reference

Distilled from a full Section remodel pilot (2026-06: button harness replacement, intro tracer, breaker scaling proof, full intro remodel with 7 variant fixtures, migration to a catalog page). Every statement below was verified against a live WST install. Keep project-specific page IDs, URLs, brand slugs, and Figma node IDs in `PROJECT-CONTEXT.md` or the active handoff, not in this reference.

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

Dump a layout's expanded structure:

```sh
php wp-cli.phar eval "foreach (acf_get_field('flexible_content')['layouts'] as \$l) { if (\$l['name'] === 'layout_intro') { print_r(wp_list_pluck(\$l['sub_fields'], 'type', 'name')); } }"
```

(Resolve the FC field properly first - see next section. Alternatively read an existing page's postmeta: `flexible_content_{i}_{expanded_name}`.)

## 3. Resolving the Flexible Content field

`acf_get_field('flexible_content')` by name is ambiguous - WST installs carry several fields with that name (page builder, flexblocks, multi-column). Resolve through the field group assigned to post type `page`:

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

## 5. Fixture exporter mechanics

- Read row metas raw: `SELECT meta_key, meta_value WHERE post_id = %d AND meta_key LIKE 'flexible_content_{i}_%'`, strip the prefix. This exports exactly what renders (the page's source of truth), independent of ACF formatting.
- Repeaters: stored flat as `<name> = <count>` plus `<name>_<j>_<sub>` keys. Convert generically to nested arrays (detect numeric value + existing `<name>_0_` sibling keys; recurse for nested repeaters). Nested arrays are what `acf_setup_meta()` consumes for repeaters.
- Validate per row before writing: layout name AND expected variant value. Wrong order = SKIP, never a silently wrong fixture.
- Keep label/body_class/design references in the exporter config, generate the description (source page, row, date, re-export command) - fixtures stay self-documenting.

## 6. Writing FC content programmatically (test page prefill, migration)

- `update_field('<fc field key>', $rows, $post_id)` with rows keyed by **expanded names** writes the exact native meta shape of a real page and keeps the ACFE performance blob consistent (see 7).
- Limitation: doubly-nested clone fields (for example `image_img_align_alternative_img_align` chains) are silently ignored by `update_field` - omit them and let ACF defaults apply at render time.
- Append-only migration pattern for a big existing page (proven on a 50-row page):
  1. Full postmeta snapshot to a file outside the webroot (restore-capable).
  2. Load existing rows raw (`get_field($name, $id, false)` - rows keyed by field keys), splice in the new rows (expanded-name keys mix fine), `update_field` once.
  3. Verify: meta diff vs snapshot (classify changes; zero unexpected keys), rendered section inventory before/after (counts per section type identical except the target), variant order, browser pre-interaction heights on the cached page.
  4. Fidelity proof: re-export fixtures from the migrated page - `data` blocks must be byte-identical.
- **Stale-meta caveat:** ACF's FC rewrite does not delete foreign native metas at reused row indexes (leftovers from previous occupants). The ACFE blob is clean (rendering correct), but native-meta readers (the exporter!) see the leftovers. After index-shifting rewrites, diff native metas per row against the expected field set and delete strays.

## 7. ACFE performance mode ("hybrid")

- Values are stored natively AND in a compiled `_acf` blob per post; reads prefer the blob, fall back to native.
- `update_field()` keeps both consistent. Direct `$wpdb`/`update_post_meta` writes do NOT update the blob - avoid them for content, or accept that the blob wins on read.
- Field GROUP *definitions* are a separate, higher-risk surface from content. On WST/ACFE clone groups, changing a definition is dangerous: never write back `acf_get_fields()` output (it is an expanded, read-only view onto shared source field posts), never `acf_update_field()` a composite or clone-expanded field, reorder only the real child `menu_order` column via `$wpdb`, and run a snapshot, dry-run, and clone-integrity scan first. See `acf-wst-patterns-reference.md` "ACF Field Definition Safety (Clone Sources)".

<!-- acf-safety-reviewed: field-value harness; definition writes routed to the clone-source-safe reference; no read-then-write-back idiom -->

## 8. WST output formatting (autop)

- `[wst_acf]` on a filled WYSIWYG field applies ACF formatting incl. `wpautop` -> `<p>` wrappers inside headings/sublines break markup contracts (browser error-recovery even re-parents text out of `<p class="wso-subline"><p>...`).
- Fix per template: `[wst_acf field='...' format_value='0']` renders the raw value (no autop). Editor paragraph breaks collapse; `<br>` (Shift+Enter) survives - fine for headlines.
- `[wst_acf_wysiwyg]` returns EMPTY inside FC loop context (both DB and local-meta) - do not use it for FC sub-fields.
- Treat installation-wide autop cleanup as a separate work package: existing sections may have CSS built around the broken markup.

## 9. QA verification pitfalls

- HTML minifiers strip comments from the served output - never anchor structural checks on `<!-- ... -->` markers; use real tags (`</section>`) or attribute anchors (`data-preview-variant`).
- Global template instances can render a SECOND copy of a section outside `#primary` (for example reference-popup clones). Scope every check to the target section's chunk.
- Previews are nocache: page-cache staleness and Delay-JS effects (for example `--vh` set only after first interaction) only reproduce on real cached pages. Full-page QA must include pre-interaction measurements on a fresh load.
- CLI PHP may have `allow_url_fopen=0` - use curl (binary or PHP ext) for QA fetches.
- Lazy regex quantifiers across megabyte-sized HTML can blow the PCRE backtrack limit and silently return no-match - prefer `strpos`-windowing before regex.

## 10. Per-environment setup

- `php wp-cli.phar rewrite flush` once after deploying the harness.
- Host gate defaults to logged-in OR `*.weseo.dev`; adjust per hosting convention.
- Adding a new section variant = adding one fixture JSON (+ export config row). No PHP, no rewrite flush (proven with a second section: it rendered on first request from JSON only).
- Theme coupling: the preview-page template assumes an Astra child theme (`astra_primary_*` calls). On a different theme, swap those for the active theme's primary-content wrapper and keep the `#primary` + `data-preview-*` QA hooks.
