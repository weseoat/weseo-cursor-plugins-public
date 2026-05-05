---
name: wst-new-post-type
description: Create a WESEO Smart Template Builder Custom Post Type foundation. Use when adding a WST CPT, post type, taxonomy, ACF CPT fields, WP Grid Builder grid/card foundation, card template, optional single template, or CPT foundation handoff.
---

# WST New Post Type

## Quick Start

Use this Skill for the server-side WST foundation of a new Custom Post Type. Before editing, read the project-local context for:

- WordPress root, WST template path, theme path, and style registration path.
- CPT naming conventions, URL slug policy, and whether the CPT has a detail page.
- ACF field creation approach, field key naming expectations, and existing CPT field groups.
- WP Grid Builder grid/card conventions and where grid/card IDs are recorded.
- Section handoff location, branch or PR carrier, and target dev or staging URL.

If any required value is missing, ask the maintainer or leave an explicit placeholder in the handoff. Do not invent CPT names, rewrite slugs, ACF keys, post IDs, WPGB IDs, paths, URLs, or theme values.

## Inputs

Capture these values at the start:

- CPT slug, for example `<resource>`, with registered post type usually `wso_<resource>`.
- Singular and plural admin labels.
- Public detail-page decision and URL slug if detail pages are public.
- Optional taxonomy name, labels, and hierarchy decision.
- Required CPT fields and whether they belong in ACF, core post fields, taxonomy terms, or featured image.
- Card type: single-part, multi-part, or custom section rendering.
- WP Grid Builder display target: grid, carousel, existing Section, or dedicated Section.
- Branch or PR that will carry the CPT foundation and handoff.

## Workflow

Track progress with this checklist:

```text
New WST CPT Foundation:
- [ ] Decide public detail-page, taxonomy, field, card, and display shape
- [ ] Register CPT
- [ ] Register taxonomy if needed
- [ ] Create ACF field group for CPT fields
- [ ] Create WP Grid Builder card and grid foundation
- [ ] Create card template foundation
- [ ] Create optional single template foundation
- [ ] Register minimal style hooks if the project requires style files upfront
- [ ] Flush project caches and verify server state
- [ ] Emit or update CPT foundation handoff
```

### 1. Decide Foundation Shape

Before writing files or changing WordPress configuration, decide:

- Whether the CPT has public detail pages.
- Whether it needs a taxonomy for filtering, grouping, or card labels.
- Which data belongs in post title, featured image, excerpt/editor, ACF fields, or taxonomy terms.
- Whether WP Grid Builder should render a grid, carousel, or card only.
- Whether a dedicated WST Flexible Content Section is required, or an existing grid/slider Section can consume the grid.

Record unresolved decisions in the handoff rather than guessing.

### 2. Register CPT

Register the CPT through the project's established CPT UI or equivalent registration path.

Default invariants:

- Registered post type should follow the project convention, usually `wso_<resource>`.
- `show_ui` and `show_in_rest` should be enabled unless the maintainer gives a reason not to.
- `supports` should include only fields the content model actually uses.
- Public detail-page CPTs need query, search, archive, and rewrite settings reviewed together.
- Non-detail CPTs should not create public URLs accidentally.

Use `reference.md` for reusable CPT UI settings.

### 3. Register Taxonomy If Needed

Add a taxonomy only when the content model requires grouping, filtering, admin columns, or card labels.

Default invariants:

- Taxonomy name usually follows `wso_tax_<resource>`.
- Attach it to the new CPT's registered post type.
- Choose hierarchical behavior deliberately: category-style for controlled groups, tag-style for loose labels.
- Disable public rewrites unless taxonomy archives are part of the brief.
- Enable REST support when editor tooling or block/admin integrations need it.

Use `reference.md` for reusable taxonomy settings.

### 4. Create ACF Field Group

Create an ACF field group whose location rule targets the new CPT.

Recommended structure:

- A tab field for admin organization.
- Content fields specific to the CPT.
- Optional tabs for complex CPTs.
- Field names prefixed with the CPT naming convention when the project does that already.

Use ACF field group posts or the project's established ACF tooling. Do not place `acf_add_local_field()` snippets in theme bootstrap files unless the project has already chosen local PHP field registration as its source of truth.

Use `reference.md` for generic ACF field group and field-shape guidance.

### 5. Create WP Grid Builder Foundation

Create the WP Grid Builder card and grid through the project's established admin workflow.

Default WST pattern:

- The WPGB card exists so the grid can select a card.
- The visual card builder is usually left empty when WST PHP card templates render the card markup.
- The WPGB grid source should point at the new CPT.
- Record generated grid and card IDs in Project Context or the handoff.

Do not hardcode generated WPGB IDs into reusable plugin content. Treat them as project-local values.

### 6. Create Card Template Foundation

Create card template files in the project-local WST post-type template location.

Common shapes:

- Single-part card: one template for the full card.
- Multi-part card: `-part-1`, `-part-2`, or more parts when WPGB expects multiple card areas.
- Custom Section rendering: a dedicated WST Section queries or embeds the grid instead of relying only on a card template.

Template invariants:

- Use stable `.wso-<resource>-card` style hooks.
- Render core post data and ACF fields through the project's WST shortcodes or established helpers.
- Use conditionals for optional fields so empty data does not leave broken markup.
- Include accessible link labels when the card has empty or full-card links.

Use `examples.md` for generic card structures.

### 7. Create Optional Single Template Foundation

Only create a single template when the CPT is publicly queryable and the brief requires detail pages.

Template invariants:

- Use the project-local WST single template location.
- Keep markup compatible with existing WST element, row, wrap, and typography patterns.
- Establish stable `.wso-<resource>-single` hooks.
- Record any required local CSS and QA expectations in the handoff.

If no public detail page exists, record that decision in the handoff so later frontend QA does not look for a single view.

### 8. Register Minimal Style Hooks

If the project expects style files to be registered during server setup, create placeholder or minimal files only to establish tracked hooks.

Allowed server-phase style work:

- Create or record expected card/archive CSS file paths.
- Create or record expected single CSS file paths when there is a detail page.
- Register style files in the project's style loader if that is part of the WST foundation contract.
- Add minimal selectors needed to keep template hooks stable.

Leave final spacing, typography, responsive behavior, Chrome Local Overrides spikes, Playwright checks, and visual QA to the Frontend Design QA phase.

### 9. Flush And Verify Server State

Run the project-local cache flush command. Then verify:

- The CPT appears in the WordPress admin.
- Taxonomy UI appears only when expected.
- ACF fields appear on the CPT edit screen.
- WP Grid Builder can select the new CPT and card.
- Card template markup renders without PHP errors.
- Public single URLs work only when the CPT is intended to have detail pages.
- Expected card or single CSS hooks exist in rendered markup.

### 10. Emit Or Update CPT Foundation Handoff

Update the handoff on the same branch or PR as the CPT foundation work.

Fill these handoff areas before local frontend work starts:

- Handoff carrier and owner.
- CPT name, labels, detail-page decision, taxonomy decision, and display target.
- Template files for card, optional single, and any Section integration.
- ACF field group, field names, and unresolved field questions.
- WP Grid Builder grid/card IDs as project-local values.
- Expected card, archive/grid, and optional single selectors.
- Server responsibilities completed, cache state, known risks, and open questions.
- Local frontend responsibilities for final CSS, responsive checks, Chrome Local Overrides spikes, and Playwright-oriented verification.

## Outputs

When this Skill is complete, the project should have:

- A registered CPT and optional taxonomy.
- A CPT-specific ACF field group when custom fields are needed.
- A WP Grid Builder card/grid foundation when the CPT is displayed through WPGB.
- WST card template foundation and optional single template foundation.
- Minimal tracked hooks or style registration only when the project requires them upfront.
- A handoff that lets Frontend Design QA finish card/archive/single presentation locally.
