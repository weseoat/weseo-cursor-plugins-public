<?php

/**
 * Fixture exporter (building block of the Section Preview Harness)
 *
 * Reads the Flexible Content rows of a source page directly from postmeta
 * (= exactly the state that renders on the page) and writes one preview
 * fixture per configured row:
 *
 *   smart-template-builder/section-previews/<section>/<slug>.json
 *
 * The fixture data uses the EXPANDED field names (e.g. intro_content_title);
 * repeaters are translated from the flat meta format (count + indexed keys)
 * into nested arrays - the format acf_setup_meta() expects in the harness.
 *
 * Call (from the WordPress root):
 *   php wp-cli.phar eval-file wp-content/themes/<child-theme>/smart-template-builder/section-previews/_export-fixtures.php <section> [source_page_id]
 *
 * Example:
 *   php wp-cli.phar eval-file .../_export-fixtures.php intro
 *
 * Re-export after a content change: run the same command again. If the source
 * moves later (e.g. test page -> "All Sections"), only adjust source_page +
 * row indexes in the configuration below.
 *
 * NOTE: the config below is a generic example. Replace the placeholder page ID
 * (0), variant slugs, body classes, and Figma references with the project's
 * real values from PROJECT-CONTEXT.md or the active handoff.
 */

if (! defined('ABSPATH')) exit;

/* -----------------------------------------------------------------------------
Configuration: per section the source page and the row-index -> fixture mapping */
$wso_export_config = array(

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

/* -----------------------------------------------------------------------------
Export logic (generic, section-independent)                                    */

/**
 * Recursively translate flat row metas (count + indexed keys) into nested
 * repeater arrays. A repeater root = numeric value + at least one "<key>_0_"
 * sub key.
 */
function wso_export_nest_repeaters(array $flat)
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

			$rows[] = wso_export_nest_repeaters($row);
		}

		$flat[$key] = $rows;
	}

	return $flat;
}

/**
 * Read all metas of an FC row as a flat array of expanded field names.
 */
function wso_export_collect_row($post_id, $index)
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

	return wso_export_nest_repeaters($flat);
}

$section = isset($args[0]) ? sanitize_key($args[0]) : '';

if (! isset($wso_export_config[$section])) {
	echo "Unknown section '{$section}'. Configured: " . implode(', ', array_keys($wso_export_config)) . "\n";
	return;
}

$config  = $wso_export_config[$section];
$page_id = isset($args[1]) ? (int) $args[1] : (int) $config['source_page'];

if ($page_id <= 0) {
	echo "ERROR: no source_page configured for '{$section}'. Set the real page ID in the config or pass it as the second argument.\n";
	return;
}

$layouts = get_post_meta($page_id, 'flexible_content', true);

if (! is_array($layouts) || ! $layouts) {
	echo "ERROR: page {$page_id} has no Flexible Content rows.\n";
	return;
}

$target_dir = wso_preview_fixtures_dir() . '/' . $section;

if (! is_dir($target_dir)) {
	wp_mkdir_p($target_dir);
}

echo "Source: page {$page_id} (" . get_the_title($page_id) . "), " . count($layouts) . " rows\n\n";

foreach ($config['rows'] as $index => $map) {
	// Safety: check the source row's layout and variant against the config.
	if (! isset($layouts[$index]) || $layouts[$index] !== $config['layout']) {
		echo "SKIP row {$index} ({$map['slug']}): layout is '" . ($layouts[$index] ?? 'missing') . "', expected '{$config['layout']}'.\n";
		continue;
	}

	$data = wso_export_collect_row($page_id, $index);

	if (! $data) {
		echo "SKIP row {$index} ({$map['slug']}): no row metas found.\n";
		continue;
	}

	$variant_field = $section . '_variant';

	if (isset($map['expect_variant']) && (($data[$variant_field] ?? '') !== $map['expect_variant'])) {
		echo "SKIP row {$index} ({$map['slug']}): {$variant_field}='" . ($data[$variant_field] ?? '') . "', expected '{$map['expect_variant']}'.\n";
		continue;
	}

	$fixture = array(
		'label'       => $map['label'],
		'description' => sprintf(
			'Exported from "%s" (page %d, row %d) on %s. %s. Re-export: php wp-cli.phar eval-file wp-content/themes/%s/smart-template-builder/section-previews/_export-fixtures.php %s',
			get_the_title($page_id),
			$page_id,
			$index,
			wp_date('Y-m-d'),
			$map['design'],
			get_option('stylesheet'),
			$section
		),
		'template'    => $config['template'],
		'layout'      => $config['layout'],
		'body_class'  => $map['body_class'],
		'data'        => $data,
	);

	$file    = $target_dir . '/' . $map['slug'] . '.json';
	$existed = file_exists($file);

	file_put_contents($file, json_encode($fixture, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

	echo ($existed ? 'UPDATE' : 'NEW   ') . " {$map['slug']}.json (" . count($data) . " fields)\n";
}

echo "\nDone. Preview URLs: /section-preview/{$section}/<slug>/\n";
