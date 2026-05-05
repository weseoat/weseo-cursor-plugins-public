# ACF/WST Pattern Reference

This reference supports the `acf-wst-patterns` Cursor Rule. It preserves reusable ACF and WST structure while keeping project-specific keys, IDs, URLs, and examples in Project Context.

## ACF Field Group Architecture

Each Flexible Content Section usually has three related records:

1. A layout entry inside the Flexible Content field's `layouts` array.
2. One clone `acf-field` post as a child of the Flexible Content field post, with `parent_layout` linking it to the layout key.
3. One Section field group containing Section-specific fields and clones of shared groups.

Project Context should provide:

- Flexible Content field key: `<fc-field-key>`.
- Flexible Content field post ID: `<fc-post-id>`.
- Section field group key: `<section-field-group-key>`.
- Layout key: `<layout-key>`.

## Shared Clone Groups

Shared clone groups are clone targets, not standalone editor field groups. They are normally inactive and cloned into Section field groups.

| Group key | Title | Purpose | Common fields |
| --- | --- | --- | --- |
| `<inhalt-group-key>` | `[TMPL] Inhalt` | Content tab | `title`, `subtitle`, `text`, `title_format`, `subtitle_format`, `title_style`, `subtitle_style` |
| `<button-group-key>` | `[TMPL] Button` | Button tab | `buttons` repeater with type, title, link fields, variations, target, and aria label |
| `<layout-group-key>` | `[TMPL] Layout` | Layout tab | `align`, `width`, `bg_color_switch`, `bg_color`, padding fields, custom class, custom ID, conditional wrappers |

## Content And Layout Fields

Common content clone fields:

| Name | Type | Default |
| --- | --- | --- |
| `title` | textarea | project default |
| `subtitle` | textarea | project default |
| `title_format` | select | usually `h2` |
| `subtitle_format` | select | usually `h4` |
| `title_style` | select | project default |
| `subtitle_style` | select | project default |
| `text` | wysiwyg | empty |

Common layout clone fields:

| Name | Type | Typical values |
| --- | --- | --- |
| `align` | radio | `wso-align-left`, `wso-align-center`, `wso-align-right` |
| `width` | radio | content width or full container |
| `bg_color_switch` | radio | disabled or enabled |
| `bg_color` | select | project background classes |
| `padding_top` | radio | project top-padding classes |
| `padding_bottom` | radio | project bottom-padding classes |
| `class` | text | custom CSS class |
| `id` | text | custom CSS ID |

## Creating A New Flexible Content Layout

The reusable shape is:

1. Create a Section field group with a tab and Section-specific fields.
2. Clone the shared content group with `prefix_name: 1`.
3. Clone the shared button group with `prefix_name: 1` when the Section has buttons.
4. Clone the shared layout group with `prefix_name: 1`.
5. Add the Flexible Content layout entry.
6. Create a clone child field under the Flexible Content field post.
7. Set the clone child field's `parent_layout` to the exact generated layout key.
8. Register the Section include in the Flexible Content render block.

## WST Shortcode Reference

Field output:

| Shortcode | Purpose |
| --- | --- |
| `[wst_acf field='name']` | Output an ACF field value. |
| `[wst_acf field='name' id='option']` | Output from an options page. |
| `[wst_acf field='name' id='{{row_id/wst_acf_repeater}}']` | Output from a repeater row context. |
| `[wst_acf_image field='name' size='size']` | Output an image tag. |
| `[wst_acf_file field='name' url='1']` | Output a file URL. |
| `[wst_acf_date_time field='name' format='d.m.Y']` | Output a formatted date. |
| `[wst_post_title]` | Output the current post title. |
| `[wst_post_permalink url='1']` | Output the current post URL. |
| `[wst_post_thumbnail size='size']` | Output the featured image. |
| `[wst_post_excerpt words='55' fallback='1' html='0']` | Output a post excerpt. |
| `[wst_post_terms taxonomy='category']` | Output term data. |

Conditionals:

| Shortcode | Purpose |
| --- | --- |
| `[wst_if field='x' compare='!=' value='']...[else]...[/wst_if]` | Single condition. |
| `[wst_if_a]...[else_a]...[/wst_if_a]` | Nested condition level A. |
| `[wst_if_b]...[else_b]...[/wst_if_b]` | Nested condition level B. |
| `[!wst_if_b field='x' value='']...[/!wst_if_b]` | Negated non-empty condition. |
| `[wst_if_a field='x' value='' field_2='y' value_2='' relation='OR']` | Multi-field condition. |
| `[wst_is_mobile]...[/wst_is_mobile]` | Mobile-only output. |
| `[!wst_is_mobile]...[/!wst_is_mobile]` | Non-mobile output. |

Loops, templates, and variables:

| Shortcode | Purpose |
| --- | --- |
| `[wst_acf_repeater field='name']...[/wst_acf_repeater]` | Repeater loop. |
| `[wst_acf_repeater_a]` | Nested repeater level A. |
| `[wst_acf_flexible_content field='name']...[/wst_acf_flexible_content]` | Flexible Content loop. |
| `[wst_include template='path']` | Include a partial relative to the WST template root. |
| `[wst_include template='path' layout='layout_name']` | Include a Section template for a specific layout. |
| `[wst_include template='path' acf_id='{{post.id}}']` | Include a card or partial in post context. |
| `[wst_variable name='x']...[/wst_variable]` | Define a reusable variable. |
| `[wst_variable name='x']` | Output a previously defined variable. |
| `[wst_string_replace search=',' replace=' ']...[/wst_string_replace]` | Transform string output. |
| `[wst_continue_loop id='wst_acf_repeater']` | Skip a repeater row. |

## Template Variables

| Variable | Source |
| --- | --- |
| `{{title_format}}` | ACF `title_format` field. |
| `{{title_style}}` | ACF `title_style` field. |
| `{{subtitle_format}}` | ACF `subtitle_format` field. |
| `{{subtitle_style}}` | ACF `subtitle_style` field. |
| `{{row_id/wst_acf_repeater}}` | Current repeater row post ID. |
| `{{loop_row_index/wst_acf_repeater}}` | Current repeater row index. |
| `{{conditional_logic_start}}` | ACF field or placeholder that starts conditional Section output. |
| `{{conditional_logic_end}}` | Closing conditional output placeholder. |
| `{{post.id}}` | Current post ID in card context. |

## Standard Section Template Structure

Use nearby project templates as the source of truth. A generic, non-normative shape is:

```php
<?php
if (! defined('ABSPATH')) exit;
?>
{{conditional_logic_start}}

<section class="wso-section wso-section-<section-slug> [wst_include template='elements/layout/layout.php']"
  [wst_include template='elements/section-id.php']
  [wst_include template='elements/tabindex.php']>
  <div class="row gy-4 wso-section-wrapper wso-section-inner">
    [!wst_if_b field='title' value='' field_2='subtitle' value='' field_3='text' value_3='']
    <div class="col-12 wso-wrap valign-top wso-wrap-content">
      <div class="row gy-4 wso-wrap-inner">
        <div class="col-12 wso-column wso-column-column">
          <div class="wso-column-attr">
            [wst_include template="elements/content.php" title="TMPL Inhalt"]
          </div>
        </div>
      </div>
    </div>
    [/!wst_if_b]

    <div class="col-12 wso-wrap">
      <!-- Section-specific content. -->
    </div>
  </div>
</section>

{{conditional_logic_end}}
```

Common includes:

- `elements/layout/layout.php` outputs width, padding, alignment, and background classes.
- `elements/content.php` outputs the shared title, subtitle, and text block.
- `elements/button/button.php` outputs the shared button repeater.
- `elements/section-id.php` outputs an optional `id` attribute.
- `elements/tabindex.php` preserves the project tabindex hook when present.
- `elements/display.php` outputs project display or color-scheme classes when present.

## ACF Field Storage In Post Meta

Flexible Content values usually follow this meta-key shape:

- `flexible_content` stores a serialized array of layout names.
- `flexible_content_<index>_<field_name>` stores field values.
- `_flexible_content_<index>_<field_name>` stores field key references.

For cloned fields with `prefix_name: 1`, the clone name becomes part of the meta key:

- `flexible_content_<index>_<clone_name>_<field_name>`.
- `_flexible_content_<index>_<clone_name>_<field_name>`.

Treat exact field names, clone names, and indexes as project-specific unless the current Section handoff or Project Context supplies them.
