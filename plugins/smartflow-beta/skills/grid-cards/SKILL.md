---
name: grid-cards
description: Author WST grid-card templates for WP Grid Builder under smart-template-builder/post-types/<resource>/cards/ in the child theme. Core rule: every wst_ shortcode that reads a field from the database needs id='{{post_id}}'. Use when creating new CPT cards or WPGB card templates, remodeling cards, or when grid cards render empty or missing ACF values.
---

# Grid Cards

WST card templates for WP Grid Builder live in the active child theme under:

```text
wp-content/themes/<child-theme>/smart-template-builder/post-types/<resource>/cards/
```

Typical file names:

- `<resource>-card.php` — single-part card
- `<resource>-card-part-1.php`, `-part-2`, … — multi-part card when WPGB expects several card areas

Never place card templates under `wp-content/plugins/weseo-smart-template-builder/` — that folder is only the WST runtime (see the `file-edit-boundary` Rule).

## Why `id='{{post_id}}'`?

Grid cards are rendered **per grid entry**, not in the context of the current page.
Without an explicit post ID, a field-reading shortcode often reads the wrong object or returns empty.

WP Grid Builder provides the SmartTag `{{post_id}}` per card. **Always** pass it to field-emitting shortcodes.

> **Open catalog discrepancy:** the bundled Gesamthilfe snapshot names `{{post.id}}` for WP Grid Builder in three places, while the working project implementation this Skill is based on builds on `{{post_id}}`. Until a runtime test on a real WPGB card settles the spelling, treat this as the open validation documented in the `wst-shortcodes` Skill: prefer `{{post_id}}` (working precedent), verify the rendered HTML per the four-source proof, and report the outcome so catalog or templates can be corrected.

## Core Rule

**Every `wst_` shortcode that reads a field from the database must carry `id='{{post_id}}'`.**

This applies in particular to shortcodes with a `field='…'` attribute (ACF fields, conditions on fields, repeaters, type-specific ACF output):

| Shortcode type | Example |
|---|---|
| Condition on a field | `[wst_if field='file' compare='!=' value='' id='{{post_id}}']` |
| Nested condition | `[wst_if_a field='file' compare='end_with_csi' value='.pdf' id='{{post_id}}']` |
| ACF output | `[wst_acf field='contact_email' id='{{post_id}}']` |
| Type-specific ACF output | `[wst_acf_file_size field='file' id='{{post_id}}']` |
| Repeater | `[wst_acf_repeater field='items_repeat' id='{{post_id}}']` |

The `id` attribute sits **on the same shortcode tag** as `field`, `compare`, `value`, and so on.

### No `id` needed

Shortcodes without a field binding (`field='…'`) that do not read a record from the database:

- `[wst_i18n_string …]`
- `[wso_svg_converter …]`
- `[wst_include …]`
- pure layout/wrapper markup without WST field access

Post standard fields (`[wst_post_title]`, `[wst_post_excerpt]`, `[wst_post_thumbnail]`) can work without `id` in some cards. If the output is empty or depends on the page context, set `id='{{post_id}}'` there as well.

## Nested Same-Named Shortcodes

When an enclosing `wst_if` contains another `wst_if`, the inner shortcode needs a suffix (`_a`, `_b`, …). Details: the bundled `wst-nested-shortcodes` Skill and the `wst-conditional-nesting` Rule.

**Both rules apply at the same time** — suffix and `id='{{post_id}}'`:

```text
[wst_if field='file' compare='!=' value='' id='{{post_id}}']
	[wst_if_a field='file' compare='end_with_csi' value='.pdf' id='{{post_id}}']
		…
	[/wst_if_a]
[/wst_if]
```

## Markup Conventions

- Stable CSS hooks with the `wso-` prefix, for example `.wso-<resource>-card`
- Modifiers with a **single** hyphen: `wso-resource-card-highlight`, not `--`
- Full-area links: `.wso-absolute-link` with an `aria-label` derived from the visible title
- Class naming rule: if the project has a local Cursor Rule for `wso-` class names, follow it; otherwise use single hyphens instead of BEM `--`

## Workflow

1. **Read the CPT work record** in the project docs layer (`docs/post-types/<resource>.md`) — CPT slug, ACF field names, expected CSS hooks, WPGB card ID
2. **Check existing cards in the project** — same CPT or a similar layout as the pattern
3. **Create the card template** under `post-types/<resource>/cards/`
4. **Check every field-based `wst_` shortcode** — is `id='{{post_id}}'` set?
5. **Check nesting** — suffixes on same-named enclosing shortcodes
6. **CSS** — when needed, through the bundled `cpt-frontend-qa` Skill, not in the card PHP

New-to-the-project shortcode forms go through the four-source proof of the `wst-shortcodes` Skill before they land in the card template.

## Checklist Before Finishing

```text
- [ ] Path: smart-template-builder/post-types/<resource>/cards/
- [ ] Every wst_ shortcode with field='…' has id='{{post_id}}'
- [ ] Nested wst_if / wst_acf_repeater: correct suffixes (_a, _b, …)
- [ ] CSS hooks wso-* with single hyphens
- [ ] No field data cached through [wst_variable] when the WPGB card context is affected (prefer direct field references)
- [ ] Accessibility: meaningful aria-label on icon-only or overlay links
```

## Related

- `wst-shortcodes` Skill — catalog snapshot, four-source proof, and the open `{{post.id}}` vs `{{post_id}}` validation
- `wst-nested-shortcodes` Skill — suffix rules for nesting
- `wst-new-post-type` Skill — CPT foundation including the card path and WPGB wiring
- `cpt-frontend-qa` Skill — CSS and visual QA after the card markup
