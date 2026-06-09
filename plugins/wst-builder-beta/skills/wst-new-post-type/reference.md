# WST New Post Type Reference

Reusable lookup material for the `wst-new-post-type` Skill. Values shown with angle brackets come from Project Context or the current issue brief.

This reference is intentionally generic. Project-specific CPT names, rewrite slugs, ACF keys, field post IDs, WP Grid Builder IDs, URLs, paths, selectors, credentials, and theme tokens must stay in Project Context or the concrete CPT handoff.

This reference is shared across all CPT work types: `new-cpt-foundation`, `existing-cpt-remodel`, and the routing decisions for `visual-only`. The Skill itself decides which parts of this reference apply to a given request.

## Safe Defaults

Use these defaults only as starting points for discussion during the bundled `grill-me` preflight:

- Prefer a non-detail CPT unless the brief explicitly needs public single pages.
- Prefer core post title, featured image, editor, excerpt, and taxonomy terms before duplicating data in ACF fields.
- Add taxonomy only when it has a clear filtering, grouping, card-label, archive, or editor-organization purpose.
- Create WP Grid Builder card/grid foundations only when the display target uses WPGB.
- Keep generated IDs and field keys in Project Context or the CPT handoff, never in reusable plugin docs.
- Route completed CPT handoffs to `cpt-frontend-qa`; if a dedicated WST Section becomes the main display surface, record the split with `frontend-section-qa`.

## Work Type Defaults

Use these workflow defaults before applying any CPT-specific implementation guidance:

| Work type | Default behavior |
| --- | --- |
| `new-cpt-foundation` | Create only the artifacts explicitly needed by the handoff: CPT registration, optional taxonomy, optional ACF field group, optional WPGB grid/card, card template, optional archive/grid integration, optional single template, and CSS hook documentation. |
| `existing-cpt-remodel` | Preserve existing structure by default. Do not create new registration, taxonomy, ACF, WPGB, template, CSS, or style loader artifacts unless the confirmed handoff requires them. |
| `visual-only` | Do not write server-side artifacts. Route to Frontend Design QA `cpt-frontend-qa`, with `frontend-section-qa` noted when the visible change is Section-level. |
| `unclear` | Stop and ask before any write or read-only audit beyond Project Context. |

## Live And Unknown Environment Confirmation

For any change in a `live` or `unknown` environment, the prompt to the maintainer must be concrete. Generic example:

```text
Work type: existing-cpt-remodel
Environment: live
Files to change: wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php
ACF/DB objects to change: none
WPGB objects to change: none
Content changes: none
Cache action: wp cache flush after template change
Rollback path: Git revert on branch <branch>

May I perform exactly these changes on <environment>?
```

Cache flush on `live` or `unknown` requires its own confirmation even when other server writes were already approved.

## CPT UI Settings

Create or change CPT UI settings only when the approved `Server write scope` includes the CPT registration. For `existing-cpt-remodel`, preserve the registered post type, labels, supports, rewrite, archive, and search behavior unless the confirmed handoff explicitly approves a change.

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

Only enable detail pages after the handoff records the URL slug, archive/search behavior, expected single-template path, selectors, and local frontend responsibilities.

## Taxonomy Settings

Use a taxonomy when the CPT needs filtering, grouping, labels, or editor organization.

For `existing-cpt-remodel`, preserve the existing taxonomy name, hierarchy, public archive behavior, and rewrite behavior unless the confirmed handoff explicitly approves a structural taxonomy change.

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

Set `hierarchical` to `true` for category-style terms and `false` for tag-style terms. Keep `rewrite` disabled unless taxonomy archives are a deliberate requirement.

Record an explicit no-taxonomy decision when the CPT does not need taxonomy. If public taxonomy archives are required, the handoff must capture rewrite slug, archive behavior, selectors, and frontend QA expectations.

## ACF Field Group Shape

Create one field group for CPT-specific fields, with a location rule targeting the registered post type.

For `existing-cpt-remodel`, reuse the existing field group and field keys by default. Create new fields only when the approved `Server write scope` and handoff say the content model requires them.

```php
$group_id = wp_insert_post([
    'post_type'    => 'acf-field-group',
    'post_title'   => '<singular-label> Fields',
    'post_name'    => 'group_<resource>_fields',
    'post_status'  => 'publish',
    'post_content' => serialize([
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'wso_<resource>'],
        ]],
        'position' => 'normal',
        'style'    => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ]),
]);
```

Field group conventions:

- Start with a tab field when the project uses tabs for editor organization.
- Prefer core post title, thumbnail, editor, excerpt, and taxonomy fields before adding duplicate ACF fields.
- Keep ACF field names stable and prefixed consistently with the CPT.
- Record generated field keys in Project Context or the handoff when later work needs them.
- Stop when field ownership is unclear; do not invent field names, field keys, or generated IDs.

Common field types:

| Type | Use Case |
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

## WP Grid Builder Foundation

WST projects commonly use WP Grid Builder for CPT card lists:

- Create an empty WPGB card when WST PHP templates render the markup.
- Create a WPGB grid whose source is the new CPT when the display target is a grid or carousel.
- Select the created card in the grid settings.
- Record generated `<grid-id>` and `<card-id>` in Project Context or the handoff.
- Record an explicit no-WPGB decision when the CPT is rendered only by a custom Section, single template, or other project-specific path.

For `existing-cpt-remodel`, preserve existing WPGB grid/card IDs, grid source, selected card, filters, pagination, and carousel behavior unless the confirmed handoff explicitly approves a change.

CPT card and single templates live in the project-owned WST source inside the active child theme. The theme-relative lookup shapes are:

```text
smart-template-builder/post-types/<resource>/cards/<resource>-card.php
smart-template-builder/post-types/<resource>/cards/<resource>-card-part-1.php
smart-template-builder/post-types/<resource>/cards/<resource>-card-part-2.php
smart-template-builder/post-types/<resource>/singles/<resource>-single.php
```

Resolved against the active child theme that means:

```text
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card.php
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card-part-1.php
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/<resource>-card-part-2.php
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/singles/<resource>-single.php
```

Do not place CPT card, archive/grid, or single templates under `wp-content/plugins/weseo-smart-template-builder/`. That folder is the WST runtime/library and is off-limits unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath. Use the equivalent theme-internal paths from Project Context when a project differs.

Do not hardcode WPGB IDs in card templates, reusable references, or plugin docs. Generated IDs belong in concrete project context and handoffs.

## Protected Existing Artifacts

Fill this during `existing-cpt-remodel` preflight before any write:

| Artifact | Preserve unless explicitly approved |
| --- | --- |
| CPT registration | Registered post type, labels, supports, rewrite, archive, search behavior, REST/admin visibility. |
| Taxonomy | Taxonomy name, hierarchy, object type binding, public archive behavior, rewrite behavior. |
| ACF | Field group, field names, field keys, location rules, field ownership. |
| WPGB | Grid ID, card ID, source settings, selected card, filters, pagination, carousel settings. |
| Templates | Card, archive/grid, carousel/filter, Section integration, and optional single template paths. |
| Selectors | Card, archive/grid, filter, carousel, single, and integration selectors used by templates, JS, WPGB behavior, CSS, or tests. |
| CSS | Existing CSS path, style loader entry, and `CSS status`. |

If the desired change can be made inside an existing template without changing registration, ACF, WPGB, or paths, limit the write scope to that template. If the desired change is visual-only, route to `cpt-frontend-qa`.

## CSS Hooks Belong In The Handoff

Server-side WST Builder records CSS hook expectations in the CPT handoff. It does not create or edit final CPT CSS or SCSS over Remote-SSH.

Record:

- Card selector, usually `.wso-<resource>-card`.
- Archive/grid wrapper selector.
- Carousel or filter selectors when relevant.
- Single selector, usually `.wso-<resource>-single` when detail pages exist.
- Section integration selector when a dedicated Section renders the CPT display.
- CSS file path for local frontend work.
- Whether the project requires a new style loader registration during local frontend work.
- `CSS status` from `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

Final spacing, typography, responsive behavior, Chrome Local Overrides spikes, local Playwright MCP browser QA, and optional project-local Playwright regression checks belong to Frontend Design QA. New CSS files and new style loader entries are created or registered by `cpt-frontend-qa` in tracked local source files, not by this Skill over Remote-SSH.

## WST Card Shortcodes

Common card data:

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
| `[wst_if field='<field-name>' compare='!=' value='']...[/wst_if]` | Conditional output |
| `[wst_if_a field='<field-a>' ... field_2='<field-b>' ... relation='AND']...[/wst_if_a]` | Multi-field conditional output |
| `[wst_variable name='<name>']...[/wst_variable]` | Define reusable output |

## CPT Handoff Draft

The CPT handoff draft is separate from the Section handoff template. Create it from the bundled reusable template at `plugins/wst-builder/handoffs/cpt-handoff.template.md` during the bundled `grill-me` preflight, store the filled draft at the project-configured CPT handoff storage location from Project Context, then keep updating it as server-side CPT foundation work proceeds.

The draft should capture:

- Handoff carrier, owner, project-configured storage location, and source brief.
- `Work type`, `Environment`, `Server write scope`, `Frontend route`, `Preflight gate status`, `Protected existing artifacts`, and `CSS status`.
- CPT slug, registered name, singular label, plural label, and admin visibility.
- Detail-page decision, URL slug or explicit "no public detail page" decision, archive/search behavior, and unresolved detail-page questions.
- Taxonomy decision, taxonomy name, labels, hierarchy, public archive decision, purpose, and unresolved taxonomy questions.
- ACF field group, field names, and unresolved field questions.
- WPGB grid/card IDs as project-local values, or an explicit no-WPGB decision.
- Display target: grid, carousel, existing Section, dedicated Section, card-only, or single-only.
- Card template files, archive/grid integration, optional single template files, and optional Section integration files.
- Expected selectors for card, archive/grid, optional single template, and Section integration.
- Expected desktop, tablet, mobile, content variation, filtering, linking, and interaction behavior.
- Server verification results and cache state.
- Known risks, open questions, unresolved placeholders, and stop conditions.
- Local frontend checklist for CSS/SCSS, responsive checks, Chrome Local Overrides spikes, local Playwright MCP browser QA, and optional project-local Playwright regression verification.

Stop instead of guessing when the draft lacks blocking values such as CPT names, rewrite slugs, taxonomy decisions, ACF field ownership, WPGB IDs, target URLs, stable selectors, template paths, storage location, or expected visible behavior.

Completed CPT handoffs route to the Frontend Design QA `cpt-frontend-qa` Skill. When the CPT display becomes primarily a dedicated WST Section layout, keep the CPT handoff explicit about the split: `frontend-section-qa` owns the Section layout behavior, while `cpt-frontend-qa` owns CPT card, archive/grid, and optional single-template behavior.
