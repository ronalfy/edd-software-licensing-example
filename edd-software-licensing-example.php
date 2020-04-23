<?php // phpcs:ignore
/**
 * EDD Software Licensing Example
 *
 * @package   EDD
 * @copyright Copyright(c) 2020, MediaRon LLC
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: EDD Software Licensing Example
 * Plugin URI: https://github.com/ronalfy/edd-software-licensing-example
 * Description: An example of how to use EDD+Software Licensing
 * Version: 1.0.0
 * Author: MediaRon LLC
 * Author URI: https://mediaron.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: edd-software-licensing-example
 * Domain Path: languages
 */

define( 'EDD_SL_EXAMPLE_VERSION', '1.0.0' );
define( 'EDD_SL_EXAMPLE_NAME', 'EDD Software Licensing Example' );
define( 'EDD_SL_EXAMPLE_DIR', plugin_dir_path( __FILE__ ) );
define( 'EDD_SL_EXAMPLE_URL', plugins_url( '/', __FILE__ ) );
define( 'EDD_SL_EXAMPLE_SLUG', plugin_basename( __FILE__ ) );
define( 'EDD_SL_EXAMPLE_FILE', __FILE__ );

// Setup the plugin auto loader.
require_once 'autoloader.php';


/**
 * The base class.
 */
class EDD_SL_Example {

	/**
	 * EDD_SL_Example instance.
	 *
	 * @var EDD_SL_Example $instance
	 */
	private static $instance = null;

	/**
	 * Return a class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class Constructor
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 20 );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Fired when the init action for WordPress is triggered.
	 */
	public function init() {
		load_plugin_textdomain( 'edd-software-licensing-example', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Fired when the plugins for WordPress have finished loading.
	 */
	public function plugins_loaded() {
		$this->admin_output = new \EDD_SL_Example\Includes\Admin\Output();
		$this->admin_output->run();

		$this->admin_enqueue = new \EDD_SL_Example\Includes\Admin\Enqueue();
		$this->admin_enqueue->run();

		$this->admin_ajax = new \EDD_SL_Example\Includes\Admin\Ajax();
		$this->admin_ajax->run();
	}
}
EDD_SL_Example::get_instance();
