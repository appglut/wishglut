<?php
namespace Wishglut;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Wishglut\admin\dataManage as adminDatamange;



class WishglutBase {

	// Declare properties to fix PHP 8.2+ deprecation warnings
	public $menu_slug;

	public function __construct() {

	
		adminDatamange::get_instance();
		dataManage::get_instance();
		
	}


	public static function get_instance() {
		static $instance;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}
		return $instance;
	}
}