# WST New Post Type Examples

These examples are non-normative. They show reusable structure only; replace names, labels, fields, selectors, and paths with values from `PROJECT-CONTEXT.md` and the current brief. ACF field names always carry the `wso_<resource>_` prefix per the `acf-local-json` Rule (a Job CPT's salary field is `wso_job_salary`, never `job_salary`).

Keep generated field keys, WPGB IDs, target URLs, and rewrite slugs in the concrete CPT work record. Completed CPT work routes to `cpt-frontend-qa`; Section-level layouts that display CPT content route their Section behavior to `frontend-section-qa`. Every WST shortcode form new to the project needs the four-source proof through the bundled `wst-shortcodes` Skill.

## Simple logo card

Use this shape when the CPT has no public detail page and the card mostly displays a featured image with an optional link.

```php
[wst_post_thumbnail size='<image-size>' url='0']

[wst_if field='wso_<resource>_link_type' compare='!=' value='no-link']
<a class="wso-absolute-link"
   href="[wst_acf field='wso_<resource>_link_url']"
   aria-label="[wst_post_title]"></a>
[/wst_if]
```

Key patterns:

- No public single template is required.
- The featured image carries most of the card presentation.
- The empty link needs an accessible label.
- Final positioning and hover behavior belong to `cpt-frontend-qa`.

## Standard detail card

Use this shape when the CPT has public detail pages and the card links to the single view.

```php
<article class="wso-<resource>-card">
  <a class="wso-<resource>-card-image" href="[wst_post_permalink url='1']">
    [wst_post_thumbnail size='<image-size>']
  </a>

  <div class="wso-<resource>-card-body">
    <h3 class="wso-<resource>-card-title wso-h4">[wst_post_title]</h3>

    [wst_if field='wso_<resource>_summary' compare='!=' value='']
    <p class="wso-<resource>-card-summary">[wst_acf field='wso_<resource>_summary']</p>
    [/wst_if]

    <a class="button secondary-button wso-<resource>-card-link"
       href="[wst_post_permalink url='1']">
      <span class="button_label">Read more</span>
    </a>
  </div>
</article>
```

Key patterns:

- Stable `.wso-<resource>-card-*` hooks are ready for local CSS.
- Optional fields are guarded by WST conditionals.
- Link text should be adapted to the project's language and accessibility conventions.

## Taxonomy label card

Use this shape when taxonomy terms act as a visible category, position, or filter label.

```php
<article class="wso-<resource>-card">
  <p class="wso-<resource>-card-term wso-h6">
    [wst_post_terms taxonomy='wso_tax_<resource>']
    {{name/wst_post_terms}}
    [/wst_post_terms]
  </p>

  <h3 class="wso-<resource>-card-title wso-h4">[wst_post_title]</h3>

  <div class="wso-<resource>-card-media">
    [wst_post_thumbnail size='<image-size>']
  </div>
</article>
```

Key patterns:

- The taxonomy exists because it has a card, filtering, or editor purpose (`wordpress-taxonomies` Rule).
- The term loop depends on the taxonomy name recorded in `PROJECT-CONTEXT.md`.
- The frontend pass owns final badge styling and responsive behavior.

## Optional single template shell

Use this shell only for CPTs with public detail pages. The partial renders only through the Smart Template assignment (user creates a `smart_template` post, assigns it to the CPT, and pastes the `[wst_include]` one-liner into its top-level code editor — see the Skill's step 4.7 and the reference apply-spec); it is never found through the WordPress template hierarchy.

```php
<?php if (! defined('ABSPATH')) exit; ?>

<main class="wso-<resource>-single">
  <section class="wso-<resource>-single-hero">
    <h1 class="wso-<resource>-single-title">[wst_post_title]</h1>
    [wst_post_thumbnail size='<image-size>']
  </section>

  <section class="wso-<resource>-single-content">
    [wst_if field='wso_<resource>_description' compare='!=' value='']
    [wst_acf_wysiwyg field='wso_<resource>_description']
    [/wst_if]

    [wst_post_excerpt words='5000' fallback='1' html='1']
  </section>
</main>
```

Key patterns:

- Single templates are skipped for non-detail CPTs.
- The shell establishes hooks and semantic regions.
- ACF fields use typed shortcodes (`[wst_acf_text]`, `[wst_acf_wysiwyg]`, …) or the `field_…` key: generic `[wst_acf]` resolves empty in the Smart Template include context. `[wst_post_*]` and `[wst_if]` work normally.
- Final layout, typography, media behavior, and Playwright verification are owned by the frontend pass.
- This shell is the `typed-only` content model. When the design shows page Sections around the core, see Example D.

## Example D: Hybrid single (typed core plus Flexible Content)

Use this shape when the maintainer answered the content-model question with `hybrid`: the detail has a structured, post-specific core (hero, meta, typed columns) **and** shared page Sections that editors compose from the same layouts as pages (Benefits slider, related items grid, later an application form). The Job pattern: typed hero and meta, then Flexible Content for the shared Sections. The reference CPT's Flexible Content clone is the precedent for the ACF side.

ACF side (same JSON group as the typed fields; clone group key read from the install):

```json
{
    "key": "field_<unique>_<resource>_content",
    "label": "<content-label>",
    "name": "wso_<resource>_content",
    "type": "clone",
    "clone": ["<key-of-the-project-[TMPL]-Flexible-Inhalte-group>"],
    "display": "seamless",
    "layout": "block",
    "prefix_label": 0,
    "prefix_name": 1
}
```

Single partial:

```php
<?php if (! defined('ABSPATH')) exit; ?>

<main class="wso-<resource>-single">
  <section class="wso-<resource>-single-hero">
    <h1 class="wso-<resource>-single-title">[wst_post_title]</h1>
    [wst_post_thumbnail size='<image-size>']
  </section>

  <section class="wso-<resource>-single-meta">
    [wst_if field='wso_<resource>_location' compare='!=' value='']
    <p class="wso-<resource>-single-location">[wst_acf_text field='wso_<resource>_location']</p>
    [/wst_if]
  </section>
</main>

[wst_include template="flexible-content.php"]
```

Key patterns:

- The Smart Template include stays the single partial (`post-types/<resource>/singles/<resource>-single.php`); the Smart Template's own Flexible Content stays empty.
- `flexible-content.php` is included **after** the `.wso-<resource>-single` wrapper as a sibling, never inside it. It renders the Sections editors entered in the CPT post's `wso_<resource>_content` clone; with `prefix_name: 1` the field resolves like the reference CPT's clone, so the renderer needs no change.
- One FC field, one insertion point: if Figma sandwiches a page Section between typed blocks, the work record carries that limitation; typed core first, then the FC output. No second FC field and no split typed template without an explicit maintainer decision.
- Page Sections are never hardcoded as includes in the partial to imitate Figma — that is the gap this model closes.
- Section-level behavior of the composed Sections routes to `frontend-section-qa`; the typed core routes to `cpt-frontend-qa`.
