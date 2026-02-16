<?php
/**
 * Plugin Name: WishGlut - Wishlist for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/shopglut
 * Description: Beautiful WooCommerce wishlist plugin with advanced features like wishlist sharing, social sharing, QR code, popular products, and more.
 * Version: 1.0.6
 * Author: AppGlut
 * Author URI: https://appglut.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wishglut
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WISHGLUT_VERSION', '1.0.0' );
define( 'WISHGLUT_FILE', __FILE__ );
define( 'WISHGLUT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WISHGLUT_URL', plugin_dir_url( __FILE__ ) );
define( 'WISHGLUT_BASENAME', plugin_basename( __FILE__ ) );

// Load WelcomePage class early - needed for both individual menu and standalone welcome page
if ( is_admin() && file_exists( WISHGLUT_PATH . 'src/WelcomePage.php' ) ) {
	require_once WISHGLUT_PATH . 'src/WelcomePage.php';
}

// Register welcome page menu (always available, not dependent on individual menu)
add_action( 'admin_menu', function() {
	add_submenu_page(
		null, // Parent as null = hidden from menu, accessible via URL
		esc_html__( 'Welcome to WishGlut', 'wishglut' ),
		esc_html__( 'Welcome', 'wishglut' ),
		'manage_options',
		'wishglut-welcome',
		function() {
			$welcome_page = new \Wishglut\WelcomePage();
			$welcome_page->render_welcome_content();
		}
	);
}, 99 ); // Low priority to run after individual menu registration

// Initialize individual menu class
if ( is_admin() && file_exists( WISHGLUT_PATH . 'src/WishglutIndividualMenu.php' ) ) {
	require_once WISHGLUT_PATH . 'src/WishglutIndividualMenu.php';
	Wishglut\WishglutIndividualMenu::get_instance();
}

// Hook into WooCommerce initialization
add_action( 'woocommerce_init', 'wishglut_plugin_initialize' );


function wishglut_plugin_initialize() {
	// Ensure that WooCommerce is loaded before proceeding
	if ( class_exists( 'WooCommerce' ) ) {
		// Run ShopGlut initialization
		// Include the AGSHOPGLUT framework setup class
		if ( file_exists( WISHGLUT_PATH . 'src/library/model/classes/setup.class.php' ) && ! defined( 'SHOPGLUT_VERSION' ) ) {
			require_once WISHGLUT_PATH . 'src/library/model/classes/setup.class.php';
		}
		require_once WISHGLUT_PATH . 'src/wishlist-settings.php';

	}
}

// Activation hook
register_activation_hook( __FILE__, function() {
	// Set transient to redirect to welcome page
	set_transient( 'wishglut_activation_redirect', true, 30 );
} );

// Admin init hook for redirect to welcome page
add_action( 'admin_init', function() {
	// Check if we should redirect to welcome page
	if ( get_transient( 'wishglut_activation_redirect' ) ) {
		delete_transient( 'wishglut_activation_redirect' );

		// Don't redirect if activating from network admin or bulk activation
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking WordPress core parameter during plugin activation
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Redirect to welcome page (always available via standalone menu registration)
		wp_safe_redirect( admin_url( 'admin.php?page=wishglut-welcome' ) );
		exit;
	}
} );
