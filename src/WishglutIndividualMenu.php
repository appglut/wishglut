<?php
namespace Wishglut;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for managing WishGlut individual top-level menu
 * This ensures the individual menu shows the same content as ShopGlut's wishlist submenu
 */
class WishglutIndividualMenu {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add menu using standard WordPress priority
		add_action( 'admin_menu', array( $this, 'addIndividualMenu' ), 20 );
		// Enqueue assets for the individual menu page
		add_action( 'load-toplevel_page_wishglut', array( $this, 'enqueueAssets' ) );
	}

	/**
	 * Add individual top-level menu when enabled from integration settings
	 */
	public function addIndividualMenu() {
		// Only add menu if enabled from integration settings
		if ( ! $this->showIndividualMenu() ) {
			return;
		}

		// Add top-level menu for WishGlut
		add_menu_page(
			'WishGlut',
			'WishGlut',
			'manage_options',
			'wishglut',
			array( $this, 'renderPage' ),
			'dashicons-heart',
			31
		);

		// Add Welcome submenu
		add_submenu_page(
			'wishglut',
			esc_html__( 'Welcome', 'wishglut' ),
			esc_html__( 'Welcome', 'wishglut' ),
			'manage_options',
			'wishglut-welcome',
			array( $this, 'render_welcome_page' )
		);
	}

	/**
	 * Check if individual menu should be shown
	 *
	 * @return bool Whether to show the individual menu
	 */
	public function showIndividualMenu() {
		// Get integration settings
		$settings = get_option( 'shopglut_integration_settings', array() );

		// Check if the wishglut-show-menu option is enabled
		if ( isset( $settings['wishglut-show-menu'] ) ) {
			// AGSHOPGLUT framework stores '1' or '0' as string, check for both
			return $settings['wishglut-show-menu'] === '1' || $settings['wishglut-show-menu'] === true || $settings['wishglut-show-menu'] === 1;
		}

		return false;
	}

	/**
	 * Render the page - loads the same WishlistMenuHandler that ShopGlut uses
	 * Passes 'wishglut' as menu_slug to ensure URLs stay within individual menu
	 */
	public function renderPage() {
		// Check if WishlistMenuHandler exists and load it
		if ( file_exists( WP_PLUGIN_DIR . '/wishglut/src/WishlistMenuHandler.php' ) ) {
			require_once WP_PLUGIN_DIR . '/wishglut/src/WishlistMenuHandler.php';
			// Pass 'wishglut' as menu_slug to ensure URLs stay within individual menu
			$wishlist_handler = new \Shopglut\enhancements\wishlist\WishlistMenuHandler('wishglut');
			$wishlist_handler->render();
			return;
		}

		// Fallback message if handler not found
		echo '<div class="wrap"><h1>' . esc_html__( 'WishGlut Settings', 'wishglut' ) . '</h1>';
		echo '<p>' . esc_html__( 'Unable to load WishGlut settings page.', 'wishglut' ) . '</p></div>';
	}

	/**
	 * Enqueue assets for the individual menu page
	 */
	public function enqueueAssets() {
		// Enqueue the same assets that ShopGlut uses for wishlist page
		do_action( 'load-shopglut_page_shopglut_wishlist' );
	}

	/**
	 * Render welcome page
	 */
	public function render_welcome_page() {
		$welcome_page = new \Wishglut\WelcomePage();
		$welcome_page->render_welcome_content();
	}

	/**
	 * Get singleton instance
	 *
	 * @return WishglutIndividualMenu
	 */
	public static function get_instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}
}
