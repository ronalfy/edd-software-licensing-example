<?php
/**
 * Ajax functionality for the plugin.
 *
 * @package EDD_SL_Example
 */

namespace EDD_SL_Example\Includes\Admin;

use EDD_SL_Example\Includes\Admin\Options as Options;

/**
 * Class Ajax
 */
class Ajax {
	/**
	 * Initialize method.
	 */
	public function run() {

		// License Saving/Checking.
		add_action( 'wp_ajax_edd_sl_license_save', array( $this, 'ajax_license_save' ) );
		add_action( 'wp_ajax_edd_sl_license_check', array( $this, 'ajax_license_check' ) );
		add_action( 'wp_ajax_edd_sl_license_deactivate', array( $this, 'ajax_license_deactivate' ) );

	}

	/**
	 * Save a license as an option.
	 */
	public function ajax_license_save() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_DEFAULT );
		if ( ! wp_verify_nonce( $nonce, 'save_edd_sl_options' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				new \WP_Error( 'edd_sl_license_save', esc_html__( 'Security check failed.', 'edd-software-licensing-example' ) )
			);
		}

		$license = trim( filter_input( INPUT_POST, 'license', FILTER_DEFAULT ) );

		if ( empty( $license ) ) {
			wp_send_json_error( new \WP_Error( 'edd_sl_invalid_license', __( 'The license field cannot be empty.', 'edd-software-licensing-example' ) ) );
		}

		// Check for valid license.
		$store_url  = 'https://wpteams.pro';
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => rawurlencode( 'EDD Software Licensing Example' ),
			'url'        => home_url(),
		);
		// Call the custom API.
		$response = wp_remote_post(
			$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		$options = Options::get_options( true );

		// make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response );
			} else {
				wp_send_json_error( new \WP_Error( 'edd_sl_invalid_code', __( 'We could not communicate with the update server. Please try again later.', 'edd-software-licensing-example' ) ) );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				$options['license_status'] = '';
				switch ( $license_data->error ) {
					case 'expired':
						$license_message = sprintf(
							/* Translators: %s is a date format placeholder */
							__( 'Your license key expired on %s.', 'edd-software-licensing-example' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) // phpcs:ignore
						);
						break;

					case 'disabled':
					case 'revoked':
						$license_message = __( 'Your license key has been disabled.', 'edd-software-licensing-example' );
						break;

					case 'missing':
						$license_message = __( 'Invalid license.', 'edd-software-licensing-example' );
						break;
					case 'invalid':
					case 'site_inactive':
						$license_message = __( 'Your license is not active for this URL.', 'edd-software-licensing-example' );
						break;

					case 'item_name_mismatch':
						/* Translators: %s is the plugin name */
						$license_message = sprintf( __( 'This appears to be an invalid license key for %s.', 'edd-software-licensing-example' ), 'EDD Software Licensing Example' );
						break;

					case 'no_activations_left':
						$license_message = __( 'Your license key has reached its activation limit.', 'edd-software-licensing-example' );
						break;
					default:
						$license_message = __( 'An error occurred, please try again.', 'edd-software-licensing-example' );
						break;
				}
			}
			if ( empty( $license_message ) ) {
				$options['license_status'] = $license_data->license;
				$options['license']        = sanitize_text_field( $license );
				Options::update_options( $options );
				ob_start();
				require_once EDD_SL_EXAMPLE_DIR . 'includes/license-buttons.php';
				wp_send_json_success(
					array(
						'message' => __( 'Your license is now active.', 'edd-software-licensing-example' ),
						'html'    => ob_get_clean(),
					)
				);
			} else {
				wp_send_json_error( new \WP_Error( 'edd_sl_license_fail', $license_message ) );
			}
		}
		wp_send_json_error( new \WP_Error( 'edd_sl_license_failure', __( 'An unexpected error has occurred. Please contact support.', 'edd-software-licensing-example' ) ) );
	}

	/**
	 * Revoke a license.
	 */
	public function ajax_license_deactivate() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_DEFAULT );
		if ( ! wp_verify_nonce( $nonce, 'save_edd_sl_options' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				new \WP_Error( 'edd_sl_license_save', esc_html__( 'Security check failed.', 'edd-software-licensing-example' ) )
			);
		}

		$license = trim( filter_input( INPUT_POST, 'license', FILTER_DEFAULT ) );

		if ( empty( $license ) ) {
			wp_send_json_error( new \WP_Error( 'edd_sl_invalid_license', __( 'The license field cannot be empty.', 'edd-software-licensing-example' ) ) );
		}

		// Check for valid license.
		$store_url  = 'https://wpteams.pro';
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => rawurlencode( 'EDD Software Licensing Example' ),
			'url'        => home_url(),
		);
		// Call the custom API.
		$response = wp_remote_post(
			$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		$options = Options::get_options( true );

		// make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response );
			} else {
				wp_send_json_error( new \WP_Error( 'edd_sl_invalid_code', __( 'We could not communicate with the update server. Please try again later.', 'edd-software-licensing-example' ) ) );
			}
		} else {
			$options['license_status'] = '';
			$options['license']        = '';
			Options::update_options( $options );
			ob_start();
			require_once EDD_SL_EXAMPLE_DIR . 'includes/license-buttons.php';
			wp_send_json_success(
				array(
					'message' => __( 'Your license is now deactivated.', 'edd-software-licensing-example' ),
					'html'    => ob_get_clean(),
				)
			);
		}
	}

	/**
	 * Check a license.
	 */
	public function ajax_license_check() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_DEFAULT );
		if ( ! wp_verify_nonce( $nonce, 'save_edd_sl_options' ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				new \WP_Error( 'edd_sl_license_save', esc_html__( 'Security check failed.', 'edd-software-licensing-example' ) )
			);
		}

		$license = trim( filter_input( INPUT_POST, 'license', FILTER_DEFAULT ) );

		if ( empty( $license ) ) {
			wp_send_json_error( new \WP_Error( 'edd_sl_invalid_license', __( 'The license field cannot be empty.', 'edd-software-licensing-example' ) ) );
		}

		// Check for valid license.
		$store_url  = 'https://wpteams.pro';
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => rawurlencode( 'EDD Software Licensing Example' ),
			'url'        => home_url(),
		);
		// Call the custom API.
		$response = wp_remote_post(
			$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response );
			} else {
				wp_send_json_error( new \WP_Error( 'edd_sl_invalid_code', __( 'We could not communicate with the update server. Please try again later.', 'edd-software-licensing-example' ) ) );
			}
		} else {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			$message  = '<ul>';
			$message .= sprintf(
				'<li><strong>%s:</strong> %s</li>',
				esc_html__( 'License Status', 'edd-software-licensing-example' ),
				'valid' === $response->license ? esc_html_x( 'Valid', 'Valid license', 'edd-software-licensing-example' ) : esc_html_x( 'Invalid', 'Invalid license', 'edd-software-licensing-example' )
			);
			$message .= sprintf(
				'<li><strong>%s:</strong> %s</li>',
				esc_html__( 'Expires', 'edd-software-licensing-example' ),
				esc_html( strtoupper( $response->expires ) )
			);
			$message .= sprintf(
				'<li><strong>%s:</strong> %s</li>',
				esc_html__( 'Activations Left', 'edd-software-licensing-example' ),
				esc_html( strtoupper( $response->activations_left ) )
			);
			$message .= sprintf(
				'<li><strong>%s:</strong> %s %s</li>',
				esc_html__( 'Active on', 'edd-software-licensing-example' ),
				number_format( absint( $response->site_count ) ),
				_n( 'site', 'sites', absint( $response->site_count ), 'edd-software-licensing-example' )
			);
			$message .= '</ul>';
			ob_start();
			require_once EDD_SL_EXAMPLE_DIR . 'includes/license-buttons.php';
			wp_send_json_success(
				array(
					'message' => $message,
					'html'    => ob_get_clean(),
				)
			);
		}
	}
}
