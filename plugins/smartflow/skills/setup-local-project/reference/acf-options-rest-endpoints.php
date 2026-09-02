<?php
/**
 * REST API - ACF options pages endpoints.
 *
 * Registers GET and POST routes under wso/v1/options/{slug} for each
 * configured ACF options page; ACF options pages are not reachable over
 * the core REST API by themselves. Install into the child theme's
 * js-snippets.php in the style of that file (tracked source, delivered
 * over the project deploy path), or import as a Code Snippet as fallback.
 *
 * Adapt the $options_pages map to the project's actual options pages
 * (slug => ACF post_id) before installing.
 *
 * Origin: "REST API - ACF Options-Seiten Endpunkte registrieren"
 * (Tobias Uhl, 2026-03-27), absorbed from the setup-wordpress-cursor skill.
 */

/**
 * ACF options page: resolve the page-specific fields.
 *
 * Returns only the ACF fields whose field group is assigned to the given
 * options page through its location rules.
 */
function wso_get_acf_fields_for_options_page( $page_slug ) {
	$field_groups = acf_get_field_groups( array( 'options_page' => $page_slug ) );
	$fields       = array();

	foreach ( $field_groups as $group ) {
		$group_fields = acf_get_fields( $group['key'] );
		if ( $group_fields ) {
			$fields = array_merge( $fields, $group_fields );
		}
	}

	return $fields;
}

/**
 * Register the wso/v1/options/{slug} routes. Access requires edit_posts.
 */
function wso_register_options_rest_routes() {
	$options_pages = array(
		'wso-website-settings'   => 'options',
		'wso-logo-settings'      => 'logo-settings',
		'wso-animation-settings' => 'animation-settings',
		'404-options'            => 'options',
		'acf-menue'              => 'options',
		'acf-img-switch'         => 'options',
	);

	$permission_callback = function () {
		return current_user_can( 'edit_posts' );
	};

	foreach ( $options_pages as $slug => $post_id ) {
		register_rest_route(
			'wso/v1',
			'/options/' . $slug,
			array(
				array(
					'methods'             => 'GET',
					'callback'            => function () use ( $slug, $post_id ) {
						$fields = wso_get_acf_fields_for_options_page( $slug );
						$data   = array();

						foreach ( $fields as $field ) {
							$data[ $field['name'] ] = get_field( $field['name'], $post_id );
						}

						return rest_ensure_response( $data );
					},
					'permission_callback' => $permission_callback,
				),
				array(
					'methods'             => 'POST',
					'callback'            => function ( \WP_REST_Request $request ) use ( $slug, $post_id ) {
						$params = $request->get_json_params();

						if ( empty( $params ) || ! is_array( $params ) ) {
							return new \WP_Error(
								'invalid_params',
								'Request body must be a JSON object with field/value pairs.',
								array( 'status' => 400 )
							);
						}

						$fields  = wso_get_acf_fields_for_options_page( $slug );
						$allowed = array_column( $fields, 'name' );
						$updated = array();

						foreach ( $params as $field_name => $value ) {
							if ( ! in_array( $field_name, $allowed, true ) ) {
								continue;
							}
							update_field( $field_name, $value, $post_id );
							$updated[] = $field_name;
						}

						return rest_ensure_response(
							array(
								'updated' => $updated,
							)
						);
					},
					'permission_callback' => $permission_callback,
				),
			)
		);
	}
}

/* -----------------------------------------------------------------------------
REST API - ACF options pages endpoints                                        */
add_action( 'rest_api_init', 'wso_register_options_rest_routes' );
