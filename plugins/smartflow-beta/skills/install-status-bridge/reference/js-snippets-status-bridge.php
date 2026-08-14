<?php
/**
 * SmartFlow status bridge template for the child theme's js-snippets.php.
 *
 * This file is the canonical template bundled with the smartflow plugin.
 * Install or update it only through the `install-status-bridge` Skill: copy
 * the managed block below (from `WSO STATUS BRIDGE BEGIN` to
 * `WSO STATUS BRIDGE END`, markers included) into the project's
 * js-snippets.php. Never hand-edit the installed block; update it by
 * replacing the whole block from this template.
 *
 * The bridge registers a versioned REST API under the `wso/v1` namespace:
 *
 * - GET  /wp-json/wso/v1/status            deploy and configuration status
 * - POST /wp-json/wso/v1/flush-cache       object cache and page cache flush
 * - POST /wp-json/wso/v1/flush-permalinks  rewrite rules flush
 *
 * Every route requires an authenticated user with the `manage_options`
 * capability (use a WordPress application password).
 */

/* WSO STATUS BRIDGE BEGIN v1.0.0 -- managed block, update only through the install-status-bridge Skill */

if ( ! defined( 'WSO_BRIDGE_VERSION' ) ) {
	define( 'WSO_BRIDGE_VERSION', '1.0.0' );
}

if ( ! function_exists( 'wso_bridge_permission_check' ) ) {
	/**
	 * Every bridge route requires an authenticated user with manage_options.
	 */
	function wso_bridge_permission_check() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return new WP_Error(
			'wso_bridge_forbidden',
			'The SmartFlow status bridge requires an authenticated user with the manage_options capability.',
			array( 'status' => rest_authorization_required_code() )
		);
	}
}

if ( ! function_exists( 'wso_bridge_deployed_commit' ) ) {
	/**
	 * The project deploy path writes the hash of the deployed commit into
	 * `.wso-deployed-commit` in the child theme root. Returns null when the
	 * file is missing or malformed, so the agent-side deploy verification
	 * fails loudly instead of passing on stale data.
	 */
	function wso_bridge_deployed_commit() {
		$file = trailingslashit( get_stylesheet_directory() ) . '.wso-deployed-commit';

		if ( ! is_readable( $file ) ) {
			return null;
		}

		$hash = trim( (string) file_get_contents( $file ) );

		return preg_match( '/^[0-9a-f]{7,40}$/i', $hash ) ? $hash : null;
	}
}

if ( ! function_exists( 'wso_bridge_acf_field_groups' ) ) {
	/**
	 * Read-only list of registered ACF field groups. `local` is `php` or
	 * `json` for code-registered groups and false for database groups, which
	 * lets the agent verify that PHP-registered groups actually arrived.
	 */
	function wso_bridge_acf_field_groups() {
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return array(
				'available' => false,
				'groups'    => array(),
			);
		}

		$groups = array();

		foreach ( acf_get_field_groups() as $group ) {
			$groups[] = array(
				'key'    => isset( $group['key'] ) ? $group['key'] : null,
				'title'  => isset( $group['title'] ) ? $group['title'] : null,
				'local'  => isset( $group['local'] ) ? $group['local'] : false,
				'active' => ! isset( $group['active'] ) || (bool) $group['active'],
			);
		}

		return array(
			'available' => true,
			'groups'    => $groups,
		);
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_grids' ) ) {
	/**
	 * Read-only list of WP Grid Builder grids from the plugin's grid table.
	 */
	function wso_bridge_wpgb_grids() {
		global $wpdb;

		if ( ! defined( 'WPGB_VERSION' ) && ! function_exists( 'wpgb' ) ) {
			return array(
				'available' => false,
				'grids'     => array(),
			);
		}

		$table = $wpdb->prefix . 'wpgb_grids';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
			return array(
				'available' => true,
				'grids'     => array(),
			);
		}

		$rows = $wpdb->get_results( "SELECT id, name FROM {$table} ORDER BY id ASC", ARRAY_A );

		if ( $wpdb->last_error ) {
			return array(
				'available' => true,
				'grids'     => array(),
				'error'     => $wpdb->last_error,
			);
		}

		$grids = array();

		foreach ( (array) $rows as $row ) {
			$grids[] = array(
				'id'   => isset( $row['id'] ) ? (int) $row['id'] : null,
				'name' => isset( $row['name'] ) ? $row['name'] : null,
			);
		}

		return array(
			'available' => true,
			'grids'     => $grids,
		);
	}
}

if ( ! function_exists( 'wso_bridge_cache_state' ) ) {
	/**
	 * Cache overview: external object cache, detected page cache plugins,
	 * and the timestamps of the last flushes performed through the bridge.
	 */
	function wso_bridge_cache_state() {
		$plugins = array();

		if ( defined( 'LSCWP_V' ) ) {
			$plugins[] = 'litespeed-cache';
		}
		if ( defined( 'WP_ROCKET_VERSION' ) ) {
			$plugins[] = 'wp-rocket';
		}
		if ( defined( 'W3TC' ) ) {
			$plugins[] = 'w3-total-cache';
		}
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			$plugins[] = 'wp-super-cache';
		}
		if ( class_exists( 'autoptimizeCache' ) ) {
			$plugins[] = 'autoptimize';
		}
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			$plugins[] = 'sg-cachepress';
		}

		return array(
			'external_object_cache' => (bool) wp_using_ext_object_cache(),
			'cache_plugins'         => $plugins,
			'last_cache_flush'      => get_option( 'wso_bridge_last_cache_flush', null ),
			'last_permalink_flush'  => get_option( 'wso_bridge_last_permalink_flush', null ),
		);
	}
}

if ( ! function_exists( 'wso_bridge_status_handler' ) ) {
	function wso_bridge_status_handler() {
		return rest_ensure_response(
			array(
				'bridge_version'  => WSO_BRIDGE_VERSION,
				'deployed_commit' => wso_bridge_deployed_commit(),
				'acf'             => wso_bridge_acf_field_groups(),
				'wpgb'            => wso_bridge_wpgb_grids(),
				'cache'           => wso_bridge_cache_state(),
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_flush_cache_handler' ) ) {
	/**
	 * Flush the object cache plus every detected page cache, best effort.
	 */
	function wso_bridge_flush_cache_handler() {
		$flushed = array();

		if ( wp_cache_flush() ) {
			$flushed[] = 'object-cache';
		}

		if ( defined( 'LSCWP_V' ) ) {
			do_action( 'litespeed_purge_all' );
			$flushed[] = 'litespeed-cache';
		}

		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
			$flushed[] = 'wp-rocket';
		}

		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
			$flushed[] = 'w3-total-cache';
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
			$flushed[] = 'wp-super-cache';
		}

		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			autoptimizeCache::clearall();
			$flushed[] = 'autoptimize';
		}

		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
			$flushed[] = 'sg-cachepress';
		}

		$timestamp = gmdate( 'c' );
		update_option( 'wso_bridge_last_cache_flush', $timestamp, false );

		return rest_ensure_response(
			array(
				'flushed'    => $flushed,
				'flushed_at' => $timestamp,
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_flush_permalinks_handler' ) ) {
	/**
	 * Soft rewrite-rules flush: regenerates the rules without rewriting
	 * .htaccess, which is what CPT and taxonomy registrations need.
	 */
	function wso_bridge_flush_permalinks_handler() {
		flush_rewrite_rules( false );

		$timestamp = gmdate( 'c' );
		update_option( 'wso_bridge_last_permalink_flush', $timestamp, false );

		return rest_ensure_response(
			array(
				'flushed'    => array( 'rewrite-rules' ),
				'flushed_at' => $timestamp,
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_register_routes' ) ) {
	function wso_bridge_register_routes() {
		register_rest_route(
			'wso/v1',
			'/status',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'wso_bridge_status_handler',
				'permission_callback' => 'wso_bridge_permission_check',
			)
		);

		register_rest_route(
			'wso/v1',
			'/flush-cache',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wso_bridge_flush_cache_handler',
				'permission_callback' => 'wso_bridge_permission_check',
			)
		);

		register_rest_route(
			'wso/v1',
			'/flush-permalinks',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wso_bridge_flush_permalinks_handler',
				'permission_callback' => 'wso_bridge_permission_check',
			)
		);
	}

	add_action( 'rest_api_init', 'wso_bridge_register_routes' );
}

/* WSO STATUS BRIDGE END */
