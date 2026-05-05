# WST New Post Type Reference

Reusable lookup material for the `wst-new-post-type` Skill. Values shown with angle brackets come from Project Context or the current issue brief.

## CPT UI Settings

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

## Taxonomy Settings

Use a taxonomy when the CPT needs filtering, grouping, labels, or editor organization.

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

## ACF Field Group Shape

Create one field group for CPT-specific fields, with a location rule targeting the registered post type.

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
- Create a WPGB grid whose source is the new CPT.
- Select the created card in the grid settings.
- Record generated `<grid-id>` and `<card-id>` in Project Context or the handoff.

Template lookup usually follows these shapes:

```text
smart-template-builder/post-types/<resource>/cards/<resource>-card.php
smart-template-builder/post-types/<resource>/cards/<resource>-card-part-1.php
smart-template-builder/post-types/<resource>/cards/<resource>-card-part-2.php
smart-template-builder/post-types/<resource>/singles/<resource>-single.php
```

Use the equivalent paths from Project Context when a project differs.

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

## Handoff Fields

The CPT foundation handoff should capture:

- CPT registered name, labels, and detail-page decision.
- URL slug or explicit "no public detail page" decision.
- Taxonomy name, hierarchy, and purpose, if any.
- ACF field group, field names, and unresolved field questions.
- WPGB grid/card IDs as project-local values.
- Card template files and optional single template files.
- Display target: existing grid Section, slider Section, or dedicated Section.
- Expected selectors for card, archive/grid, and optional single template.
- Server verification results and cache state.
- Local frontend checklist for CSS/SCSS, responsive checks, Chrome Local Overrides spikes, and Playwright-oriented verification.
