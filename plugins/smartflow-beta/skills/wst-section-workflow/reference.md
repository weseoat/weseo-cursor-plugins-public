# WST Section Workflow Reference

Reusable lookup material for the `wst-section-workflow` Skill. Values shown with angle brackets come from `PROJECT-CONTEXT.md`, the current brief, or the Section work record. Do not copy project-only keys, IDs, URLs, paths, labels, or access values into reusable plugin content.

All field definitions here are PHP registrations per the `acf-php-field-groups` Rule. There are no database field-definition shapes anymore; the legacy `wp_insert_post`-based ACF wiring is retired together with the structural-DB-write hazard.

## Shared clone groups

Every standard Section field group usually clones the project's shared content, button, and layout groups (the `[TMPL]` groups, PHP-registered since the project migration).

| Clone group | Placeholder | Typical purpose |
| --- | --- | --- |
| Content | `<content-clone-group-key>` | Section headline, subtitle, text, title format, subtitle format, and text style controls. |
| Button | `<button-clone-group-key>` | Button repeater with labels, link type, internal/external targets, accessibility labels, IDs, and style variations. |
| Layout | `<layout-clone-group-key>` | Width, alignment, custom class, padding, background, anchor ID, and editor layout controls. |

The exact group keys are project-local values: read them from the PHP field-group sources under `smart-template-builder/acf/field-groups/`. If a clone group key cannot be found there, stop and ask or record an unresolved placeholder. Do not invent reusable default keys.

## Section field group shape (PHP)

Create one PHP field group file for the Section-specific editor fields only when `Work type` is `new-section-foundation` or the confirmed work record explicitly requires a new field group. For an `existing-section-remodel`, reuse the existing field group and field keys by default.

The group is cloned into the Flexible Content layout and needs no location rule. One file per group, registered through the project's field-group loader on `acf/init`:

```php
<?php

if (! defined('ABSPATH')) exit;

add_action('acf/init', function () {
    acf_add_local_field_group([
        'key'    => 'group_<unique>_section_<section_slug>',
        'title'  => '[TMPL] <section-label>',
        'fields' => [
            [
                'key'   => 'field_<unique>_<section_slug>_tab',
                'label' => '<section-label>',
                'name'  => '',
                'type'  => 'tab',
            ],
            [
                'key'          => 'field_<unique>_<section_slug>_content',
                'label'        => 'Content',
                'name'         => 'content',
                'type'         => 'clone',
                'clone'        => ['<content-clone-group-key>'],
                'display'      => 'seamless',
                'prefix_name'  => 1,
                'prefix_label' => 0,
            ],
            // button clone, layout clone, then Section-specific fields
        ],
        'location' => [],
        'active'   => true,
    ]);
});
```

Key discipline (`acf-php-field-groups` Rule): every group, field, and layout carries a stable explicit fresh key. Never reuse an existing key for a different field, and never rename a saved field's `name` or `key` casually — stored content references both; such changes are data migrations needing an explicit user decision.

## Flexible Content layout wiring (PHP)

The Page-Builder Flexible Content container is PHP-registered (one-time migration per project; see `migrate-ssh-to-local`). The layout entry and the seamless clone child field are added in that container's PHP source:

```php
// Inside the FC field's 'layouts' array:
'<generated-layout-key>' => [
    'key'        => '<generated-layout-key>',
    'name'       => 'layout_<section_slug_with_underscores>',
    'label'      => '<section-label>',
    'display'    => 'block',
    'sub_fields' => [
        [
            'key'            => '<clone-child-field-key>',
            'label'          => '<section-label>',
            'name'           => '<clone-child-field-name>',
            'type'           => 'clone',
            'clone'          => ['<section-field-group-key>'],
            'display'        => 'seamless',
            'layout'         => 'block',
            'prefix_label'   => 0,
            'prefix_name'    => 1,
            'parent_layout'  => '<generated-layout-key>',
            'acfe_save_meta' => 1,
        ],
    ],
],
```

`parent_layout` is the binding between the Flexible Content layout and the cloned Section fields. If it does not match the layout key exactly, editor fields can appear under the wrong layout or fail to appear.

Field names resolve through the seamless clone chain with `prefix_name=1`: a field `title` in the Content group, cloned as `content` into the Section group, cloned as `<clone-child-field-name>` into the layout, is stored and read as `<clone_child>_content_title`. These expanded names are what `get_sub_field()` resolves, what test-content row plans must use, and what preview fixtures carry.

If the FC container is still an admin-created database group, stop: ACF cannot add PHP-registered fields to an admin-created Flexible Content field. The one-time migration must happen first.

## Section template and registration

Section templates live in the project-owned WST source inside the active child theme (repository-relative, since the repo root is the wp-content level):

```text
themes/<child-theme>/smart-template-builder/sections/<section-slug>.php
```

Do not place Section templates, PHP field groups, or `flexible-content.php` registrations under `plugins/weseo-smart-template-builder/`. That folder is the WST runtime/library and is off-limits unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath.

Register the Section in the project-local Flexible Content template registry:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

For `existing-section-remodel`, reuse the existing template path; do not create a new template file unless the confirmed work record explicitly approves it.

Template output composes existing WST shortcodes (`wst-php-authoring-route` Rule). Every shortcode form new to the project needs the four-source proof through the bundled `wst-shortcodes` Skill.

## CSS hooks belong in the work record

This workflow records CSS hook expectations; it does not create or edit CSS or SCSS.

Record:

- Primary class: `.wso-section-<section-slug>`.
- Wrapper/item selectors that template markup depends on.
- CSS file path, usually `<theme-css-section-path>/<section-slug>.css`.
- Whether the project requires a new style loader registration.
- `CSS status` from `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

Final spacing, typography, responsive behavior, and browser QA belong to `frontend-section-qa`, which works on tracked CSS source in the same repository: injection-proof iteration, served pass only after a bridge-verified deploy.

## Content row plan (no server shell)

Test content and catalog-page content are entered in the WordPress admin by the user, from an exact row plan the Skill prepares, unless `PROJECT-CONTEXT.md` documents an approved programmatic route. A usable row plan names:

```text
Page: <test-page-title-or-id> (unlinked test page unless directed otherwise)
Row <n>: layout <section-label>
  <expanded_field_name>: <value>
  <expanded_field_name>_img: <attachment-id>
  ...
Append after row <m> / replace row <m>   (replace needs its own confirmation)
```

Use the expanded clone field names, real design content, and Media Library attachment IDs (imported over the REST media route or by the user). After the user reports the rows are saved, verify over the rendered page or the preview export route.

## Work record skeleton

The Section work record lives in the project docs layer (default `docs/sections/<section-slug>.md`). Sections to maintain:

```text
# Section: <section-label> (<section-slug>)

## Status
Work type / write scope / Discovery and safety status / Frontend route / deploy state (commit, bridge verification)

## Identity
Layout name + key, group + field keys, template path, registration entry

## Discovery sources
Figma desktop + mobile links, reference Sections, catalog sections consulted, four-source proofs

## Protected existing artifacts   (remodels)

## CSS hooks
Primary class, wrappers, custom properties, selectors to preserve, CSS path, CSS status

## Preview URLs

## Visual QA Targets
| Variant | Viewport | Expectation (yes/no-checkable) | Result |

## Frontend QA Brief

## QA notes / open questions / blockers
```

## Cleanup

Temporary artifacts (snapshots, scratch exports, diagnostic dumps) live under the repository `tmp/` folder, which never reaches the server. Delete them when the run completes. There are no server-side temp scripts anymore: everything the server executes arrived through a bridge-verified deploy.
