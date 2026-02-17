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

		$shopglut_table = $wpdb->prefix . 'shopglut_wishlist';
		$wishglut_table = self::get_wishlist_table();

		// Check if shopglut table exists
		$shopglut_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $shopglut_table ) ) === $shopglut_table;

		if ( ! $shopglut_exists ) {
			return; // Nothing to migrate
		}

		// Get data from shopglut table
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query
		$shopglut_data = $wpdb->get_results( "SELECT * FROM {$shopglut_table}", ARRAY_A );

		if ( empty( $shopglut_data ) ) {
			return; // No data to migrate
		}

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

	/**
	 * Initialize database tables
	 */
	public static function init() {
		// Create the wishlist table
		self::create_wishlist_table();

		// Migrate data from shopglut if available
		$migration_done = get_option( 'wishglut_shopglut_migration_done', false );

		if ( ! $migration_done ) {
			self::migrate_from_shopglut();
			update_option( 'wishglut_shopglut_migration_done', true );
		}
	}
}
