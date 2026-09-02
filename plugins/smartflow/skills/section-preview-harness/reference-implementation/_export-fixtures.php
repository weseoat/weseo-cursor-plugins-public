<?php

/**
 * Fixture export route (building block of the Section Preview Harness)
 *
 * Reads the Flexible Content rows of a source page directly from postmeta
 * (= exactly the state that renders on the page) and RETURNS one preview
 * fixture per configured row over a gated REST route. It never writes files
 * on the server: fixtures are tracked repository source. The local agent
 * fetches this route, writes each returned fixture to
 *
 *   smart-template-builder/section-previews/<section>/<slug>.json
 *
 * in the repository, commits, and the next deploy delivers the fixtures with
 * the child theme.
 *
 * Route (registered on rest_api_init, loaded via section-preview-harness.php):
 *
 *   GET /wp-json/wso-preview/v1/export/<section>[?source_page=<id>]
 *
 * Auth: manage_options, e.g. the application password from the project .env:
 *
 *   curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
 *     "<site-url>/wp-json/wso-preview/v1/export/intro"
 *
 * The fixture data uses the EXPANDED field names (e.g. intro_content_title);
 * repeaters are translated from the flat meta format (count + indexed keys)
 * into nested arrays - the format acf_setup_meta() expects in the harness.
 *
 * Re-export after a content change: fetch the same route again and rewrite
 * the repository fixtures. If the source moves later (e.g. test page ->
 * "All Sections"), only adjust source_page + row indexes in the configuration
 * below, commit, and deploy before re-exporting.
 *
 * NOTE: the config below is a generic example. Replace the placeholder page ID
 * (0), variant slugs, body classes, and Figma references with the project's
 * real values from PROJECT-CONTEXT.md or the project docs layer.
 */

if (! defined('ABSPATH')) exit;

/**
 * Configuration: per section the source page and the row-index -> fixture
 * mapping.
 */
function wso_preview_export_config()
{
	return array(

		'intro' => array(
			'source_page' => 0, // <source-page-id>: the page that holds one FC row per variant
			'template'    => 'sections/intro.php',
			'layout'      => 'layout_intro',
			'rows'        => array(
				2 => array(
					'slug'           => 'split',
					'label'          => 'Split',
					'body_class'     => '',
					'expect_variant' => 'split',
					'design'         => 'Figma <desktop-node-id> / <mobile-node-id>',
				),
				3 => array(
					'slug'           => 'split-brand-a',
					'label'          => 'Split - Brand A',
					'body_class'     => 'brand-a',
					'expect_variant' => 'split',
					'design'         => 'Figma <desktop-node-id> / <mobile-node-id>',
				),
				4 => array(
					'slug'           => 'centered-image',
					'label'          => 'Centered Image',
					'body_class'     => '',
					'expect_variant' => 'centered-image',
					'design'         => 'Figma <desktop-node-id>',
				),
				5 => array(
					'slug'           => 'fullscreen-image',
					'label'          => 'Fullscreen Image (no text)',
					'body_class'     => '',
					'expect_variant' => 'fullscreen-image',
					'design'         => 'Figma <desktop-node-id> / <mobile-node-id>',
				),
				6 => array(
					'slug'           => 'text-only',
					'label'          => 'Text Only (no image)',
					'body_class'     => '',
					'expect_variant' => 'text-only',
					'design'         => 'Figma <desktop-node-id> / <mobile-node-id>',
				),
			),
		),

	);
}

/* -----------------------------------------------------------------------------
Export logic (generic, section-independent)                                    */

/**
 * Recursively translate flat row metas (count + indexed keys) into nested
 * repeater arrays. A repeater root = numeric value + at least one "<key>_0_"
 * sub key.
 */
function wso_preview_export_nest_repeaters(array $flat)
{
	foreach (array_keys($flat) as $key) {
		if (! isset($flat[$key]) || ! is_numeric($flat[$key])) {
			continue;
		}

		$has_subs = false;

		foreach ($flat as $other => $unused) {
			if (strpos($other, $key . '_0_') === 0) {
				$has_subs = true;
				break;
			}
		}

		if (! $has_subs) {
			continue;
		}

		$rows = array();

		for ($i = 0; $i < (int) $flat[$key]; $i++) {
			$prefix = $key . '_' . $i . '_';
			$row    = array();

			foreach (array_keys($flat) as $other) {
				if (strpos($other, $prefix) === 0) {
					$row[substr($other, strlen($prefix))] = $flat[$other];
					unset($flat[$other]);
				}
			}

			$rows[] = wso_preview_export_nest_repeaters($row);
		}

		$flat[$key] = $rows;
	}

	return $flat;
}

/**
 * Read all metas of an FC row as a flat array of expanded field names.
 */
function wso_preview_export_collect_row($post_id, $index)
{
	global $wpdb;

	$prefix = 'flexible_content_' . $index . '_';
	$metas  = $wpdb->get_results($wpdb->prepare(
		"SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s ORDER BY meta_key",
		$post_id,
		$wpdb->esc_like($prefix) . '%'
	));

	$flat = array();

	foreach ($metas as $meta) {
		$flat[substr($meta->meta_key, strlen($prefix))] = maybe_unserialize($meta->meta_value);
	}

	return wso_preview_export_nest_repeaters($flat);
}

/**
 * Handle GET /wso-preview/v1/export/<section>.
 */
function wso_preview_export_handler($request)
{
	$config_all = wso_preview_export_config();
	$section    = sanitize_key($request['section']);

	if (! isset($config_all[$section])) {
		return new WP_Error('wso_preview_unknown_section', sprintf(
			'Unknown section "%s". Configured: %s',
			$section,
			implode(', ', array_keys($config_all))
		), array('status' => 404));
	}

	$config  = $config_all[$section];
	$page_id = (int) ($request->get_param('source_page') ?: $config['source_page']);

	if ($page_id <= 0) {
		return new WP_Error('wso_preview_no_source_page', sprintf(
			'No source_page configured for "%s". Set the real page ID in the config or pass ?source_page=<id>.',
			$section
		), array('status' => 400));
	}

	$layouts = get_post_meta($page_id, 'flexible_content', true);

	if (! is_array($layouts) || ! $layouts) {
		return new WP_Error('wso_preview_no_rows', sprintf(
			'Page %d has no Flexible Content rows.',
			$page_id
		), array('status' => 400));
	}

	$fixtures = array();
	$skipped  = array();

	foreach ($config['rows'] as $index => $map) {
		// Safety: check the source row's layout and variant against the config.
		if (! isset($layouts[$index]) || $layouts[$index] !== $config['layout']) {
			$skipped[] = array(
				'row'    => $index,
				'slug'   => $map['slug'],
				'reason' => sprintf('layout is "%s", expected "%s"', $layouts[$index] ?? 'missing', $config['layout']),
			);
			continue;
		}

		$data = wso_preview_export_collect_row($page_id, $index);

		if (! $data) {
			$skipped[] = array('row' => $index, 'slug' => $map['slug'], 'reason' => 'no row metas found');
			continue;
		}

		$variant_field = $section . '_variant';

		if (isset($map['expect_variant']) && (($data[$variant_field] ?? '') !== $map['expect_variant'])) {
			$skipped[] = array(
				'row'    => $index,
				'slug'   => $map['slug'],
				'reason' => sprintf('%s="%s", expected "%s"', $variant_field, $data[$variant_field] ?? '', $map['expect_variant']),
			);
			continue;
		}

		$fixtures[$map['slug']] = array(
			'label'       => $map['label'],
			'description' => sprintf(
				'Exported from "%s" (page %d, row %d) on %s. %s. Re-export: GET /wp-json/wso-preview/v1/export/%s',
				get_the_title($page_id),
				$page_id,
				$index,
				wp_date('Y-m-d'),
				$map['design'],
				$section
			),
			'template'    => $config['template'],
			'layout'      => $config['layout'],
			'body_class'  => $map['body_class'],
			'data'        => $data,
		);
	}

	return rest_ensure_response(array(
		'section'      => $section,
		'source_page'  => $page_id,
		'source_title' => get_the_title($page_id),
		'source_rows'  => count($layouts),
		'fixtures'     => $fixtures,
		'skipped'      => $skipped,
	));
}

/**
 * Register the export route.
 */
function wso_preview_export_register_route()
{
	register_rest_route('wso-preview/v1', '/export/(?P<section>[a-z0-9_-]+)', array(
		'methods'             => 'GET',
		'callback'            => 'wso_preview_export_handler',
		'permission_callback' => function () {
			return current_user_can('manage_options');
		},
	));
}

add_action('rest_api_init', 'wso_preview_export_register_route');
