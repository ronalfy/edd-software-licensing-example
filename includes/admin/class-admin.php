<?php
/**
 * Admin functionality for the plugin.
 *
 * @package EDD_SL_Example
 */

namespace EDD_SL_Example\Includes\Admin;

use EDD_SL_Example\Includes\Admin\Options as Options;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Initialize the Admin component.
	 */
	public function init() {
		// Add settings link.
		$prefix = is_multisite() ? 'network_admin_' : '';
		add_action( $prefix . 'plugin_action_links_' . EDD_SL_EXAMPLE_SLUG, array( $this, 'plugin_settings_link' ) );

		// Init admin menu.
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'register_sub_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
		}
	}

	/**
	 * Admin init hooks.
	 */
	public function admin_init() {
		$options = Options::get_options();
		$license = $options['license'];
		if ( false !== $license ) {

			// setup the updater.
			$edd_updater = new \EDD_SL_Example\Includes\Admin\Upgrader(
				'https://wpteams.pro',
				EDD_SL_EXAMPLE_FILE,
				array(
					'version' => EDD_SL_EXAMPLE_VERSION,
					'license' => $license,
					'item_id' => 63,
					'author'  => 'Ronald Huereca',
					'beta'    => false,
					'url'     => home_url(),
				)
			);
		}
		add_action( 'after_plugin_row_' . EDD_SL_EXAMPLE_SLUG, array( $this, 'after_plugin_row' ), 10, 3 );
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function run() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Adds license information
	 *
	 * @since 1.0.0
	 * @access public
	 * @param string $plugin_file Plugin file.
	 * @param array  $plugin_data Array of plugin data.
	 * @param string $status      If plugin is active or not.
	 * @return void HTML Settings.
	 */
	public function after_plugin_row( $plugin_file, $plugin_data, $status ) {
		$options        = Options::get_options();
		$license        = $options['license'];
		$license_status = $options['license_status'];
		$options_url    = $this->get_url();
		if ( empty( $license ) || false === $license_status ) {
			echo sprintf( '<tr class="active"><td colspan="3">%s <a href="%s">%s</a></td></tr>', esc_html__( 'Please enter a license to receive automatic updates.', 'edd-software-licensing-example' ), esc_url( $options_url ), esc_html__( 'Enter License.', 'edd-software-licensing-example' ) );
		}
	}

	/**
	 * Initializes admin menus
	 *
	 * @since 1.0.0
	 * @access public
	 * @see init
	 */
	public function register_sub_menu() {
		if ( is_multisite() ) {
			$hook = add_submenu_page(
				'settings.php',
				__( 'EDD Software Licensing Example', 'edd-software-licensing-example' ),
				__( 'EDD Software Licensing Example', 'edd-software-licensing-example' ),
				'manage_network',
				'edd-sl-example',
				array( $this, 'admin_page' )
			);
		} else {
			$hook = add_submenu_page(
				'options-general.php',
				__( 'EDD Software Licensing Example', 'edd-software-licensing-example' ),
				__( 'EDD Software Licensing Example', 'edd-software-licensing-example' ),
				'manage_options',
				'edd-sl-example',
				array( $this, 'admin_page' )
			);
		}
	}

	/**
	 * Output admin menu
	 *
	 * @since 1.0.0
	 * @access public
	 * @see register_sub_menu
	 */
	public function admin_page() {
		$options = Options::get_options();
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'EDD Software Licensing Example', 'edd-software-licensing-example' ); ?></h2>
			<form action="" method="POST">
				<?php wp_nonce_field( 'save_edd_sl_options', '_edd_sl' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="edd-license"><?php esc_html_e( 'Enter Your License', 'edd-software-licensing-example' ); ?></label></th>
							<td>
								<input id="edd-license" class="regular-text" type="password" value="<?php echo esc_attr( $options['license'] ); ?>" name="options[license]" /><br />
								<div class="edd-sl-field edd-sl-field--checkbox">
									<label for="edd-sl-field-license-reveal">
										<input type="checkbox" id="edd-sl-field-license-reveal" value="0" /> <?php esc_html_e( 'Reveal license', 'edd-software-licensing-example' ); ?>
									</label>
								</div>
								<div class="edd-sl-action-buttons-wrapper">
									<?php require_once EDD_SL_EXAMPLE_DIR . 'includes/license-buttons.php'; ?>
								</div>
								<div class="edd-sl-field edd-sl-field--status edd-sl-status edd-sl-success license-status" style="display: none;">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		<?php
	}

	/**
	 * Adds plugin settings page link to plugin links in WordPress Dashboard Plugins Page
	 *
	 * @since 2.0.0
	 * @access public
	 * @see __construct
	 * @param array $settings Uses $prefix . "plugin_action_links_$plugin_file" action.
	 * @return array Array of settings
	 */
	public function plugin_settings_link( $settings ) {
		$admin_anchor = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $this->get_url() ),
			esc_html__( 'Settings', 'edd-software-licensing-example' )
		);
		if ( ! is_array( $settings ) ) {
			return array( $admin_anchor );
		} else {
			return array_merge( array( $admin_anchor ), $settings );
		}
	}

	/**
	 * Return the URL to the admin panel page.
	 *
	 * Return the URL to the admin panel page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string URL to the admin panel page.
	 */
	public function get_url() {
		if ( is_multisite() ) {
			$url = add_query_arg( array( 'page' => 'edd-sl-example' ), network_admin_url( 'settings.php' ) );
		} else {
			$url = add_query_arg( array( 'page' => 'edd-sl-example' ), admin_url( 'options-general.php' ) );
		}
		return $url;
	}
}
