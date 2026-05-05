---
name: wst-new-fc-section
description: Create a new WESEO Smart Template Builder Flexible Content Section foundation. Use when adding a WST Section template, ACF section field group, Flexible Content layout, clone child field, section registration, CSS hook, or server-to-local Section handoff.
---

# WST New Flexible Content Section

## Quick Start

Use this Skill for the server-side WST foundation of a new Flexible Content Section. Before editing, read the project-local context for:

- WST template path and theme CSS path.
- Flexible Content field key and field post ID.
- Standard clone group keys for content, button, and layout.
- WP-CLI command and cache flush command.
- Section handoff location and target dev or staging URL.

If any required value is missing, ask the maintainer or leave an explicit placeholder in the handoff. Do not invent ACF keys, post IDs, project paths, URLs, or theme values.

## Inputs

Capture these values at the start:

- Section slug, for example `feature-cards`.
- Section label, for example `Feature Cards`.
- Layout name, usually `layout_<section_slug_with_underscores>`.
- Field key prefix, short and project-unique.
- Target page URL or planned verification URL.
- Source design or written brief.
- Branch or PR that will carry the Section and handoff.

## Workflow

Track progress with this checklist:

```text
New WST FC Section:
- [ ] Create Section template
- [ ] Create ACF section field group
- [ ] Add Flexible Content layout entry
- [ ] Create clone child field with matching parent_layout
- [ ] Register Section in flexible-content.php
- [ ] Create initial CSS file and register it
- [ ] Flush project caches
- [ ] Emit or update Section handoff
```

### 1. Create Section Template

Create `smart-template-builder/sections/<section-slug>.php` or the equivalent project-local WST sections path.

Required template invariants:

- Guard direct access with `if (! defined('ABSPATH')) exit;`.
- Wrap the template with `{{conditional_logic_start}}` and `{{conditional_logic_end}}` when the project uses those WST placeholders.
- Use the primary section class `.wso-section-<section-slug>`.
- Include the project layout, section ID, and tabindex WST elements when present in the existing WST templates.
- Keep custom markup inside WST row, wrap, column, and column attribute classes that match the project pattern.

### 2. Create ACF Section Field Group

Create an `acf-field-group` for the Section. It is cloned into the Flexible Content layout and normally has no direct location rule.

Standard structure:

- Tab field for the Section label.
- Optional editor message.
- Content clone, using the project-local content clone group key.
- Button clone, using the project-local button clone group key.
- Layout clone, using the project-local layout clone group key.
- Section-specific custom fields from menu order `5` onward.

Use ACF field group posts or the project's established ACF tooling. Do not place `acf_add_local_field()` snippets in theme bootstrap files unless the project has already chosen local PHP field registration as its source of truth.

### 3. Add Flexible Content Layout

Update the project Flexible Content field using the project-local field key or field post ID.

Add a layout entry with:

- `key`: generated layout key.
- `name`: `layout_<section_slug_with_underscores>`.
- `label`: human-readable Section label.
- `display`: usually `block`.

Record the generated layout key immediately. The clone child field must reference it exactly.

### 4. Create Clone Child Field

Create an `acf-field` child under the Flexible Content field post.

Required clone settings:

- `type`: `clone`.
- `clone`: the new Section field group key.
- `display`: `seamless`.
- `prefix_name`: `1`.
- `prefix_label`: `0`.
- `parent_layout`: the generated layout key from step 3.
- `acfe_save_meta`: `1` when the project uses ACF Extended save-meta behavior.

The `parent_layout` value is the critical binding between the Flexible Content layout and the cloned Section fields. If it does not match the layout key, the editor fields will appear under the wrong layout or not appear at all.

### 5. Register The Section

Add the Section include inside the project's `[wst_acf_flexible_content]` block:

```php
[wst_include template="sections/<section-slug>.php" layout="layout_<section_slug_with_underscores>"]
```

Keep the registration order consistent with the project's existing editor grouping.

### 6. Create CSS Hook

Create the initial CSS file, usually `styles/sections/<section-slug>.css`, and register it in the project `styles.json` or equivalent style loader.

The server-phase CSS can be minimal, but it must establish stable hooks for the local frontend phase:

- `.wso-section-<section-slug>`.
- Any wrapper or item classes that template markup relies on.
- Initial custom property names if the Section needs local styling tokens.

Leave final spacing, typography, responsive behavior, and Playwright visual checks to the local frontend phase unless the issue explicitly includes them.

### 7. Flush And Verify Server State

Run the project-local cache flush command. Then verify:

- The target page loads.
- The editor can select the new layout.
- The template renders without PHP errors.
- The expected section class exists in the page markup.

### 8. Emit Or Update Section Handoff

Copy or update `section-handoffs/section-handoff.template.md` on the same branch or PR as the Section work.

Fill these handoff areas before local CSS work starts:

- Handoff carrier and owner.
- Section name, layout name, target page URL, and source reference.
- Template file, CSS file, ACF section field group, Flexible Content layout key/name, and ACF fields.
- Primary section class, wrapper classes, custom properties, and selectors to preserve.
- Expected desktop, tablet, mobile, content variation, and interaction behavior.
- Server responsibilities completed, cache state, known risks, and open questions.

Run `python scripts/validate-section-handoffs.py` when the handoff lives in this repository.

## Generic Example

For a `feature-cards` Section:

- Template: `smart-template-builder/sections/feature-cards.php`.
- CSS: `styles/sections/feature-cards.css`.
- Layout name: `layout_feature_cards`.
- Primary class: `.wso-section-feature-cards`.
- ACF group: `<section-field-group-key>` from the project-local ACF setup.
- Flexible Content field: `<fc-field-key>` or `<fc-post-id>` from project context.
- Handoff: records the generated layout key, created fields, target URL, cache state, and local frontend checklist.
