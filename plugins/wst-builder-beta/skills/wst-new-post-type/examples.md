# WST New Post Type Examples

These examples are non-normative. They show reusable structure only; replace names, labels, fields, selectors, and paths with values from Project Context and the current issue brief.

Keep generated ACF keys, field post IDs, WP Grid Builder IDs, target URLs, rewrite slugs, and storage locations in the concrete CPT handoff. Completed CPT handoffs route to `cpt-frontend-qa`; Section-level layouts that display CPT content route their Section behavior to `frontend-section-qa`.

## Simple Logo Card

Use this shape when the CPT has no public detail page and the card mostly displays a featured image with an optional link.

```php
[wst_post_thumbnail size='<image-size>' url='0']

[wst_if field='<link-type-field>' compare='!=' value='no-link']
<a class="wso-absolute-link"
   href="[wst_acf field='<link-url-field>']"
   aria-label="[wst_post_title]"></a>
[/wst_if]
```

Key patterns:

- No public single template is required.
- The featured image carries most of the card presentation.
- The empty link needs an accessible label.
- Final positioning and hover behavior belong to the local frontend phase.

## Standard Detail Card

Use this shape when the CPT has public detail pages and the card links to the single view.

```php
<article class="wso-<resource>-card">
  <a class="wso-<resource>-card-image" href="[wst_post_permalink url='1']">
    [wst_post_thumbnail size='<image-size>']
  </a>

  <div class="wso-<resource>-card-body">
    <h3 class="wso-<resource>-card-title wso-h4">[wst_post_title]</h3>

    [wst_if field='<summary-field>' compare='!=' value='']
    <p class="wso-<resource>-card-summary">[wst_acf field='<summary-field>']</p>
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

## Taxonomy Label Card

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

- The taxonomy exists because it has a card, filtering, or editor purpose.
- The term loop depends on the taxonomy name recorded in Project Context.
- Local frontend work owns final badge styling and responsive behavior.

## Optional Single Template Shell

Use this shell only for CPTs with public detail pages.

```php
<?php if (! defined('ABSPATH')) exit; ?>

<main class="wso-<resource>-single">
  <section class="wso-<resource>-single-hero">
    <h1 class="wso-<resource>-single-title">[wst_post_title]</h1>
    [wst_post_thumbnail size='<image-size>']
  </section>

  <section class="wso-<resource>-single-content">
    [wst_post_excerpt words='5000' fallback='1' html='1']
  </section>
</main>
```

Key patterns:

- Single templates are skipped for non-detail CPTs.
- The shell establishes hooks and semantic regions.
- Final layout, typography, media behavior, and Playwright verification are handed off to Frontend Design QA.
