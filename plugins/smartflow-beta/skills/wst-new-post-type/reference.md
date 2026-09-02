# WST New Post Type Reference

Reusable lookup material for the `wst-new-post-type` Skill. Values shown with angle brackets come from `PROJECT-CONTEXT.md` or the current brief.

This reference is intentionally generic. Project-specific CPT names, rewrite slugs, field keys, WPGB IDs, URLs, paths, selectors, credentials, and theme tokens stay in project context or the concrete CPT work record.

## Safe defaults

Use these defaults only as starting points during the preflight:

- Prefer a non-detail CPT unless the brief explicitly needs public single pages.
- There is no default content model. When detail pages exist or the design shows page Sections around the CPT content, the maintainer picks `typed-only`, `flexible-content-only`, or `hybrid` from the analysts' evidence (see below); the only value the Skill records on its own is `not-applicable` for CPTs without detail pages and without page Sections.
- Prefer core post title, featured image, editor, excerpt, and taxonomy terms before duplicating data in ACF fields.
- Add taxonomy only when it has a clear filtering, grouping, card-label, archive, or editor-organization purpose.
- Prepare WP Grid Builder card/grid apply-specs only when the display target uses WPGB.
- Keep generated IDs and field keys in `PROJECT-CONTEXT.md` or the CPT work record, never in reusable plugin docs.
- Route completed CPT work to `cpt-frontend-qa`; if a dedicated WST Section becomes the main display surface, record the split with `frontend-section-qa`.

## Work type defaults

| Work type | Default behavior |
| --- | --- |
| `new-cpt-foundation` | Create or prepare only the artifacts explicitly needed by the work record: registration apply-spec, optional taxonomy apply-spec, optional ACF JSON group, optional WPGB apply-spec, card template, optional archive/grid integration, optional single template, and CSS hook documentation. |
| `existing-cpt-remodel` | Preserve existing structure by default. No new registration, taxonomy, field group, WPGB, template, CSS, or style loader artifacts unless the confirmed work record requires them. |
| `visual-only` | No structural work. Route to `cpt-frontend-qa`, with `frontend-section-qa` noted when the visible change is Section-level. |
| `unclear` | Stop and ask before any write or read-only audit beyond project context. |

## Content models

The `Content model` decides whether a CPT with detail pages carries page-builder Sections on the post. It is asked verbatim in the preflight (question 3 in the Skill) and answered by the maintainer, never defaulted:

| Content model | ACF group | Single partial | Smart Template include | Use when |
| --- | --- | --- | --- | --- |
| `typed-only` | typed `wso_<resource>_*` fields only | yes, renders everything | `post-types/<resource>/singles/<resource>-single.php` | The design is a closed, structured detail with no page Sections around it. |
| `flexible-content-only` | clone of `[TMPL] Flexible Inhalte` (plus the Intro clone where the project has it); no typed detail fields | none | `flexible-content.php` | Editors compose each detail from the same Sections as pages — the reference-CPT pattern. |
| `hybrid` | typed fields **and** the `[TMPL] Flexible Inhalte` clone | yes, typed core, then the FC include as a sibling | `post-types/<resource>/singles/<resource>-single.php` | A structured core (hero, meta, columns) plus shared page Sections (Benefits, grid, form) on the same detail. |
| `not-applicable` | typed fields as needed | none | none | No detail pages and no page Sections around the CPT content. |

Evidence the decision is made on (delivered by the analysts, see the Skill's preflight):

- The reference CPT's content model: does its ACF group carry the `[TMPL] Flexible Inhalte` clone? Clone field name and clone group key as read from this install.
- The ordered detail-page walk from the design: each segment with a `content-role` of `typed-core`, `page-section` (matched to an existing Flexible Content layout by name when possible), `global`, or `unresolved`.
- Whether typed and page-section segments alternate (design-order caveat).

The Smart Template's own Flexible Content stays empty in every model; the Smart Template is only the top-level code-editor include. Page Sections live in the clone on the CPT post.

### Flexible Content clone field (hybrid, flexible-content-only)

Added to the CPT's own JSON group, after the typed fields (or as the only content field for `flexible-content-only`):

```json
{
    "key": "field_<unique>_<resource>_content",
    "label": "<content-label>",
    "name": "wso_<resource>_content",
    "type": "clone",
    "clone": [
        "<key-of-the-project-[TMPL]-Flexible-Inhalte-group>"
    ],
    "display": "seamless",
    "layout": "block",
    "prefix_label": 0,
    "prefix_name": 1
}
```

- The clone group key is read from this install (the reference CPT's clone field is the precedent), never copied from another project or from documentation.
- `prefix_name: 1` makes the cloned Flexible Content resolve as `wso_<resource>_content_<clone-field-name>` — the same shape as the reference CPT's clone. `flexible-content.php` resolves that prefix the same way it does for the reference CPT; no template change is needed for a new CPT.
- Fresh `field_` key, additive change. On an `existing-cpt-remodel` the clone is added next to the saved fields; already-saved unprefixed field names stay as they are (renaming them is a data migration with an explicit user decision).

### Render order (hybrid)

```php
<main class="wso-<resource>-single">
  ... typed core ...
</main>
[wst_include template="flexible-content.php"]
```

The FC include is a sibling after the CPT wrapper, not nested inside it. One FC field has one insertion point: when the design sandwiches typed blocks and page Sections (Hero → Benefits → typed columns → Form → related grid), record the limitation in the work record — the default is typed core first, then the FC output. No second FC field and no split of the typed template unless the maintainer explicitly asks.

## Apply-spec discipline

Admin-managed objects (CPT UI registrations, taxonomies, WPGB grids/cards) are changed by the user from an exact apply-spec, never from a vague instruction. An apply-spec names every setting and its value, marks deviations from the existing baseline, and states the verification that follows (permalink flush over the bridge, REST/type check, `GET /status` readback). Example framing:

```text
CPT UI apply-spec: wso_<resource>
Settings: <the full settings block below, values filled>
Deviations from baseline CPT <reference-cpt>: <list>
After applying: I flush permalinks over the bridge and verify
/wp-json/wp/v2/types plus the admin menu entry.
```

Content changes on the live site and replace-operations always get their own explicit confirmation inside the spec.

## CPT UI settings

For CPTs without public detail pages:

```json
{
  "name": "wso_<resource>",
  "label": "<plural-label>",
  "singular_label": "<singular-label>",
  "public": "true",
  "publicly_queryable": "false",
  "show_ui": "true",
  "show_in_rest": "true",
  "has_archive": "false",
  "exclude_from_search": "true",
  "rewrite": "false",
  "query_var": "false",
  "supports": ["title", "thumbnail"],
  "taxonomies": [],
  "menu_icon": "dashicons-<icon>"
}
```

Stop if the no-detail-page decision is unclear. Do not create public URLs accidentally just because the CPT is visible in the admin.

For CPTs with public detail pages, review these settings together:

```json
{
  "publicly_queryable": "true",
  "exclude_from_search": "false",
  "has_archive": "<true-or-false>",
  "rewrite": "true",
  "rewrite_slug": "<url-slug>",
  "query_var": "true",
  "supports": ["title", "thumbnail", "editor", "excerpt"]
}
```

Only enable detail pages after the work record captures the URL slug, archive/search behavior, expected single-template path, selectors, and frontend responsibilities.

For `existing-cpt-remodel`, preserve the registered post type, labels, supports, rewrite, archive, and search behavior unless the confirmed work record explicitly approves a change.

## Taxonomy settings

Follow the `wordpress-taxonomies` Rule: English `wso_tax_*` machine name, German labels allowed, CPT UI managed.

```json
{
  "name": "wso_tax_<resource>",
  "label": "<taxonomy-plural-label>",
  "singular_label": "<taxonomy-singular-label>",
  "public": "true",
  "publicly_queryable": "<true-or-false>",
  "hierarchical": "<true-or-false>",
  "show_ui": "true",
  "show_in_menu": "true",
  "show_admin_column": "true",
  "show_in_rest": "true",
  "rewrite": "false",
  "object_types": ["wso_<resource>"]
}
```

Set `hierarchical` to `true` for category-style terms and `false` for tag-style terms. Keep `rewrite` disabled unless taxonomy archives are a deliberate requirement. Record an explicit no-taxonomy decision when the CPT does not need one. List fixed terms in the apply-spec so the user creates them in the same admin visit.

For `existing-cpt-remodel`, preserve the existing taxonomy name, hierarchy, public archive behavior, and rewrite behavior unless the confirmed work record explicitly approves a structural change.

## ACF field group shape (JSON)

One JSON file per field group under `themes/<child-theme>/acf-json/`, named per the installation's filename convention from `PROJECT-CONTEXT.md`, formatted in the PHP `json_encode` style ACF writes itself (4-space indentation, `\/` escaping, `\uXXXX` for non-ASCII), per the `acf-local-json` Rule. The location rule targets the registered post type:

```json
{
    "key": "group_<unique>_<resource>_fields",
    "title": "<singular-label> Fields",
    "fields": [
        {
            "key": "field_<unique>_<resource>_tab",
            "label": "<singular-label>",
            "name": "",
            "type": "tab"
        },
        {
            "key": "field_<unique>_<resource>_<field>",
            "label": "<field-label>",
            "name": "<resource>_<field>",
            "type": "<field-type>"
        }
    ],
    "location": [
        [
            {
                "param": "post_type",
                "operator": "==",
                "value": "wso_<resource>"
            }
        ]
    ],
    "position": "normal",
    "style": "default",
    "label_placement": "top",
    "instruction_placement": "label",
    "active": true,
    "acfe_autosync": ["json"],
    "modified": <unix-timestamp>
}
```

Field group conventions:

- Every group and field carries a stable explicit fresh key; never reuse a key for a different field, never rename saved fields casually (data migration, explicit user decision).
- For `existing-cpt-remodel`, reuse the existing field group and field keys by default; add fields only when the approved work record requires them.
- Prefer core post fields before adding duplicate ACF fields.
- Field names always carry the `wso_<resource>_` prefix (`wso_job_salary` on a Job CPT, never `job_salary`); keep them stable — retrofitting the prefix onto already saved fields is a data migration requiring an explicit user decision.
- `acfe_autosync` must contain `"json"` and `modified` must exceed the database state, otherwise the admin offers no sync. The group stays editable in the admin; after deploy plus sync, `GET /status` lists it with `local: "json"`.

Common field types:

| Type | Use case |
| --- | --- |
| `text` | Short labels, price text, location text |
| `textarea` | Short descriptions that should not use rich formatting |
| `wysiwyg` | Rich content managed by editors |
| `url` | External links |
| `email` | Contact email addresses |
| `file` | Downloads or media assets |
| `image` | Additional images beyond featured image |
| `select` | Controlled choices |
| `true_false` | Toggles |
| `link` | Internal or external link object |
| `page_link` | Internal page reference |
| `relationship` | Related posts |
| `date_time_picker` | Event or availability dates |

## WP Grid Builder foundation

WST projects commonly use WP Grid Builder for CPT card lists:

- An empty WPGB card exists so the grid can select it; WST PHP card templates render the markup.
- The WPGB grid source is the new CPT; the created card is selected in the grid settings.
- Read existing grids over `GET /status` to pick the closest baseline; the apply-spec carries the minimal diff from that baseline.
- Record generated `<grid-id>` and `<card-id>` in `PROJECT-CONTEXT.md` or the work record after the user created the objects.
- Record an explicit no-WPGB decision when the CPT is rendered only by a custom Section, single template, or other project-specific path.

For `existing-cpt-remodel`, preserve existing WPGB grid/card IDs, grid source, selected card, filters, pagination, and carousel behavior unless the confirmed work record explicitly approves a change.

CPT card and single templates live in the repository child theme:

```text
themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php
themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card-part-1.php
themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card-part-2.php
themes/<child-theme>/smart-template-builder/post-types/<resource>/singles/<resource>-single.php
```

Do not place them under `plugins/weseo-smart-template-builder/` (WST runtime/library; off-limits without a recorded project-source exception). Do not hardcode WPGB IDs in card templates, reusable references, or plugin docs.

## Smart Template single assignment (apply-spec for the user)

CPT detail pages render only through a Smart Template assignment — the WordPress template hierarchy never finds the single partial (WST projects have no `single.php`/`page.php` in the child theme). The agent authors the partial and the include one-liner; the admin steps are the user's. Apply-spec shape:

```text
Smart Template apply-spec: single view for wso_<resource>
1. Create a Smart Template (post type smart_template), e.g. title
   "CPT - <singular-label>", category "Post Types".
2. Assign it to the CPT: Smart Template → Settings → Post Types →
   <CPT> → Template Assignment.
3. Enable the top-level code editor in that Smart Template and paste
   exactly this include (Flexible Content field stays empty; the include
   goes in the top-level code editor, not in a code-editor Section):

   [wst_include template="post-types/<resource>/singles/<resource>-single.php"]

   (Content model flexible-content-only: the one include is
   [wst_include template="flexible-content.php"] instead.)

After applying: I verify the single URL renders the partial
(.wso-<resource>-single present in the served markup; for hybrid also
the Flexible Content output after the wrapper).
```

"Flexible Content stays empty" refers to the Smart Template post. For `hybrid` and `flexible-content-only` the page Sections are entered on the CPT post in its `wso_<resource>_content` clone and rendered by `flexible-content.php` from the include.

Verification notes:

- `smart_template` posts are not REST-exposed (`show_in_rest = false`). Read-verify the assignment over XML-RPC (`wp.getPost` with the Application Password); the relevant metas are `smart_template_code_editor_enabled` and `smart_template_code_editor_content`.
- Record the Smart Template post ID and assignment status in the work record and `PROJECT-CONTEXT.md` once applied.
- Until the assignment exists, the single URL shows the generic theme output — a pending admin step, not a code bug.

Shortcode caution inside the single partial (include context): generic `[wst_acf field='<field-name>']` resolves empty there (ACF strict resolution over `get_field_object()`). Use typed shortcodes (`[wst_acf_text]`, `[wst_acf_wysiwyg]`, …) or the `field_…` key. `[wst_post_*]` shortcodes and `[wst_if]` conditionals work normally.

## Protected existing artifacts

Fill this during the `existing-cpt-remodel` preflight before any write:

| Artifact | Preserve unless explicitly approved |
| --- | --- |
| CPT registration | Registered post type, labels, supports, rewrite, archive, search behavior, REST/admin visibility. |
| Taxonomy | Taxonomy name, hierarchy, object type binding, public archive behavior, rewrite behavior. |
| ACF | Field group file, field names, field keys, location rules, field ownership. |
| Content model | The existing `typed-only` / `flexible-content-only` / `hybrid` shape and the FC clone field when present. A typed-only CPT whose design shows page Sections is a flagged gap with a `hybrid` proposal, not a hardcoded include. |
| WPGB | Grid ID, card ID, source settings, selected card, filters, pagination, carousel settings. |
| Templates | Card, archive/grid, carousel/filter, Section integration, and optional single template paths. |
| Selectors | Card, archive/grid, filter, carousel, single, and integration selectors used by templates, JS, WPGB behavior, CSS, or tests. |
| CSS | Existing CSS path, style loader entry, and `CSS status`. |

If the desired change fits inside an existing template without changing registration, ACF, WPGB, or paths, limit the scope to that template. If the desired change is visual-only, route to `cpt-frontend-qa`.

## WST card shortcodes

Common card data (catalog entry: the bundled `wst-shortcodes` Skill; every form new to the project needs the four-source proof):

| Shortcode | Purpose |
| --- | --- |
| `[wst_post_title]` | Current post title |
| `[wst_post_thumbnail size='<image-size>']` | Featured image |
| `[wst_post_excerpt words='<count>' fallback='1' html='0']` | Excerpt with optional content fallback |
| `[wst_post_permalink url='1']` | Current post permalink URL |
| `[wst_post_terms taxonomy='<taxonomy-name>']...[/wst_post_terms]` | Term loop |
| `[wst_acf field='<field-name>']` | ACF scalar field |
| `[wst_acf_image field='<field-name>' size='<image-size>']` | ACF image field |
| `[wst_acf_file field='<field-name>' url='1']` | ACF file URL |
| `[wst_acf_link field='<field-name>' class='<class-name>']` | ACF link field |
| `[wst_if field='<field-name>' compare='!=' value='']...[/wst_if]` | Conditional output (nesting per the `wst-conditional-nesting` Rule) |
| `[wst_if_a field='<field-a>' ... field_2='<field-b>' ... relation='AND']...[/wst_if_a]` | Multi-field conditional output |
| `[wst_variable name='<name>']...[/wst_variable]` | Define reusable output |

WPGB placeholder caution: `{{post.id}}` vs `{{post_id}}` in card context is an open validation (see `wst-shortcodes`); prove the resolving spelling on the project before relying on it.

Single-partial caution: inside the Smart Template include context (CPT single partials), generic `[wst_acf field='…']` resolves empty — use typed shortcodes or the `field_…` key; see the Smart Template single assignment section above.

## CPT work record skeleton

The CPT work record lives in the project docs layer (default `docs/post-types/<resource>.md`) and is separate from Section work records. Sections to maintain:

```text
# CPT: <singular-label> (wso_<resource>)

## Status
Work type / preflight gate status / deploy state (commit, bridge verification) / frontend route

## Identity and decisions
Registered post type, labels, admin visibility, detail-page decision (URL slug or explicit no),
Content model: typed-only | flexible-content-only | hybrid | not-applicable (with the analysts'
segment evidence, FC clone field name + clone group key, design-order limitation if any),
archive/search behavior, taxonomy decision (wso_tax_* or explicit no), display target

## Apply-specs
CPT UI settings (applied yes/no), taxonomy settings + fixed terms, WPGB spec + generated IDs,
Smart Template single assignment (post ID, applied yes/no) when detail pages exist

## ACF
ACF JSON group file, field names, FC clone field (hybrid / flexible-content-only), unresolved field questions

## Templates
Card / archive-grid integration / optional single (with the flexible-content.php include for hybrid) /
optional Section integration paths

## Selectors and CSS
Card, archive/grid, single, integration selectors; selectors to preserve; CSS path; CSS status

## Expected behavior
Desktop, tablet, mobile, content variation, filtering, linking, interaction

## Protected existing artifacts   (remodels)

## QA notes / known risks / open questions
```

Stop instead of guessing when the record lacks blocking values such as CPT names, rewrite slugs, the content model, taxonomy decisions, field ownership, WPGB IDs, target URLs, stable selectors, template paths, or expected visible behavior.
