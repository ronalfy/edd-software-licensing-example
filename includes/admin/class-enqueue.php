<?php
/**
 * Enqueues admin scripts for the plugin.
 *
 * @package EDD_SL_Example
 */

namespace EDD_SL_Example\Includes\Admin;

/**
 * Class Enqueue
 */
class Enqueue {

	/**
	 * Main initialization function.
	 */
	public function run() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
	}

	/**
	 * Add React Script to Options Screen.
	 */
	public function add_admin_scripts() {
		wp_register_script(
			'jquery.block.ui',
			EDD_SL_EXAMPLE_URL . 'js/block.ui.js',
			array( 'jquery' ),
			EDD_SL_EXAMPLE_VERSION,
			true
		);
		$screen = get_current_screen();
		if ( 'settings_page_edd-sl-example' === $screen->base ) { // note this has not been tested in Multisite.
			wp_enqueue_style(
				'edd-sl-admin',
				EDD_SL_EXAMPLE_URL . 'dist/admin.css',
				array(),
				EDD_SL_EXAMPLE_VERSION,
				'all'
			);
			wp_enqueue_script(
				'edd-sl-admin',
				EDD_SL_EXAMPLE_URL . 'dist/admin.js',
				array( 'jquery', 'jquery.block.ui', 'wp-i18n' ),
				EDD_SL_EXAMPLE_VERSION,
				true
			);
			wp_localize_script(
				'edd-sl-admin',
				'edd_sl_admin',
				array_merge(
					Options::get_options(),
					array(
						'admin_url' => admin_url( 'admin.php' ),
						'loading'   => EDD_SL_EXAMPLE_URL . 'loading.svg',
					)
				)
			);
			wp_set_script_translations( 'edd-sl-admin', 'edd-software-licensing-example', EDD_SL_EXAMPLE_DIR . 'languages/' );
		}
	}
}
