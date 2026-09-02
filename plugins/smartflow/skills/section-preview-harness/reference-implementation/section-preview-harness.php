<?php

/**
 * Section Preview Harness
 *
 * Renders single WST Sections in isolation under stable preview URLs:
 *
 *   /section-preview/<section>/<variant>
 *   e.g. /section-preview/intro/default
 *
 * The data source is NOT a WordPress page but a JSON fixture file:
 *
 *   smart-template-builder/section-previews/<section>/<variant>.json
 *
 * The fixture data is registered on a virtual post ID through acf_setup_meta()
 * (ACF Local Meta, the same mechanism ACF Blocks use). That makes
 * have_rows()/get_sub_field() inside the WST Flexible Content loop resolve
 * against the fixture instead of the database - including seamless clone
 * resolution. Important: the keys in "data" are the EXPANDED field names of
 * the layout (e.g. intro_content_title, not title - seamless clones with
 * prefix_name). Derive the expanded names from the ACF JSON sources under
 * the child theme's acf-json/ directory (definitions are tracked files) or
 * from an existing page row over the fixture export route
 * (see _export-fixtures.php).
 *
 * Access protection: reachable only when logged in or on *.weseo.dev, always
 * noindex. On live the route never renders publicly. Adjust the host gate if
 * the project uses a different dev/staging host convention.
 *
 * Loaded through the theme snippet loader (theme-functions.php). Editing that
 * bootstrap file requires explicit user confirmation. After the deploy that
 * first delivers the harness to an environment, flush rewrites once through
 * the status bridge:
 *   POST /wp-json/wso/v1/flush-permalinks
 */

if (! defined('ABSPATH')) exit;


/**
 * Base directory of the fixture files.
 */
function wso_preview_fixtures_dir()
{
	return get_stylesheet_directory() . '/smart-template-builder/section-previews';
}


/**
 * Register the rewrite for /section-preview/<section>/<variant>.
 */
function wso_preview_register_rewrite()
{
	add_rewrite_rule(
		'^section-preview/([^/]+)/([^/]+)/?$',
		'index.php?wso_preview_section=$matches[1]&wso_preview_variant=$matches[2]',
		'top'
	);
}


/**
 * Register the query vars.
 */
function wso_preview_query_vars($vars)
{
	$vars[] = 'wso_preview_section';
	$vars[] = 'wso_preview_variant';

	return $vars;
}


/**
 * Allow access only on dev/staging or when logged in.
 */
function wso_preview_is_allowed_request()
{
	if (is_user_logged_in()) {
		return true;
	}

	$host = $_SERVER['HTTP_HOST'] ?? '';
	$host = strtolower(preg_replace('/:\d+$/', '', sanitize_text_field(wp_unslash($host))));

	return is_string($host) && str_ends_with($host, '.weseo.dev');
}


/**
 * Resolve the preview context from URL + fixture file.
 *
 * Unknown section/variant (= no fixture file) returns null -> 404.
 */
function wso_preview_get_context()
{
	static $context = false;

	if ($context !== false) {
		return $context;
	}

	$context = null;

	$section = sanitize_key(get_query_var('wso_preview_section'));
	$variant = sanitize_key(get_query_var('wso_preview_variant'));

	if (empty($section) || empty($variant)) {
		return $context;
	}

	$fixture_file = wso_preview_fixtures_dir() . '/' . $section . '/' . $variant . '.json';

	if (! file_exists($fixture_file)) {
		return $context;
	}

	$fixture = json_decode((string) file_get_contents($fixture_file), true);

	if (! is_array($fixture) || empty($fixture['template']) || empty($fixture['layout']) || ! isset($fixture['data']) || ! is_array($fixture['data'])) {
		return $context;
	}

	$context = array(
		'section' => $section,
		'variant' => $variant,
		'fixture' => $fixture,
	);

	return $context;
}


/**
 * Do not classify preview requests as the blog home.
 *
 * A virtual route with no posts falls back to is_home in WP_Query. The WST
 * plugin 404s is_home when the post archive is disabled (option
 * wst_post_disable_post_archive_page) - so the classification must be
 * corrected at the source.
 */
function wso_preview_parse_query($wp_query)
{
	if ($wp_query->is_main_query() && ! empty($wp_query->query_vars['wso_preview_section'])) {
		$wp_query->is_home = false;
	}
}


/**
 * Preview requests never load real posts.
 *
 * Without this, the virtual route behaves like the blog index: the main query
 * loads the latest posts and WordPress registers the first one as the global
 * $post - its thumbnail/title would leak into the preview through the template
 * fallbacks ([wst_post_thumbnail]/[wst_post_title]). An empty query keeps the
 * preview context deterministic.
 */
function wso_preview_pre_get_posts($wp_query)
{
	if ($wp_query->is_main_query() && ! empty($wp_query->query_vars['wso_preview_section'])) {
		$wp_query->set('post__in', array(0));
		$wp_query->set('no_found_rows', true);
	}
}


/**
 * Never treat a valid preview as a 404.
 *
 * Besides the 404 status, this also prevents the WST 404 redirect from setting
 * a foreign global $post (whose title/thumbnail would otherwise leak into the
 * preview through the [wst_post_title]/[wst_post_thumbnail] fallbacks, e.g. on
 * the no-image variant).
 */
function wso_preview_pre_handle_404($preempt, $wp_query)
{
	if (empty(get_query_var('wso_preview_section'))) {
		return $preempt;
	}

	if (wso_preview_get_context() && wso_preview_is_allowed_request()) {
		return true;
	}

	return $preempt;
}


/**
 * Route the harness template (virtual route, no WP page).
 */
function wso_preview_template_include($template)
{
	if (empty(get_query_var('wso_preview_section'))) {
		return $template;
	}

	$context = wso_preview_get_context();

	if (! $context || ! wso_preview_is_allowed_request()) {
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
		nocache_headers();

		return get_404_template() ?: $template;
	}

	// QA infrastructure: never index, never cache (WP Rocket + browser).
	header('X-Robots-Tag: noindex, nofollow', true);
	nocache_headers();

	if (! defined('DONOTCACHEPAGE')) {
		define('DONOTCACHEPAGE', true);
	}

	return get_stylesheet_directory() . '/smart-template-builder/section-previews/_preview-template.php';
}


/**
 * Body classes: stable QA hook + optional classes from the fixture
 * (e.g. brand-a for company palettes).
 */
function wso_preview_body_class($classes)
{
	$context = wso_preview_get_context();

	if (! $context) {
		return $classes;
	}

	$classes[] = 'wso-section-preview';

	if (! empty($context['fixture']['body_class'])) {
		foreach (explode(' ', (string) $context['fixture']['body_class']) as $class) {
			$class = sanitize_html_class($class);

			if ($class !== '') {
				$classes[] = $class;
			}
		}
	}

	return $classes;
}


/**
 * Resolve the Flexible Content field of the pages.
 *
 * Important: there are several ACF fields named "flexible_content" (page
 * builder, flexblocks, multi-columns). An acf_get_field('flexible_content') by
 * name would return the wrong field. So the field is resolved through the
 * field group assigned to post type "page" - exactly the field real pages
 * render against.
 */
function wso_preview_get_fc_field()
{
	foreach (acf_get_field_groups(array('post_type' => 'page')) as $group) {
		foreach ((array) acf_get_fields($group) as $field) {
			if ($field['type'] === 'flexible_content' && $field['name'] === 'flexible_content') {
				return $field;
			}
		}
	}

	return null;
}


/**
 * Render the Section with fixture data through the real WST render path.
 *
 * Exactly one Flexible Content row is simulated and only the Section template
 * belonging to the fixture is included - exactly like on a real page.
 */
function wso_preview_render_section()
{
	$context = wso_preview_get_context();

	if (! $context || ! function_exists('wst_do_shortcode') || ! function_exists('acf_setup_meta')) {
		return '';
	}

	$fixture = $context['fixture'];

	$fc_field = wso_preview_get_fc_field();

	if (! $fc_field) {
		return '';
	}

	$row = array_merge(
		array('acf_fc_layout' => $fixture['layout']),
		$fixture['data']
	);

	$preview_id = 'wso_preview_' . $context['section'] . '_' . $context['variant'];

	// Provide the fixture as virtual ACF meta (no DB writes).
	acf_setup_meta(array($fc_field['key'] => array($row)), $preview_id, true);

	$template = sprintf(
		'[wst_acf_flexible_content field="flexible_content" id="%s"][wst_include template="%s" layout="%s"][/wst_acf_flexible_content]',
		esc_attr($preview_id),
		esc_attr($fixture['template']),
		esc_attr($fixture['layout'])
	);

	$html = wst_do_shortcode($template);

	acf_reset_meta($preview_id);

	return $html;
}


/* -----------------------------------------------------------------------------
Hooks (the include is activated through theme-functions.php -> snippet loader) */
add_action('init', 'wso_preview_register_rewrite');
add_filter('query_vars', 'wso_preview_query_vars');
add_action('parse_query', 'wso_preview_parse_query');
add_action('pre_get_posts', 'wso_preview_pre_get_posts');
add_filter('pre_handle_404', 'wso_preview_pre_handle_404', 10, 2);
add_filter('template_include', 'wso_preview_template_include');
add_filter('body_class', 'wso_preview_body_class');

// Fixture export route (wso-preview/v1/export/<section>); see _export-fixtures.php.
$wso_preview_exporter = __DIR__ . '/section-previews/_export-fixtures.php';

if (file_exists($wso_preview_exporter)) {
	require_once $wso_preview_exporter;
}
