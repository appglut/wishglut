<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: related_plugins
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'AGWISHGLUT_related_plugins' ) ) {
	class AGWISHGLUT_related_plugins extends AGWISHGLUTP {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {
			echo wp_kses_post( $this->field_before() );

			$plugins = $this->get_related_plugins();
			$versions = get_option( 'shopglut_related_plugins_versions', array() );
			$dismissed = get_option( 'shopglut_related_plugins_versions_dismissed', array() );

			?>
			<div class="shopglut-related-plugins-wrapper">
				<div class="shopglut-plugins-header">
					<div>
						<h3><?php esc_html_e( 'More Plugins by AppGlut', 'shopglut' ); ?></h3>
						<p class="description"><?php esc_html_e( 'Check out our other WooCommerce plugins to enhance your store.', 'shopglut' ); ?></p>
					</div>
					<button type="button" class="button button-primary button-primary shopglut-check-updates" id="shopglut-check-updates-btn">
						<span class="dashicons dashicons-update-alt"></span>
						<?php esc_html_e( 'Check for Updates', 'shopglut' ); ?>
					</button>
					<span class="spinner" style="display:none;" id="shopglut-check-updates-spinner"></span>
				</div>

				<div class="shopglut-plugins-grid">
					<?php foreach ( $plugins as $slug => $plugin ) :
						$is_installed = $this->is_plugin_installed( $plugin['basename'] );
						$is_active = $this->is_plugin_active( $plugin['basename'] );
						$current_version = $is_installed ? $this->get_plugin_version( $plugin['basename'] ) : null;
						$latest_version = isset( $versions[ $slug ]['version'] ) ? $versions[ $slug ]['version'] : null;
						$has_update = $latest_version && $current_version && version_compare( $current_version, $latest_version, '<' );
						$version_info = isset( $versions[ $slug ] ) ? $versions[ $slug ] : null;
						?>

						<div class="shopglut-plugin-card <?php echo $is_active ? 'active' : ''; ?> <?php echo $has_update ? 'has-update' : ''; ?>">
							<div class="plugin-icon"><?php echo esc_html( $plugin['icon'] ); ?></div>
							<div class="plugin-info">
								<h4 class="plugin-name"><?php echo esc_html( $plugin['name'] ); ?></h4>
								<p class="plugin-description"><?php echo esc_html( $plugin['description'] ); ?></p>

								<!-- Version Info -->
								<div class="plugin-versions">
									<?php if ( $current_version ) : ?>
										<span class="version-label current-version">
											<?php esc_html_e( 'Current:', 'shopglut' ); ?>
											<strong><?php echo esc_html( $current_version ); ?></strong>
										</span>
									<?php endif; ?>

									<?php if ( $latest_version ) : ?>
										<span class="version-label latest-version <?php echo $has_update ? 'update-available' : ''; ?>">
											<?php esc_html_e( 'Latest:', 'shopglut' ); ?>
											<strong><?php echo esc_html( $latest_version ); ?></strong>
										</span>
									<?php endif; ?>
								</div>

								<?php if ( $has_update ) : ?>
									<div class="plugin-update-badge">
										<span class="update-badge">
											<?php esc_html_e( 'Update Available!', 'shopglut' ); ?>
										</span>
									</div>
								<?php endif; ?>

								<div class="plugin-status">
									<?php if ( $is_active ) : ?>
										<span class="status-badge active"><?php esc_html_e( 'Active', 'shopglut' ); ?></span>
									<?php elseif ( $is_installed ) : ?>
										<span class="status-badge installed"><?php esc_html_e( 'Installed', 'shopglut' ); ?></span>
									<?php else : ?>
										<span class="status-badge not-installed"><?php esc_html_e( 'Not Installed', 'shopglut' ); ?></span>
									<?php endif; ?>
								</div>

								<div class="plugin-actions">
									<?php if ( $has_update && $version_info ) : ?>
										<!-- Update Now button -->
										<button type="button" class="button button-primary button-small shopglut-update-plugin"
											data-slug="<?php echo esc_attr( $slug ); ?>"
											data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>"
											data-zip="<?php echo esc_url( $version_info['zip_url'] ); ?>"
											data-nonce="<?php echo esc_attr( wp_create_nonce( 'shopglut_update_plugin_' . $slug ) ); ?>">
											<span class="dashicons dashicons-update-alt"></span>
											<?php esc_html_e( 'Update Now', 'shopglut' ); ?>
										</button>
										<a href="<?php echo esc_url( $version_info['url'] ); ?>" target="_blank" class="button button-small">
											<?php esc_html_e( 'View Release', 'shopglut' ); ?>
										</a>
									<?php elseif ( $is_installed && $version_info ) : ?>
										<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button button-small">
											<?php esc_html_e( 'Learn More', 'shopglut' ); ?>
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( $plugin['url'] ); ?>" target="_blank" class="button button-small">
											<?php esc_html_e( 'View Plugin', 'shopglut' ); ?>
										</a>
									<?php endif; ?>
								</div>

								<!-- Update Progress -->
								<div class="update-progress" style="display:none;" id="progress-<?php echo esc_attr( $slug ); ?>">
									<div class="progress-bar">
										<div class="progress-fill" style="width: 0%"></div>
									</div>
									<div class="progress-text"><?php esc_html_e( 'Updating...', 'shopglut' ); ?></div>
								</div>
							</div>
						</div>

					<?php endforeach; ?>
				</div>
			</div>

			<style>
				.shopglut-related-plugins-wrapper {
					background: #f9f9f9;
					border: 1px solid #ddd;
					border-radius: 8px;
					padding: 20px;
					margin: 15px 0;
				}

				.shopglut-plugins-header {
					display: flex;
					justify-content: space-between;
					align-items: flex-start;
					margin-bottom: 20px;
					gap: 20px;
				}

				.shopglut-plugins-header > div {
					flex: 1;
				}

				.shopglut-plugins-header h3 {
					margin: 0 0 5px 0;
					font-size: 16px;
					color: #1d2327;
				}

				.shopglut-plugins-header .description {
					margin: 0;
					color: #646970;
					font-size: 13px;
				}

				.shopglut-plugins-header .button-primary {
					display: inline-flex;
					align-items: center;
					gap: 5px;
					white-space: nowrap;
					flex-shrink: 0;
				}

				.shopglut-plugins-header .spinner {
					vertical-align: middle;
					margin: 0 0 0 10px;
					float: none;
				}

				.shopglut-plugins-grid {
					display: grid;
					grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
					gap: 15px;
					margin-bottom: 15px;
				}

				.shopglut-plugin-card {
					background: #fff;
					border: 1px solid #ddd;
					border-radius: 6px;
					padding: 15px;
					display: flex;
					gap: 12px;
					transition: all 0.2s ease;
				}

				.shopglut-plugin-card:hover {
					box-shadow: 0 2px 8px rgba(0,0,0,0.1);
					border-color: #2271b1;
				}

				.shopglut-plugin-card.has-update {
					border-color: #72aee6;
					background: #f0f6fc;
					border-left: 4px solid #72aee6;
				}

				.plugin-icon {
					font-size: 32px;
					line-height: 1;
					flex-shrink: 0;
				}

				.plugin-info {
					flex: 1;
				}

				.plugin-info .plugin-name {
					margin: 0 0 5px 0;
					font-size: 14px;
					font-weight: 600;
					color: #1d2327;
				}

				.plugin-info .plugin-description {
					margin: 0 0 8px 0;
					font-size: 12px;
					color: #646970;
					line-height: 1.4;
				}

				/* Version Info Styles */
				.plugin-versions {
					display: flex;
					flex-wrap: wrap;
					gap: 10px;
					margin-bottom: 8px;
				}

				.version-label {
					font-size: 11px;
					padding: 3px 8px;
					border-radius: 3px;
					background: #f0f0f1;
					color: #646970;
				}

				.version-label strong {
					margin-left: 4px;
				}

				.version-label.latest-version.update-available {
					background: #edfaef;
					color: #00a32a;
					border: 1px solid #00a32a;
				}

				.plugin-update-badge {
					margin-bottom: 8px;
				}

				.plugin-update-badge .update-badge {
					display: inline-block;
					background: #d63638;
					color: #fff;
					padding: 3px 8px;
					border-radius: 3px;
					font-size: 11px;
					font-weight: 600;
					animation: pulse 2s infinite;
				}

				@keyframes pulse {
					0%, 100% { opacity: 1; }
					50% { opacity: 0.8; }
				}

				.plugin-status {
					margin-bottom: 8px;
				}

				.status-badge {
					display: inline-block;
					padding: 2px 8px;
					border-radius: 3px;
					font-size: 11px;
					font-weight: 500;
				}

				.status-badge.active {
					background: #00a32a;
					color: #fff;
				}

				.status-badge.installed {
					background: #646970;
					color: #fff;
				}

				.status-badge.not-installed {
					background: #d63638;
					color: #fff;
				}

				.plugin-actions {
					display: flex;
					gap: 8px;
					flex-wrap: wrap;
				}

				.plugin-actions .button {
					padding: 4px 10px;
					font-size: 12px;
					height: auto;
					line-height: 1.5;
					display: inline-flex;
					align-items: center;
					gap: 4px;
				}

				/* Update Progress Styles */
				.update-progress {
					margin-top: 10px;
				}

				.progress-bar {
					height: 4px;
					background: #f0f0f1;
					border-radius: 2px;
					overflow: hidden;
					margin-bottom: 5px;
				}

				.progress-fill {
					height: 100%;
					background: #2271b1;
					transition: width 0.3s ease;
				}

				.progress-fill.updating {
					animation: progress-stripes 1s linear infinite;
					background: linear-gradient(45deg, #2271b1 25%, #135e96 25%, #135e96 50%, #2271b1 50%, #2271b1 75%, #135e96 75%);
					background-size: 20px 20px;
				}

				@keyframes progress-stripes {
					0% { background-position: 0 0; }
					100% { background-position: 20px 0; }
				}

				.progress-text {
					font-size: 11px;
					color: #646970;
					text-align: center;
				}

				@media (max-width: 600px) {
					.shopglut-plugins-grid {
						grid-template-columns: 1fr;
					}

					.shopglut-plugins-header {
						flex-direction: column;
						gap: 15px;
					}

					.shopglut-plugins-header .button-primary {
						width: 100%;
						justify-content: center;
					}
				}
			</style>

			<script>
			jQuery(document).ready(function($) {
				// Check for Updates button
				$('#shopglut-check-updates-btn').on('click', function() {
					var $btn = $(this);
					var $spinner = $('#shopglut-check-updates-spinner');

					$btn.prop('disabled', true);
					$spinner.show();

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'shopglut_force_check_updates',
							nonce: '<?php echo wp_create_nonce('shopglut_check_updates'); ?>'
						},
						success: function(response) {
							if (response.success) {
								location.reload();
							}
						},
						complete: function() {
							$btn.prop('disabled', false);
							$spinner.hide();
						}
					});
				});

				// Update Plugin button
				$('.shopglut-update-plugin').on('click', function() {
					var $btn = $(this);
					var slug = $btn.data('slug');
					var basename = $btn.data('basename');
					var zipUrl = $btn.data('zip');
					var nonce = $btn.data('nonce');
					var $progress = $('#progress-' + slug);
					var $card = $btn.closest('.shopglut-plugin-card');

					// Show progress
					$btn.prop('disabled', true);
					$progress.show();
					$progress.find('.progress-fill').addClass('updating').css('width', '20%');
					$progress.find('.progress-text').text('<?php esc_html_e( 'Downloading update...', 'shopglut' ); ?>');

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'shopglut_update_plugin',
							plugin: basename,
							zip_url: zipUrl,
							slug: slug,
							nonce: nonce
						},
						success: function(response) {
							if (response.success) {
								$progress.find('.progress-fill').css('width', '100%');
								$progress.find('.progress-text').text('<?php esc_html_e( 'Update complete! Reloading...', 'shopglut' ); ?>');

								setTimeout(function() {
									location.reload();
								}, 1500);
							} else {
								$progress.find('.progress-fill').removeClass('updating').css('width', '0%');
								$progress.find('.progress-text').text('<?php esc_html_e( 'Update failed: ', 'shopglut' ); ?>' + (response.data || 'Unknown error'));
								$btn.prop('disabled', false);
							}
						},
						error: function() {
							$progress.find('.progress-fill').removeClass('updating').css('width', '0%');
							$progress.find('.progress-text').text('<?php esc_html_e( 'Update failed: Server error', 'shopglut' ); ?>');
							$btn.prop('disabled', false);
						}
					});
				});
			});
			</script>

			<?php
			echo wp_kses_post( $this->field_after() );
		}

		private function get_related_plugins() {
			return array(
				'wishglut' => array(
					'name' => 'WishGlut',
					'description' => 'Advanced wishlist plugin for WooCommerce with multiple wishlist lists',
					'icon' => '🎁',
					'url' => 'https://github.com/appglut/wishglut',
					'basename' => 'wishglut/wishglut.php'
				),
				'checkoutglut' => array(
					'name' => 'CheckoutGlut',
					'description' => 'Customize WooCommerce checkout page with drag & drop builder',
					'icon' => '🛒',
					'url' => 'https://github.com/appglut/checkoutglut',
					'basename' => 'checkoutglut/checkoutglut.php'
				),
				'shortcodeglut' => array(
					'name' => 'ShortcodeGlut',
					'description' => 'Powerful shortcode builder for WordPress with visual editor',
					'icon' => '⚡',
					'url' => 'https://github.com/appglut/shortcodeglut',
					'basename' => 'shortcodeglut/shortcodeglut.php'
				),
				'product-details-glut' => array(
					'name' => 'ProductDetailsGlut',
					'description' => 'Beautiful WooCommerce single product page builder with 7+ templates',
					'icon' => '📦',
					'url' => 'https://github.com/appglut/product-details-glut',
					'basename' => 'product-details-glut/product-details-glut.php'
				)
			);
		}

		private function is_plugin_installed( $basename ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins = get_plugins();
			return isset( $plugins[ $basename ] );
		}

		private function is_plugin_active( $basename ) {
			return is_plugin_active( $basename );
		}

		private function get_plugin_version( $basename ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins = get_plugins();
			if ( isset( $plugins[ $basename ] ) ) {
				return $plugins[ $basename ]['Version'];
			}
			return null;
		}
	}
}
