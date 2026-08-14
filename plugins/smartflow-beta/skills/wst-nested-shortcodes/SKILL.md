---
name: wst-nested-shortcodes
description: Suffix rules for nesting enclosing wst_ shortcodes (wst_if, wst_acf_repeater, wst_is_mobile, ...) in WST templates. A WordPress shortcode parser limitation forbids same-named nested shortcodes, so every nested same-named shortcode gets a unique suffix _a, _b, _c (e.g. [wst_if_a]...[/wst_if_a], [else_a]). Use when writing or editing WST templates under smart-template-builder (sections, elements, post-types, menu), when nesting same-kind wst_ shortcodes, when debugging nesting bugs with wst_ shortcodes, or when asked about nested wst_ shortcodes.
---

# Nested wst_ Shortcodes

## Core Problem

Some enclosing `wst_` shortcodes can be nested. Because of a limitation in the WordPress shortcode parser (`do_shortcode`, regex-based), nested shortcodes must **not share the same name**: the parser pairs the first closing tag with the outermost opening tag and breaks the nesting silently. Therefore nested `wst_` shortcodes get a `_(letter)` appended to the name, for example `[wst_if_a]`, to distinguish inner from outer shortcodes.

The always-on constraint form of this rule is the bundled `wst-conditional-nesting` Rule; this Skill owns the full suffix mechanics for all enclosing shortcodes.

## When A Suffix Is Needed

- **Needed:** an enclosing shortcode sits (directly or indirectly) inside an enclosing shortcode **with the same base name**. Example: `wst_if` inside `wst_if`, or `wst_acf_repeater` inside `wst_acf_repeater`.
- **Not needed:** self-closing shortcodes without content (for example `wst_acf`, `wst_acf_image`, `wst_post_title`). These may appear any number of times inside enclosing shortcodes and never take a suffix.
- **Not needed:** different base names. A `wst_acf_repeater` may contain a `wst_if` without either of them needing a suffix.

## Format Rules

1. **Suffix form:** base name + `_` + one lowercase letter: `wst_if_a`, `wst_if_b`, `wst_if_c`, …
2. **Opening and closing tags carry exactly the same suffix:** `[wst_if_a] … [/wst_if_a]`. A mismatch like `[wst_if_a] … [/wst_if]` breaks.
3. **The `else` clause inherits the suffix:** `[wst_if_a] … [else_a] … [/wst_if_a]`. Likewise `[else_if_a]`, `[else_b]`, …
4. **Negated shortcodes:** the `!` goes before the base name, the suffix at the end: `[!wst_is_mobile] … [/!wst_is_mobile]`; nested accordingly `[!wst_is_mobile_a]`.
5. **Unique only along the parent chain:** the suffix only has to differ from the enclosing same-named shortcodes. **Siblings** (side by side, not inside each other) may reuse the same suffix.
6. **The base tag without a suffix is the outermost variant (level 0).** `wst_if` may contain `wst_if_a`, which may contain `wst_if_b`.

## Letter Convention And The Open Suffix-Chain Question

Assign letters by nesting depth: outermost nested level `_a`, next `_b`, then `_c`. The count restarts per template/include (a template pulled in through `wst_include` runs its own nesting and does not collide with the parent's suffixes — that is why the `wst-conditional-nesting` Rule prefers `wst_include` for shared partials).

The Gesamthilfe catalog nests `wst_if_b` directly inside `wst_if` (skipping `_a`), and the source material for this Skill treats the letter as freely choosable as long as it differs from the enclosing same-named shortcode. Whether skipping suffix levels really works is an **open validation** (see the known catalog discrepancies in the `wst-shortcodes` Skill): until a runtime test settles it, follow the strict named-level chain `wst_if` -> `wst_if_a` -> `wst_if_b` from the `wst-conditional-nesting` Rule and never skip levels.

## Examples

### `wst_if` over three levels (a -> b -> c)

```text
[wst_if_a field='highlight_show' value='wso-show']
	[wst_if_b field='highlight_title' compare='!=' value='']
		<strong>[wst_acf field='highlight_title']</strong>
	[/wst_if_b]
	[wst_if_b field='highlight_type' compare='!=' value='no-link']
		<a href="[wst_if_c field='highlight_type' value='internal'][wst_acf field='highlight_internal_link'][/wst_if_c][wst_if_c field='highlight_type' value='url'][wst_acf field='highlight_url'][/wst_if_c]"></a>
	[/wst_if_b]
[/wst_if_a]
```

The two `[wst_if_b]` are siblings and share the suffix `_b`. The two `[wst_if_c]` in the `href` are siblings as well (`_c`).

### `else` with suffix

```text
[wst_if_a field='img_alternative_show' compare='!=' value='1']
	[wst_acf_image field='img' size='medium']
[else_a]
	[wst_acf_image field='img_alternative' size='medium']
[/wst_if_a]
```

### Negated shortcode

```text
[!wst_is_mobile]
	[wst_variable name='desktop_image']
[/!wst_is_mobile]
[wst_is_mobile]
	[wst_variable name='mobile_image']
[/wst_is_mobile]
```

### Nested repeater (`wst_acf_repeater` -> `wst_acf_repeater_a`)

```text
[wst_acf_repeater field='column']
	[wst_acf_repeater_a field='column_button_buttons' id='{{row_id/wst_acf_repeater}}']
		[wst_acf field='label' id='{{row_id/wst_acf_repeater_a}}']
	[/wst_acf_repeater_a]
[/wst_acf_repeater]
```

Nested loops also pass the row context downward through `id='{{row_id/<loop-name>}}'` — the SmartTag loop rules are in the bundled `smarttags` Skill.

## Common Failures

- **Same name nested:** `[wst_if] … [wst_if] … [/wst_if] … [/wst_if]` breaks. Give the inner one a suffix -> `[wst_if_a] … [/wst_if_a]`.
- **Suffix only on the opening tag:** `[wst_if_a] … [/wst_if]` breaks. The closing tag must be `[/wst_if_a]`.
- **`else` without suffix:** `[wst_if_a] … [else] … [/wst_if_a]` pairs the `else` with the wrong `wst_if`. Correct: `[else_a]`.
- **Suffix on self-closing shortcodes:** `wst_acf`, `wst_acf_image`, and friends never take a suffix; a `[wst_acf_a]` does not exist.
