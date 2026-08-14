<?php

/**
 * Section Preview Harness - page template
 *
 * Renders get_header() + exactly ONE WST Section from fixture data +
 * get_footer(). The real theme context (Astra/child-theme CSS, cascade, brand
 * tokens) is preserved; only the content area is isolated.
 *
 * THEME ASSUMPTION: this template targets an Astra child theme and calls
 * astra_primary_class() / astra_primary_content_top() /
 * astra_primary_content_bottom() directly. On a non-Astra theme, replace these
 * with the active theme's primary-content wrapper and hooks, but keep the
 * #primary element plus the data-preview-section / data-preview-variant
 * attributes - the QA hooks depend on them.
 *
 * Loaded exclusively through wso_preview_template_include()
 * (see smart-template-builder/section-preview-harness.php).
 */
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$wso_preview = wso_preview_get_context();

get_header(); ?>
<div id="primary" <?php astra_primary_class(); ?> data-preview-section="<?php echo esc_attr($wso_preview['section']); ?>" data-preview-variant="<?php echo esc_attr($wso_preview['variant']); ?>">
	<?php
	astra_primary_content_top();

	echo wso_preview_render_section();

	astra_primary_content_bottom();
	?>
</div><!-- #primary -->
<?php get_footer(); ?>
