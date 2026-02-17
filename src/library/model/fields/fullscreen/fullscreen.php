<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: Preview
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */

if ( ! class_exists( 'AGWISHGLUT_fullscreen' ) ) {
	/**
	 *
	 * Field: shortcode
	 *
	 * @since 2.0.15
	 * @version 2.0.15
	 */
	class AGWISHGLUT_fullscreen extends AGWISHGLUTP {

		/**
		 * Shortcode field constructor.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render
		 *
		 * @return void
		 */
		public function render() {
			?>
			<div class="back-to-menu">

				<a id="layout-switch-fullscreen" class="button button-primary button-large">
					<?php echo esc_html__( 'Switch to FullScreen', 'shopglut' ); ?>
				</a>

				<div class="clear"></div>
			</div>

			<?php

		}

	}
}
