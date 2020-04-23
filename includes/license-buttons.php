<?php
/**
 * License buttons
 *
 * @package EDD_SL_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

use EDD_SL_Example\Includes\Admin\Options as Options;

$options = Options::get_options( true );
?>
<div class="edd-sl-field edd-sl-field--buttons">
	<button id="edd-sl-license-save" class="edd-sl-button edd-sl-button-save">
		<?php
		esc_html_e( 'Save', 'edd-software-licensing-example' );
		?>
	</button>
	<?php
	if ( 'valid' === $options['license_status'] ) :
		?>
		<button id="edd-sl-license-check" class="edd-sl-button edd-sl-button-secondary edd-sl-button-info">
			<?php
			esc_html_e( 'Check License', 'edd-software-licensing-example' );
			?>
		</button>
		<button id="edd-sl-license-deactivate" class="edd-sl-button edd-sl-button-danger edd-sl-flex-float-right">
			<?php
			esc_html_e( 'Revoke License', 'edd-software-licensing-example' );
			?>
		</button>
		<?php
	endif;
	?>
</div>
