---
name: smarttags
description: SmartTag {{...}} placeholder syntax of the WESEO Smart Template Builder (WST) - placeholders replaced by field values at output time, including conversion commands (pipe |), loop context ({{field/loop_name}}), nested SmartTags, attachment metadata, and SmartTags as variables ({{$var}}). Use when writing or editing WST templates, WordPress content, or ACF output that uses {{...}} placeholders/SmartTags, or when the user mentions SmartTags, conversion commands, or {{...}} syntax.
---

# SmartTags `{{…}}`

## SmartTags: `{{field/source}}`

Placeholders replaced by values at output time. They return text only (multidimensional values come back comma-separated).

```text
{{post_title}}                  -> field of the current post
{{post_title/wst_posts}}        -> field from a named loop (see below)
{{post_title/*}}                -> * = documentation placeholder, see attachment metadata
{{post_title/{{post_parent}}}}  -> nested (inner tag resolves first)
```

### Conversion Commands (pipe `|`)

Convert the value of a SmartTag in place: `{{field|command=argument}}`.

```text
{{post_title|chars=10}}                  {{post_excerpt|words=10}}
{{post_date|format=m}}                   {{post_thumbnail|image_size=full}}
{{post_terms|taxonomy=category&count=1}} {{price|number_format=1&decimals=2}}
{{post_content|strip_tags}}              {{post_title|count_words}}
```

Exactly **one** `|` is allowed between field and conversion. Multiple commands are chained in one block with `&` (for example `{{post_title|chars=10&strip_tags=1}}`); with `{{field|a|b}}` the `b` part is lost.

Note on `count` (for example with `post_terms`): `count` is a counting flag and returns the **number** of terms — it does not limit the output to the first term.

The full command list is in the catalog snapshot bundled with the `wst-shortcodes` Skill, section `### 5.24 SmartTags` of `SMART-TEMPLATE-HILFE.md` (load only that section). New-to-the-project SmartTag paths and commands go through the four-source proof of that Skill.

## Loops

Loop shortcodes output their content once per record. Most important: `[wst_posts]`, `[wst_terms]`, `[wst_foreach]`; ACF: `[wst_acf_repeater]`, `[wst_acf_post_object]`, `[wst_acf_relationship]`, `[wst_acf_taxonomy]`.

**Core rule:** inside a loop, field-emitting shortcodes need the current record through `id='{{post_id/<loop-name>}}'`. `<loop-name>` is the shortcode name of the loop (for example `wst_posts`). Repeater rows use `{{row_id/<loop-name>}}`.

```text
[wst_posts post_type='post']
  [wst_post_title id='{{post_id/wst_posts}}']
[/wst_posts]
```

With SmartTags (shorter):

```text
[wst_posts]
  {{post_title/wst_posts}}
[/wst_posts]
```

Nested loops get their own suffixed names and pass the ID down (suffix rules: bundled `wst-nested-shortcodes` Skill):

```text
[wst_acf_repeater field='post_downloads']
  {{group_title/wst_acf_repeater}}
  [wst_acf_repeater_b field='downloads' id='{{row_id/wst_acf_repeater}}']
    {{title/wst_acf_repeater_b}}
  [/wst_acf_repeater_b]
[/wst_acf_repeater]
```

Loop control: `[wst_continue_loop id='<loop-name>']` (skip), `[wst_break_loop id='<loop-name>']` (abort).

## Attachment Metadata

```text
Alt text:    {{_wp_attachment_image_alt/*}}
Title:       {{post_title/*}}
Caption:     {{post_excerpt/*}}
Description: {{post_content/*}}
File URL:    {{attachment_url/*}}
```

The asterisk `*` is a documentation placeholder only: replace it with a concrete attachment ID or the current loop name. A literal `*` in a template does not work.

## Variables

```text
[wst_variable name='x']value[/wst_variable]   -> set
[wst_variable name='x']                        -> read
```

As a SmartTag (with type cast / nested evaluation):

```text
{{$x|int={{post_id}}}}
{{$content=[wst_acf field='wysiwyg' id='{{$x}}']}}
```

## Custom Loop: `[wst_foreach]`

```text
[wst_foreach list='8276|Home,1933|Imprint' key_tag='post_id' value_tag='post_title']
  {{post_title/wst_foreach}} - [wst_post_permalink id='{{post_id/wst_foreach}}' url='1']
[/wst_foreach]
```

Also `range_start`/`range_end`/`range_step` or predefined lists (`list='countries'`, `list='languages'`).

## General WST Rules

- Use only documented fields and commands — never guess; unknown forms go through the four-source proof of the `wst-shortcodes` Skill.
- The bundled Gesamthilfe snapshot is the catalog source; on conflict, the installed runtime and rendered HTML win.
