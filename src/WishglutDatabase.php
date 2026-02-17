<?php
namespace Wishglut;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WishglutDatabase {

	/**
	 * Get the wishlist table name
	 */
	public static function get_wishlist_table() {
		global $wpdb;
		return $wpdb->prefix . 'wishglut_wishlist';
	}

	/**
	 * Get the wishlist social table name (for sublists/pro features)
	 */
	public static function get_wishlist_social_table() {
		global $wpdb;
		return $wpdb->prefix . 'wishglut_wishlist_social';
	}

	/**
	 * Create the wishlist table
	 */
	public static function create_wishlist_table() {
		global $wpdb;

		$table_name = self::get_wishlist_table();

		// Check if table exists
		$exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;

		if ( $exists ) {
			return; // Table already exists
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			wish_user_id varchar(255) NOT NULL,
			username varchar(255) NOT NULL,
			useremail varchar(255) NOT NULL,
			product_ids text NOT NULL,
			product_meta longtext DEFAULT NULL,
			wishlist_notifications text NOT NULL,
			product_added_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			product_individual_dates longtext DEFAULT NULL,
			share_data text DEFAULT NULL,
			PRIMARY KEY (id),
			KEY wish_user_id (wish_user_id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the wishlist social table (for sublists/pro features)
	 */
	public static function create_wishlist_social_table() {
		global $wpdb;

		$table_name = self::get_wishlist_social_table();

		// Check if table exists
		$exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;

		if ( $exists ) {
			return; // Table already exists
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id varchar(255) NOT NULL,
			list_name varchar(255) NOT NULL,
			list_slug varchar(255) NOT NULL,
			product_ids longtext NOT NULL,
			notification_settings longtext DEFAULT NULL,
			share_token varchar(255) DEFAULT NULL,
			share_enabled tinyint(1) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY list_slug (list_slug),
			KEY share_token (share_token),
			UNIQUE KEY user_list (user_id, list_slug)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the wishlist product subscriptions table (for email notifications)
	 */
	public static function create_wishlist_subscriptions_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wishglut_wishlist_subscriptions';

		// Check if table exists
		$exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;

		if ( $exists ) {
			return; // Table already exists
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id varchar(255) NOT NULL,
			user_email varchar(255) NOT NULL,
			product_id mediumint(9) NOT NULL,
			subscribed tinyint(1) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY product_id (product_id),
			UNIQUE KEY user_product (user_id, product_id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Check if column exists in table
	 */
	private static function column_exists( $table_name, $column_name ) {
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SHOW COLUMNS FROM `{$table_name}` LIKE %s", $column_name ) );
		return ! empty( $results );
	}

	/**
	 * Migrate data from shopglut_wishlist to wishglut_wishlist
	 * This helps users who are switching from shopglut to wishglut
	 */
	public static function migrate_from_shopglut() {
		global $wpdb;

		// Migrate main wishlist table
		$shopglut_table = $wpdb->prefix . 'shopglut_wishlist';
		$wishglut_table = self::get_wishlist_table();

		// Check if shopglut table exists
		$shopglut_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $shopglut_table ) ) === $shopglut_table;

		if ( $shopglut_exists ) {
			// Get data from shopglut table
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
			$shopglut_data = $wpdb->get_results( "SELECT * FROM {$shopglut_table}", ARRAY_A );

			if ( ! empty( $shopglut_data ) ) {
				// Insert data into wishglut table
				foreach ( $shopglut_data as $row ) {
					// Check if user already exists in wishglut table
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
					$exists = $wpdb->get_var( $wpdb->prepare(
						"SELECT id FROM {$wishglut_table} WHERE wish_user_id = %s",
						$row['wish_user_id']
					) );

					if ( ! $exists ) {
						// Insert new record
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
						$wpdb->insert(
							$wishglut_table,
							array(
								'wish_user_id' => $row['wish_user_id'],
								'username' => $row['username'],
								'useremail' => $row['useremail'],
								'product_ids' => $row['product_ids'],
								'product_meta' => $row['product_meta'] ?? null,
								'wishlist_notifications' => $row['wishlist_notifications'],
								'product_added_time' => $row['product_added_time'],
								'product_individual_dates' => $row['product_individual_dates'] ?? null,
								'share_data' => $row['share_data'] ?? null,
							)
						);
					}
				}
			}
		}

		// Migrate social table (sublists)
		$shopglut_social_table = $wpdb->prefix . 'shopglut_wishlist_social';
		$wishglut_social_table = self::get_wishlist_social_table();

		// Check if shopglut social table exists
		$shopglut_social_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $shopglut_social_table ) ) === $shopglut_social_table;

		if ( $shopglut_social_exists ) {
			// Get data from shopglut social table
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
			$shopglut_social_data = $wpdb->get_results( "SELECT * FROM {$shopglut_social_table}", ARRAY_A );

			if ( ! empty( $shopglut_social_data ) ) {
				// Insert data into wishglut social table
				foreach ( $shopglut_social_data as $row ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
					$exists = $wpdb->get_var( $wpdb->prepare(
						"SELECT id FROM {$wishglut_social_table} WHERE user_id = %s AND list_slug = %s",
						$row['user_id'],
						$row['list_slug']
					) );

					if ( ! $exists ) {
						// Insert new record
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
						$wpdb->insert(
							$wishglut_social_table,
							array(
								'user_id' => $row['user_id'],
								'list_name' => $row['list_name'] ?? '',
								'list_slug' => $row['list_slug'] ?? '',
								'product_ids' => $row['product_ids'] ?? '',
								'notification_settings' => $row['notification_settings'] ?? null,
								'share_token' => $row['share_token'] ?? null,
								'share_enabled' => $row['share_enabled'] ?? 0,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Initialize database tables
	 */
	public static function init() {
		// Create the main wishlist table
		self::create_wishlist_table();

		// Create the social table (for sublists/pro features)
		self::create_wishlist_social_table();

		// Create the subscriptions table (for email notifications)
		self::create_wishlist_subscriptions_table();

		// Migrate data from shopglut if available
		$migration_done = get_option( 'wishglut_shopglut_migration_done', false );

		if ( ! $migration_done ) {
			self::migrate_from_shopglut();
			update_option( 'wishglut_shopglut_migration_done', true );
		}
	}
}
