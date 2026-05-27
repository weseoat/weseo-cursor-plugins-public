# WST Section Workflow Reference

Reusable lookup material for the `wst-section-workflow` Skill. Values shown with angle brackets come from Project Context, the current issue brief, or the prefilled Section handoff draft.

This file is the bundled, reusable reference for WST Flexible Content Section work. Legacy SmartFlow guide material may be useful as source material, but plugin packages are the canonical workflow source. Do not copy project-only keys, IDs, URLs, paths, labels, or access values from legacy examples into reusable plugin content.

This reference is shared across all Section work types: `new-section-foundation`, `existing-section-remodel`, and the routing decisions for `visual-only`. The Skill itself decides which parts of this reference apply to a given request.

## Shared Clone Groups

Every standard Section field group usually clones the project's shared content, button, and layout groups. The exact group keys are project-local values.

| Clone Group | Placeholder | Typical Purpose |
| --- | --- | --- |
| Content | `<content-clone-group-key>` | Section headline, subtitle, text, title format, subtitle format, and text style controls. |
| Button | `<button-clone-group-key>` | Button repeater with labels, link type, internal/external targets, accessibility labels, IDs, and style variations. |
| Layout | `<layout-clone-group-key>` | Width, alignment, custom class, padding, background, anchor ID, and editor layout controls. |

If any clone group key is unknown, stop and ask or record an unresolved placeholder in the Section handoff draft. Do not invent reusable default keys.

## Section Field Group Shape

Create one ACF field group for the Section-specific editor fields only when `Work type` is `new-section-foundation` or the confirmed remodel handoff explicitly requires a new field group. For an `existing-section-remodel`, reuse the existing field group and field keys by default.

It is normally cloned into the Flexible Content layout and does not need a direct location rule unless the project has chosen a different ACF source of truth.

```php
$prefix = '<section-prefix>';
$label  = '<section-label>';

$group_id = wp_insert_post([
    'post_type'    => 'acf-field-group',
    'post_title'   => '[WST] Section ' . $label,
    'post_name'    => 'group_' . $prefix . '_section',
    'post_status'  => 'publish',
    'post_content' => serialize([
        'location'              => [],
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => [],
        'active'                => true,
    ]),
]);
```

Recommended child fields:

| Order | Field | Type | Notes |
| --- | --- | --- | --- |
| 0 | `<section-label>` | `tab` | Opens the Section editor group. |
| 1 | `<editor-message>` | `message` | Optional editor guidance when useful. |
| 2 | `content` | `clone` | Clones `<content-clone-group-key>`, usually seamless with prefixed names. |
| 3 | `button` | `clone` | Clones `<button-clone-group-key>`, usually seamless with prefixed names. |
| 4 | `layout` | `clone` | Clones `<layout-clone-group-key>`, usually seamless with prefixed names. |
| 5+ | `<section-field-name>` | project-specific | Only fields required by this Section's content model. |

Use generated field keys only after the project creates them. Record those keys in Project Context or the handoff when later work depends on them.

## Flexible Content Layout Wiring

Update the project Flexible Content field using the project-local field key or field post ID. For `existing-section-remodel`, do not change the existing layout name, layout key, or `parent_layout` unless the confirmed handoff explicitly approves a structural change.

Required values:

- Flexible Content field key or post ID: `<fc-field-key>` or `<fc-field-post-id>`.
- Layout name: `layout_<section_slug_with_underscores>`.
- Layout label: `<section-label>`.
- Generated layout key: `<generated-layout-key>`.
- Section field group key: `<section-field-group-key>`.
- Clone child field key/name: `<clone-child-field-key>` and `<clone-child-field-name>`.

The layout entry should include:

```php
$fc_content['layouts']['<generated-layout-key>'] = [
    'key'     => '<generated-layout-key>',
    'name'    => 'layout_<section_slug_with_underscores>',
    'label'   => '<section-label>',
    'display' => 'block',
];
```

The clone child field under the Flexible Content field must reference the exact generated layout key:

```php
wp_insert_post([
    'post_type'    => 'acf-field',
    'post_title'   => '[WST] <section-label>',
    'post_name'    => '<clone-child-field-key>',
    'post_excerpt' => '<clone-child-field-name>',
    'post_parent'  => '<fc-field-post-id>',
    'post_status'  => 'publish',
    'post_content' => serialize([
        'type'           => 'clone',
        'clone'          => ['<section-field-group-key>'],
        'display'        => 'seamless',
        'layout'         => 'block',
        'prefix_label'   => 0,
        'prefix_name'    => 1,
        'parent_layout'  => '<generated-layout-key>',
        'acfe_save_meta' => 1,
    ]),
]);
```

`parent_layout` is the binding between the Flexible Content layout and the cloned Section fields. If it does not match the layout key exactly, editor fields can appear under the wrong layout or fail to appear.

## Section Template And Registration

Section templates live in the project-owned WST source inside the active child theme. The theme-relative lookup shape is:

```text
smart-template-builder/sections/<section-slug>.php
```

Resolved against the active child theme that means:

```text
<wp-root>/wp-content/themes/<child-theme>/smart-template-builder/sections/<section-slug>.php
```

Do not place Section templates, ACF includes, or `flexible-content.php` registrations under `wp-content/plugins/weseo-smart-template-builder/`. That folder is the WST runtime/library and is off-limits unless `PROJECT-CONTEXT.md` records an explicit project-source exception for that exact subpath.

Register the Section in the project-local Flexible Content template registry:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

Use equivalent paths from Project Context when a project differs. For `existing-section-remodel`, reuse the existing template path; do not create a new template file unless the confirmed handoff explicitly approves it.

## CSS Hooks Belong In The Handoff

Server-side WST Builder records CSS hook expectations in the Section handoff. It does not create or edit final Section CSS or SCSS over Remote-SSH.

Record:

- Primary class: `.wso-section-<section-slug>`.
- Wrapper/item selectors that template markup depends on.
- CSS file path, usually `<theme-css-section-path>/<section-slug>.css`.
- Whether the project requires a new style loader registration during local frontend work.
- `CSS status` from `existing`, `new-needed-for-frontend`, `unknown`, or `not-applicable`.

Final spacing, typography, responsive behavior, Chrome Local Overrides spikes, local Playwright MCP browser QA, and optional project-local Playwright regression checks belong to Frontend Design QA. New CSS files and new style loader entries are created or registered by `frontend-section-qa` in tracked local source files, not by this Skill over Remote-SSH.

## Live And Unknown Environment Confirmation

For any change in a `live` or `unknown` environment, the prompt to the maintainer must be concrete. Generic example:

```text
Work type: existing-section-remodel
Environment: live
Files to change: wp-content/themes/<child-theme>/smart-template-builder/sections/intro.php
ACF/DB objects to change: none
Content changes: none
Cache action: wp cache flush after template change
Rollback path: Git revert on branch <branch>

May I perform exactly these changes on <environment>?
```

Cache flush on `live` or `unknown` requires its own confirmation even when other server writes were already approved.

## Section Handoff Draft

Create the prefilled Section handoff draft during the bundled `grill-me` preflight at the project-configured storage location from Project Context.

The draft should capture:

- Handoff carrier, owner, project-configured storage location, and source brief.
- `Work type`, `Environment`, `Server write scope`, `Frontend route`, `Preflight gate status`, `Protected existing artifacts`, and `CSS status`.
- Section slug, label, layout name, target URL or planned verification URL, and source design or written brief.
- Template path, CSS path, Section field group, Flexible Content field, layout key/name, clone child field, and Section-specific fields.
- Primary section class, wrapper classes, custom properties, and selectors to preserve.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Server verification results, cache state, known risks, open questions, unresolved placeholders, and stop conditions.
- Local frontend checklist for CSS/SCSS, responsive checks, Chrome Local Overrides spikes, local Playwright MCP browser QA, and optional project-local Playwright regression verification.

Completed Section handoffs route to the Frontend Design QA `frontend-section-qa` Skill.

## Cleanup

If project-local temporary scripts are used to create ACF or wire Flexible Content, create them only in the safe location required by that project, run them through the project-local WP-CLI command, then delete them immediately. Treat this as inside the approved `Server write scope`; do not introduce temporary scripts outside that scope.

After server changes, flush caches with the project-local cache command only when the cache action is inside the approved `Server write scope` and, on `live` or `unknown` environments, explicitly confirmed. Record the result in the Section handoff.
