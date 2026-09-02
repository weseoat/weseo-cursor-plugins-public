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
 * - GET  /wp-json/wso/v1/wpgb/<type>       list WPGB items; <type> is grids|cards|facets
 * - GET  /wp-json/wso/v1/wpgb/<type>/<id>  full item configuration, exporter-compatible JSON
 * - POST /wp-json/wso/v1/wpgb/<type>       create a WPGB item (unique name/slug required)
 * - POST /wp-json/wso/v1/wpgb/<type>/<id>  update a WPGB item
 * - POST /wp-json/wso/v1/wpgb/reindex      reindex WPGB facets (all, or one via facet_id)
 *
 * The wpgb routes wrap undocumented WP Grid Builder internals. On every
 * WPGB version: Includes\Database::save_row()/query_row() and
 * Includes\Indexer. The single-item GET additionally uses Admin\Export on
 * WPGB 1.x where that class exists; WPGB 2.x removed it, so the GET falls
 * back to Database::query_row() with the settings/layout columns
 * JSON-decoded. When an internal class or method is missing -- plugin
 * inactive or internals drifted after a WPGB update -- the routes answer
 * with a clear error carrying `wpgb_version` instead of guessing.
 *
 * Every route requires an authenticated user with the `manage_options`
 * capability (use a WordPress application password).
 */

/* WSO STATUS BRIDGE BEGIN v1.1.1 -- managed block, update only through the install-status-bridge Skill */

if ( ! defined( 'WSO_BRIDGE_VERSION' ) ) {
	define( 'WSO_BRIDGE_VERSION', '1.1.1' );
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

if ( ! function_exists( 'wso_bridge_wpgb_version' ) ) {
	/**
	 * WP Grid Builder version for drift diagnosis, null when not active.
	 */
	function wso_bridge_wpgb_version() {
		return defined( 'WPGB_VERSION' ) ? WPGB_VERSION : null;
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_internal_missing' ) ) {
	/**
	 * The wpgb routes rely on undocumented WP Grid Builder internals. When a
	 * class or method is missing (plugin inactive, or internals drifted after
	 * a WPGB update), answer with a clear error instead of guessing.
	 */
	function wso_bridge_wpgb_internal_missing( $missing ) {
		return new WP_Error(
			'wso_bridge_wpgb_internal_missing',
			sprintf( 'WPGB internal API not available: %s. WP Grid Builder is inactive or its internals changed after a plugin update.', $missing ),
			array(
				'status'       => 501,
				'wpgb_version' => wso_bridge_wpgb_version(),
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_identifier' ) ) {
	/**
	 * Unique identifier column per WPGB item type: facets are addressed by
	 * `slug`, grids and cards by `name`.
	 */
	function wso_bridge_wpgb_identifier( $type ) {
		return 'facets' === $type ? 'slug' : 'name';
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_decode_json_columns' ) ) {
	/**
	 * Decode the settings and layout columns of a WPGB row for the response.
	 * Only valid JSON arrays/objects are decoded; other strings stay as they
	 * are. css is never decoded: save_row() re-encodes only settings/layout,
	 * so a decoded css would break the GET -> POST round trip.
	 */
	function wso_bridge_wpgb_decode_json_columns( $row ) {
		foreach ( array( 'settings', 'layout' ) as $column ) {
			if ( empty( $row[ $column ] ) || ! is_string( $row[ $column ] ) ) {
				continue;
			}

			$decoded = json_decode( $row[ $column ], true );

			if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
				$row[ $column ] = $decoded;
			}
		}

		return $row;
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_list_handler' ) ) {
	/**
	 * GET /wpgb/<grids|cards|facets>: id and name (plus slug for facets) of
	 * every item, read from the WPGB custom table.
	 */
	function wso_bridge_wpgb_list_handler( $request ) {
		global $wpdb;

		$params = $request->get_url_params();
		$type   = $params['type'];

		if ( ! defined( 'WPGB_VERSION' ) && ! function_exists( 'wpgb' ) ) {
			return wso_bridge_wpgb_internal_missing( 'WP Grid Builder plugin' );
		}

		$table = $wpdb->prefix . 'wpgb_' . $type;

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
			return wso_bridge_wpgb_internal_missing( 'table ' . $table );
		}

		$columns = 'facets' === $type ? 'id, name, slug' : 'id, name';
		$rows    = $wpdb->get_results( "SELECT {$columns} FROM {$table} ORDER BY id ASC", ARRAY_A );

		if ( $wpdb->last_error ) {
			return new WP_Error(
				'wso_bridge_wpgb_query_failed',
				$wpdb->last_error,
				array(
					'status'       => 500,
					'wpgb_version' => wso_bridge_wpgb_version(),
				)
			);
		}

		$items = array();

		foreach ( (array) $rows as $row ) {
			$item = array(
				'id'   => isset( $row['id'] ) ? (int) $row['id'] : null,
				'name' => isset( $row['name'] ) ? $row['name'] : null,
			);

			if ( 'facets' === $type ) {
				$item['slug'] = isset( $row['slug'] ) ? $row['slug'] : null;
			}

			$items[] = $item;
		}

		return rest_ensure_response(
			array(
				'wpgb_version' => wso_bridge_wpgb_version(),
				'type'         => $type,
				'items'        => $items,
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_get_handler' ) ) {
	/**
	 * GET /wpgb/<type>/<id>: full configuration of one item as decoded JSON.
	 * On WPGB 1.x the admin exporter produces the row; WPGB 2.x removed the
	 * Export class, so the row is read through Database::query_row() and the
	 * settings/layout columns are JSON-decoded. Both paths return the same
	 * shape, and a body in this shape can be POSTed back for the round trip.
	 */
	function wso_bridge_wpgb_get_handler( $request ) {
		$params = $request->get_url_params();
		$type   = $params['type'];
		$id     = (int) $params['id'];

		// WPGB 1.x: the admin exporter still exists there, keep using it.
		if ( class_exists( 'WP_Grid_Builder\Admin\Export' ) ) {
			$export = new WP_Grid_Builder\Admin\Export();

			if ( method_exists( $export, 'export_items' ) ) {
				$exported = (array) $export->export_items(
					array(
						'action' => 'wpgb_export',
						'page'   => 'wpgb-' . $type,
						'type'   => $type,
						'ids'    => $id,
					)
				);

				if ( empty( $exported[ $type ] ) ) {
					return new WP_Error(
						'wso_bridge_wpgb_not_found',
						sprintf( 'No %s item with id %d.', $type, $id ),
						array(
							'status'       => 404,
							'wpgb_version' => wso_bridge_wpgb_version(),
						)
					);
				}

				return rest_ensure_response(
					array(
						'wpgb_version' => wso_bridge_wpgb_version(),
						'type'         => $type,
						'id'           => $id,
						$type          => array_values( (array) $exported[ $type ] ),
					)
				);
			}
		}

		// WPGB 2.x: no exporter; read the row through the same documented
		// internal the save path already relies on.
		if ( ! class_exists( 'WP_Grid_Builder\Includes\Database' )
			|| ! method_exists( 'WP_Grid_Builder\Includes\Database', 'query_row' ) ) {
			return wso_bridge_wpgb_internal_missing( 'WP_Grid_Builder\Includes\Database::query_row()' );
		}

		$row = WP_Grid_Builder\Includes\Database::query_row(
			array(
				'from' => $type,
				'id'   => $id,
			)
		);

		if ( ! is_array( $row ) || empty( $row['id'] ) ) {
			return new WP_Error(
				'wso_bridge_wpgb_not_found',
				sprintf( 'No %s item with id %d.', $type, $id ),
				array(
					'status'       => 404,
					'wpgb_version' => wso_bridge_wpgb_version(),
				)
			);
		}

		return rest_ensure_response(
			array(
				'wpgb_version' => wso_bridge_wpgb_version(),
				'type'         => $type,
				'id'           => $id,
				$type          => array( wso_bridge_wpgb_decode_json_columns( $row ) ),
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_save_handler' ) ) {
	/**
	 * POST /wpgb/<type> creates an item (no id in the URL, unique name/slug
	 * required); POST /wpgb/<type>/<id> updates an existing one. The payload
	 * is either the exporter shape returned by the GET route or the bare row
	 * object. settings/layout are re-encoded to JSON strings for the WPGB
	 * tables and the row is persisted via Database::save_row().
	 */
	function wso_bridge_wpgb_save_handler( $request ) {
		$params = $request->get_url_params();
		$type   = $params['type'];
		$id     = isset( $params['id'] ) ? (int) $params['id'] : 0;

		if ( ! class_exists( 'WP_Grid_Builder\Includes\Database' )
			|| ! method_exists( 'WP_Grid_Builder\Includes\Database', 'save_row' )
			|| ! method_exists( 'WP_Grid_Builder\Includes\Database', 'query_row' ) ) {
			return wso_bridge_wpgb_internal_missing( 'WP_Grid_Builder\Includes\Database::save_row()/query_row()' );
		}

		$payload = $request->get_json_params();

		if ( ! is_array( $payload ) || empty( $payload ) ) {
			return new WP_Error(
				'wso_bridge_wpgb_invalid_payload',
				'Send the item as JSON: either the exporter shape {"<type>":[{...}]} returned by the GET route, or the bare row object.',
				array( 'status' => 400 )
			);
		}

		if ( isset( $payload[ $type ] ) && is_array( $payload[ $type ] ) ) {
			$row = (array) reset( $payload[ $type ] );
		} else {
			$row = $payload;
		}

		foreach ( array( 'settings', 'layout' ) as $column ) {
			if ( isset( $row[ $column ] ) && ! is_string( $row[ $column ] ) ) {
				$row[ $column ] = wp_json_encode( $row[ $column ] );
			}
		}

		unset( $row['id'] );

		$identifier = wso_bridge_wpgb_identifier( $type );

		if ( 0 === $id ) {
			if ( empty( $row[ $identifier ] ) || ! is_string( $row[ $identifier ] ) ) {
				return new WP_Error(
					'wso_bridge_wpgb_missing_identifier',
					sprintf( 'Creating a %s item requires a non-empty "%s" in the payload.', $type, $identifier ),
					array( 'status' => 400 )
				);
			}

			$existing = WP_Grid_Builder\Includes\Database::query_row(
				array(
					'select'    => 'id',
					'from'      => $type,
					$identifier => $row[ $identifier ],
				)
			);

			if ( ! empty( $existing['id'] ) ) {
				return new WP_Error(
					'wso_bridge_wpgb_identifier_collision',
					sprintf( 'A %s item with %s "%s" already exists (id %d). Update it via POST /wpgb/%s/%d or choose a unique %s.', $type, $identifier, $row[ $identifier ], (int) $existing['id'], $type, (int) $existing['id'], $identifier ),
					array(
						'status'      => 409,
						'existing_id' => (int) $existing['id'],
					)
				);
			}
		} else {
			$existing = WP_Grid_Builder\Includes\Database::query_row(
				array(
					'select' => 'id',
					'from'   => $type,
					'id'     => $id,
				)
			);

			if ( empty( $existing['id'] ) ) {
				return new WP_Error(
					'wso_bridge_wpgb_not_found',
					sprintf( 'No %s item with id %d.', $type, $id ),
					array(
						'status'       => 404,
						'wpgb_version' => wso_bridge_wpgb_version(),
					)
				);
			}
		}

		WP_Grid_Builder\Includes\Database::save_row( $type, $row, $id );

		$created = ( 0 === $id );

		if ( $created ) {
			$saved = WP_Grid_Builder\Includes\Database::query_row(
				array(
					'select'    => 'id',
					'from'      => $type,
					$identifier => $row[ $identifier ],
				)
			);

			if ( empty( $saved['id'] ) ) {
				return new WP_Error(
					'wso_bridge_wpgb_create_failed',
					sprintf( 'save_row() did not persist the new %s item; compare the payload columns against a GET of a working item.', $type ),
					array(
						'status'       => 500,
						'wpgb_version' => wso_bridge_wpgb_version(),
					)
				);
			}

			$id = (int) $saved['id'];
		}

		return rest_ensure_response(
			array(
				'wpgb_version' => wso_bridge_wpgb_version(),
				'type'         => $type,
				'id'           => $id,
				'created'      => $created,
				'saved_at'     => gmdate( 'c' ),
			)
		);
	}
}

if ( ! function_exists( 'wso_bridge_wpgb_reindex_handler' ) ) {
	/**
	 * POST /wpgb/reindex: run the WPGB facet indexer, for all facets or for
	 * one facet when the JSON body carries a `facet_id`.
	 */
	function wso_bridge_wpgb_reindex_handler( $request ) {
		if ( ! class_exists( 'WP_Grid_Builder\Includes\Indexer' ) ) {
			return wso_bridge_wpgb_internal_missing( 'WP_Grid_Builder\Includes\Indexer' );
		}

		$indexer = new WP_Grid_Builder\Includes\Indexer();

		if ( ! method_exists( $indexer, 'index_facets' ) ) {
			return wso_bridge_wpgb_internal_missing( 'WP_Grid_Builder\Includes\Indexer::index_facets()' );
		}

		$params   = (array) $request->get_json_params();
		$facet_id = isset( $params['facet_id'] ) ? (int) $params['facet_id'] : 0;

		$indexer->index_facets( $facet_id > 0 ? array( $facet_id ) : -1 );

		return rest_ensure_response(
			array(
				'wpgb_version' => wso_bridge_wpgb_version(),
				'reindexed'    => $facet_id > 0 ? array( $facet_id ) : 'all',
				'reindexed_at' => gmdate( 'c' ),
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

		register_rest_route(
			'wso/v1',
			'/wpgb/(?P<type>grids|cards|facets)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => 'wso_bridge_wpgb_list_handler',
					'permission_callback' => 'wso_bridge_permission_check',
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => 'wso_bridge_wpgb_save_handler',
					'permission_callback' => 'wso_bridge_permission_check',
				),
			)
		);

		register_rest_route(
			'wso/v1',
			'/wpgb/(?P<type>grids|cards|facets)/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => 'wso_bridge_wpgb_get_handler',
					'permission_callback' => 'wso_bridge_permission_check',
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => 'wso_bridge_wpgb_save_handler',
					'permission_callback' => 'wso_bridge_permission_check',
				),
			)
		);

		register_rest_route(
			'wso/v1',
			'/wpgb/reindex',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wso_bridge_wpgb_reindex_handler',
				'permission_callback' => 'wso_bridge_permission_check',
			)
		);
	}

	add_action( 'rest_api_init', 'wso_bridge_register_routes' );
}

/* WSO STATUS BRIDGE END */
