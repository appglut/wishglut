<?php
/**
 * Welcome Page for WishGlut
 *
 * @package Wishglut
 */

namespace Wishglut;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WelcomePage {

	/**
	 * Render the welcome page content
	 */
	public function render_welcome_content() {
		// Check if user can access
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wishglut' ) );
		}
		?>
		<div class="wrap wsg-wrap-full wishglut-welcome-page">
			<style>
				/* Hide default WP title and add custom header */
				.wishglut-welcome-page > h1 {
					display: none;
				}

				.wsg-welcome-wrapper {
					max-width: 1000px;
					margin: 20px auto;
					background: #ffffff;
					border-radius: 12px;
					box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
					overflow: hidden;
				}

				.wsg-welcome-header {
					background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
					padding: 50px 40px;
					text-align: center;
					color: #ffffff;
				}

				.wsg-welcome-logo {
					width: 100px;
					height: 100px;
					background: rgba(255, 255, 255, 0.2);
					border-radius: 24px;
					margin: 0 auto 20px;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.wsg-welcome-logo svg {
					width: 70px;
					height: 70px;
				}

				.wsg-welcome-header h1 {
					font-size: 36px;
					font-weight: 700;
					margin: 0 0 10px 0;
					letter-spacing: -0.5px;
					color: #ffffff;
					display: block !important;
				}

				.wsg-welcome-subtitle {
					font-size: 18px;
					opacity: 0.95;
					font-weight: 400;
				}

				.wsg-welcome-content {
					padding: 40px;
				}

				.wsg-welcome-thank-you {
					text-align: center;
					margin-bottom: 40px;
				}

				.wsg-welcome-thank-you h2 {
					font-size: 24px;
					color: #1d2327;
					margin: 0 0 12px 0;
					font-weight: 600;
				}

				.wsg-welcome-thank-you p {
					font-size: 15px;
					color: #475569;
					line-height: 1.6;
					max-width: 550px;
					margin: 0 auto;
				}

				.wsg-welcome-features {
					display: grid;
					grid-template-columns: repeat(3, 1fr);
					gap: 24px;
					margin-bottom: 40px;
				}

				.wsg-welcome-feature {
					text-align: center;
					padding: 24px 16px;
					background: #f8fafc;
					border-radius: 12px;
					transition: all 0.2s ease;
					border: 1px solid #e2e8f0;
				}

				.wsg-welcome-feature:hover {
					transform: translateY(-3px);
					box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
					border-color: #cbd5e1;
				}

				.wsg-welcome-feature-icon {
					width: 50px;
					height: 50px;
					background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
					border-radius: 12px;
					margin: 0 auto 16px;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.wsg-welcome-feature-icon .dashicons {
					font-size: 28px;
					width: 28px;
					height: 28px;
					color: #ffffff;
				}

				.wsg-welcome-feature h3 {
					font-size: 16px;
					color: #1d2327;
					margin: 0 0 8px 0;
					font-weight: 600;
				}

				.wsg-welcome-feature p {
					font-size: 13px;
					color: #64748b;
					line-height: 1.5;
					margin: 0;
				}

				.wsg-welcome-quick-start {
					background: #fef3c7;
					border: 1px solid #fcd34d;
					border-radius: 12px;
					padding: 24px;
					margin-bottom: 30px;
				}

				.wsg-welcome-quick-start h3 {
					font-size: 18px;
					color: #92400e;
					margin: 0 0 16px 0;
					display: flex;
					align-items: center;
					gap: 8px;
				}

				.wsg-welcome-quick-start h3 .dashicons {
					font-size: 22px;
				}

				.wsg-welcome-quick-start ol {
					margin: 0 0 0 20px;
					padding: 0;
				}

				.wsg-welcome-quick-start li {
					font-size: 14px;
					color: #78350f;
					margin-bottom: 10px;
					line-height: 1.5;
				}

				.wsg-welcome-quick-start li:last-child {
					margin-bottom: 0;
				}

				.wsg-welcome-quick-start code {
					background: #fffbeb;
					padding: 2px 6px;
					border-radius: 4px;
					font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
					font-size: 12px;
					color: #b45309;
					border: 1px solid #fde68a;
				}

				.wsg-welcome-actions {
					display: flex;
					gap: 16px;
					justify-content: center;
				}

				.wsg-welcome-btn {
					display: inline-flex;
					align-items: center;
					gap: 8px;
					padding: 12px 24px;
					font-size: 14px;
					font-weight: 600;
					border-radius: 8px;
					text-decoration: none;
					transition: all 0.2s ease;
				}

				.wsg-welcome-btn--primary {
					background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
					color: #ffffff;
					box-shadow: 0 2px 8px rgba(17, 153, 142, 0.25);
				}

				.wsg-welcome-btn--primary:hover {
					transform: translateY(-1px);
					box-shadow: 0 4px 12px rgba(17, 153, 142, 0.35);
				}

				.wsg-welcome-btn--secondary {
					background: #f1f5f9;
					color: #475569;
					border: 1px solid #e2e8f0;
				}

				.wsg-welcome-btn--secondary:hover {
					background: #e2e8f0;
					border-color: #cbd5e1;
				}

				.wsg-welcome-btn .dashicons {
					font-size: 16px;
					width: 16px;
					height: 16px;
				}

				@media (max-width: 1200px) {
					.wsg-welcome-wrapper {
						margin: 0;
						border-radius: 0;
					}
				}

				@media (max-width: 782px) {
					.wsg-welcome-features {
						grid-template-columns: 1fr;
					}

					.wsg-welcome-header {
						padding: 40px 24px;
					}

					.wsg-welcome-content {
						padding: 24px;
					}

					.wsg-welcome-header h1 {
						font-size: 28px;
					}

					.wsg-welcome-actions {
						flex-direction: column;
					}

					.wsg-welcome-btn {
						width: 100%;
						justify-content: center;
					}
				}
			</style>

			<div class="wsg-welcome-wrapper">
				<div class="wsg-welcome-header">
					<div class="wsg-welcome-logo">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
							<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="white"/>
						</svg>
					</div>
					<h1><?php esc_html_e( 'Welcome to WishGlut', 'wishglut' ); ?></h1>
					<p class="wsg-welcome-subtitle"><?php esc_html_e( 'Beautiful Wishlist for WooCommerce', 'wishglut' ); ?></p>
				</div>

				<div class="wsg-welcome-content">
					<div class="wsg-welcome-thank-you">
						<h2><?php esc_html_e( 'Thank you for installing WishGlut!', 'wishglut' ); ?></h2>
						<p><?php esc_html_e( 'You\'re just a few steps away from offering your customers a beautiful wishlist experience.', 'wishglut' ); ?></p>
					</div>

					<div class="wsg-welcome-features">
						<div class="wsg-welcome-feature">
							<div class="wsg-welcome-feature-icon">
								<span class="dashicons dashicons-heart"></span>
							</div>
							<h3><?php esc_html_e( 'Wishlist Management', 'wishglut' ); ?></h3>
							<p><?php esc_html_e( 'Allow customers to save their favorite products and manage wishlists easily.', 'wishglut' ); ?></p>
						</div>

						<div class="wsg-welcome-feature">
							<div class="wsg-welcome-feature-icon">
								<span class="dashicons dashicons-share"></span>
							</div>
							<h3><?php esc_html_e( 'Social Sharing', 'wishglut' ); ?></h3>
							<p><?php esc_html_e( 'Share wishlists on social media or via direct link with QR code support.', 'wishglut' ); ?></p>
						</div>

						<div class="wsg-welcome-feature">
							<div class="wsg-welcome-feature-icon">
								<span class="dashicons dashicons-chart-bar"></span>
							</div>
							<h3><?php esc_html_e( 'Popular Products', 'wishglut' ); ?></h3>
							<p><?php esc_html_e( 'Track and display most wished products to help customers discover trending items.', 'wishglut' ); ?></p>
						</div>
					</div>

					<div class="wsg-welcome-quick-start">
						<h3>
							<span class="dashicons dashicons-superhero-alt"></span>
							<?php esc_html_e( 'Quick Start Guide', 'wishglut' ); ?>
						</h3>
						<ol>
							<li><?php esc_html_e( 'Navigate to the WishGlut Dashboard to see your wishlist analytics', 'wishglut' ); ?></li>
							<li><?php esc_html_e( 'Go to Settings page to configure button style, position, and behavior', 'wishglut' ); ?></li>
							<li><?php esc_html_e( 'Enable social sharing to let customers share their wishlists', 'wishglut' ); ?></li>
							<li><?php esc_html_e( 'Use the Popular Products page to see what customers want most', 'wishglut' ); ?></li>
							<li><?php esc_html_e( 'Add wishlist buttons to your product pages using shortcode: <code>[wishglut_button]</code>', 'wishglut' ); ?></li>
						</ol>
					</div>

					<div class="wsg-welcome-actions">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=wishglut' ) ); ?>" class="wsg-welcome-btn wsg-welcome-btn--primary">
							<span class="dashicons dashicons-arrow-right-alt"></span>
							<?php esc_html_e( 'Get Started', 'wishglut' ); ?>
						</a>
						<a href="https://documentation.appglut.com/?utm_source=wishglut-welcome&utm_medium=referral&utm_campaign=welcome" target="_blank" class="wsg-welcome-btn wsg-welcome-btn--secondary">
							<span class="dashicons dashicons-book"></span>
							<?php esc_html_e( 'View Documentation', 'wishglut' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function get_instance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}
		return $instance;
	}
}
