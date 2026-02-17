<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Set a unique slug-like ID
$AGWISHGLUT_WISHLIST_OPTIONS = 'agwishglut_wishlist_options';

// Create Woo options
AGWISHGLUT::createOptions( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	// menu settings
	'menu_title' => esc_html__( 'Wishlist Options', 'wishglut' ),
	'show_bar_menu' => false,
	'hide_menu' => true,
	'menu_slug' => 'wishglut_wishlist_settings',
	'menu_parent' => 'wishglut_layoutss',
	'menu_type' => 'submenu',
	'menu_capability' => 'manage_options',
	'framework_title' => esc_html__( 'Wishlist Options', 'wishglut' ),
	'show_reset_section' => true,
	'shortcode_option' => '[wishglut_wishlist]',
	'framework_class' => 'wishglut_wishlist_settings',
	'footer_credit' => __( "Wishglut (Wishlist)", 'wishglut' ),
	 'menu_position' => 3
) );

//
// Create a top-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'id' => 'primary_tab', // Set a unique slug-like ID
	'title' => __( 'Settings', 'wishglut' ),
	'icon' => 'fa fa-cog',
) );

// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab', // The slug id of the parent section
	'title' => __( 'General', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-require-login',
			'type' => 'switcher',
			'title' => __( 'Require Login', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 0,
		),


		array(
			'id' => 'wishlist-require-login-btn-text',
			'type' => 'text',
			'title' => __( 'Require Login Button Text', 'wishglut' ),
			'default' => 'Wishlist Require Login',
			'dependency' => array( 'wishlist-require-login', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-require-login-btn-icon',
			'type' => 'icon',
			'title' => __( 'Require Login Button Icon', 'wishglut' ),
			'default' => 'fa-solid fa-lock',
			'dependency' => array( 'wishlist-require-login', '==', 'true' ),
		),

	
		array(
			'id' => 'wishlist-merge-guestlist',
			'type' => 'switcher',
			'title' => __( 'Merge Guest Wishlist After Login', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
			'dependency' => array( 'wishlist-require-login', '==', 'false' ),
		),

		array(
			'id' => 'wishlist-guestlist-deletetime',
			'type'    => 'spinner',
			'title' => __( 'Guest Wishlist Delete After', 'wishglut' ),
			'unit'    => 'days',
			'default' => 15,
			'dependency' => array( 'wishlist-require-login', '==', 'false' ),
		),

		array(
			'id' => 'wishlist-general-page',
			'type' => 'select',
			'title' => esc_html__( 'Wishlist Page', 'wishglut' ),
			'options' => 'pages',
			'query_args' => array(
				'posts_per_page' => -1, 
			),
		),

		array(
			'id' => 'wishlist-enable-share-qr',
			'type' => 'switcher',
			'title' => __( 'Enable Share Via QR', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),
		
		array(
			'id' => 'wishlist-enable-print-wish',
			'type' => 'switcher',
			'title' => __( 'Enable Print WIshlist', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),

			array(
			'id' => 'wishlist-enable-other-wishlist',
			'type' => 'switcher',
			'title' => __( 'Display Others Wishlist', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),

	   	array(
            'id' => 'wishlist-enable-menu-btn',
            'type' => 'switcher',
            'title' => __( 'Enable Menu Button', 'wishglut' ),
            'desc' => __( 'Wishlist Counter will show on Primary Menu', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
        ),

        	array(
            'id' => 'wishlist-menu-btn-text',
            'type' => 'text',
            'title' => __( 'Menu Button Text', 'wishglut' ),
            'default' => __( 'Wishlist', 'wishglut' ),
            'dependency' => array( 'wishlist-enable-menu-btn', '==', 'true' ),
       ),

        	array(
            'id' => 'wishlist-menu-btn-icon',
            'type' => 'icon',
            'title' => __( 'Menu Button Icon', 'wishglut' ),
            'default' => 'fa-solid fa-heart',
            'dependency' => array( 'wishlist-enable-menu-btn', '==', 'true' ),
       ),
        
        	array(
            'id' => 'wishlist-page-account-page',
            'type' => 'switcher',
            'title' => __( 'Enable Wishlist in Account Page', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
       ),
        
        	array(
            'id' => 'wishlist-page-account-page-name',
            'type' => 'text',
            'title' => __( 'Account Page Name', 'wishglut' ),
            'desc' => __( 'After change Save again Settings > Permalinks', 'wishglut' ),
            'default' => __( 'My Wishlist', 'wishglut' ),
            'dependency' => [ 'wishlist-page-account-page', '==', '1' ],
        ),

		array(
			'id' => 'wishlist-general-notification',
			'type' => 'select',
			'title' => __( 'Wishlist after Added Notification', 'wishglut' ),
			'options' => array(
				'notification-off' => __( 'Notification Off', 'wishglut' ),
				'side-notification' => __( 'Browser Side Notification', 'wishglut' ),
				'popup-notification' => __( 'Popup Notification', 'wishglut' ),
			),
			'default' => 'notification-off',
		),

		array(
			'id' => 'wishlist-side-notification-appear',
			'type' => 'select',
			'title' => __( 'Side Notification Appear', 'wishglut' ),
			'options' => array(
				'top-left' => __( 'From Top Left', 'wishglut' ),
				'top-middle' => __( 'From Top Middle', 'wishglut' ),
				'top-right' => __( 'From Top Right', 'wishglut' ),
				'middle-left' => __( 'From Middle Left', 'wishglut' ),
				'middle-right' => __( 'From Middle Right', 'wishglut' ),
				'bottom-left' => __( 'From Bottom Left', 'wishglut' ),
				'bottom-middle' => __( 'From Bottom Middle', 'wishglut' ),
				'bottom-right' => __( 'From Bottom Right', 'wishglut' ),
			),
			'default' => 'bottom-right',
			'dependency' => array( 'wishlist-general-notification', '==', 'side-notification' ),
		),

		array(
			'id' => 'wishlist-side-notification-effect',
			'type' => 'select',
			'title' => __( 'Side Notification Effect', 'wishglut' ),
			'options' => array(
				'fade-in-out' => __( 'Fade In/Out', 'wishglut' ),
				'slide-down-up' => __( 'Slide Down/Up', 'wishglut' ),
				'slide-from-left' => __( 'Slide from Left', 'wishglut' ),
				'slide-from-right' => __( 'Slide from Right', 'wishglut' ),
				'bounce' => __( 'Bounce', 'wishglut' ),
			),
			'default' => 'fade-in-out',
			'dependency' => array( 'wishlist-general-notification', '==', 'side-notification' ),
		),
		array(
			'id' => 'wishlist-popup-notification-effect',
			'type' => 'select',
			'title' => __( 'PopUp Notification Effect', 'wishglut' ),
			'options' => array(
				'fade-in-out' => __( 'Fade In/Out', 'wishglut' ),
				'zoom-in' => __( 'Zoom In', 'wishglut' ),
				'bounce' => __( 'Bounce', 'wishglut' ),
				'shake' => __( 'Shake', 'wishglut' ),
				'drop-in' => __( 'Drop In from Top', 'wishglut' ),
			),
			'default' => 'fade-in-out',
			'dependency' => array( 'wishlist-general-notification', '==', 'popup-notification' ),
		),

					array(
				'id' => 'wishlist-product-added-notification-text',
				'type' => 'text',
				'title' => __( 'Wishlist Added Text', 'wishglut' ),
				'default' => __( 'Product Added to Wishlist', 'wishglut' ),
				'desc'  => __( ' You can use <strong>{product_name}</strong> to show the product title and <strong>{product_sku}</strong> to show the product SKU. Example: "{product_name} added to your wishlist" or "Added {product_name} {product_sku} to wishlist"', 'wishglut' ),
				'dependency' => array( 'wishlist-general-notification', 'any', 'side-notification,popup-notification' ),
			),
			array(
				'id' => 'wishlist-product-removed-notification-text',
				'type' => 'text',
				'title' => __( 'Wishlist Removed Text', 'wishglut' ),
				'default' => __( 'Product Removed from Wishlist', 'wishglut' ),
				'desc'  => __( 'You can use <strong>{product_name}</strong> to show the product title and <strong>{product_sku}</strong> to show the product SKU. Example: "{product_name} removed from wishlist" or "Removed {product_name} {product_sku} from wishlist"', 'wishglut' ),
				'dependency' => array( 'wishlist-general-notification', 'any', 'side-notification,popup-notification' ),
			),

		array(
			'id' => 'wishlist-general-outofstock',
			'type' => 'switcher',
			'title' => __( 'Hide Wishlist for Out of Stock', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),


	) ) );

$wishlist_page_options = array(
	'product-image',
	'product-name',
	'product-price',
	'product-quantity',
	'product-availability',
	'product-discount-info',
	'product-review',
	'product-short-description',
	'product-sku',
	'product-add-to-cart',
	'product-checkout',
	'product-date-added',
	'product-urgency',
	
);

$attribute_taxonomies = wc_get_attribute_taxonomies();

// Loop through attributes and add them to filter options
if ( ! empty( $attribute_taxonomies ) ) {
	foreach ( $attribute_taxonomies as $attribute ) {
		if ( isset( $attribute->attribute_name ) ) {
			$attribute_name = $attribute->attribute_name;
			$wishlist_page_options[] = $attribute_name;
		}
	}
}


$wishlist_page_fields = array();

// Create sortable field items
$sortable_fields = array();

// Create mapping for translatable titles
$option_titles = array(
	'product-image' => __( 'Show Product Image', 'wishglut' ),
	'product-name' => __( 'Show Product Name', 'wishglut' ),
	'product-price' => __( 'Show Product Price', 'wishglut' ),
	'product-quantity' => __( 'Show Product Quantity', 'wishglut' ),
	'product-availability' => __( 'Show Product Availability', 'wishglut' ),
	'product-discount-info' => __( 'Show Product Discount Info', 'wishglut' ),
	'product-review' => __( 'Show Product Review', 'wishglut' ),
	'product-short-description' => __( 'Show Product Short Description', 'wishglut' ),
	'product-sku' => __( 'Show Product SKU', 'wishglut' ),
	'product-add-to-cart' => __( 'Show Product Add To Cart', 'wishglut' ),
	'product-checkout' => __( 'Show Product Checkout', 'wishglut' ),
	'product-date-added' => __( 'Show Product Date Added', 'wishglut' ),
	'product-urgency' => __( 'Show Product Urgency', 'wishglut' ),
);

foreach ( $wishlist_page_options as $option ) {
	// Use predefined title if available, otherwise create a fallback
	$title = isset( $option_titles[ $option ] ) 
		? $option_titles[ $option ]
		: /* translators: %s: option name */
		sprintf( __( 'Show %s', 'wishglut' ), ucwords( str_replace( array( '-', '_' ), ' ', $option ) ) );
		
	$sortable_fields[] = array(
		'id' => 'wishlist-page-show-' . str_replace( '_', '-', $option ),
		'type' => 'switcher',
		'title' => $title,
		'text_on' => __( 'Yes', 'wishglut' ),
		'text_off' => __( 'No', 'wishglut' ),
		'default' => '1',
	);
}

// Add the sortable field to the main fields array
$wishlist_page_fields[] = array(
	'id'        => 'wishlist-table-sort',
	'type'      => 'sortable',
	'fields'    => $sortable_fields,
);


$wishlist_page_fields[] = array(
	'id' => 'wishlist-remove-if-add-to-cart',
	'type' => 'switcher',
	'title' => __( 'Remove Wishlist if added to Cart', 'wishglut' ),
	'text_on' => __( 'Yes', 'wishglut' ),
	'text_off' => __( 'No', 'wishglut' ),
	'default' => 0,
);


// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab',
	'title' => __( 'Wishlist Page', 'wishglut' ),
	'fields' => $wishlist_page_fields,
) );


// Add the sortable field to the main fields array
$wishlist_account_page_fields[] = array(
	'id'        => 'wishlist-account-table-sort',
	'type'      => 'sortable',
	'fields'    => $sortable_fields,
);


$wishlist_account_page_fields[] = array(
	'id' => 'wishlist-account-remove-if-add-to-cart',
	'type' => 'switcher',
	'title' => __( 'Remove Wishlist if added to Cart', 'wishglut' ),
	'text_on' => __( 'Yes', 'wishglut' ),
	'text_off' => __( 'No', 'wishglut' ),
	'default' => '1',
);


// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab',
	'title' => __( 'Account Page', 'wishglut' ),
	'fields' => $wishlist_account_page_fields,
) );

// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab',
	'title' => __( 'Product Page', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-enable-product-page',
			'type' => 'switcher',
			'title' => __( 'Enable Wishlist for Product Page', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),

		array(
			'id' => 'wishlist-product-second-click',
			'type' => 'select',
			'title' => __( 'After Added Click Action', 'wishglut' ),
			'options' => array(
				'remove-wishlist' => __( 'Remove From Wishlist', 'wishglut' ),
				'goto-wishlist' => __( 'Goto Wishlist Page', 'wishglut' ),
				'show-already-exist' => __( 'Show Already Product Added', 'wishglut' ),
				'redirect-to-checkout' => __( 'Redirect to Checkout Page', 'wishglut' ),
			),
			'default' => 'remove-wishlist',
			'dependency' => array( 'wishlist-enable-product-page', '==', 'true' ),

		),

		array(
			'id' => 'wishlist-product-position',
			'type' => 'select',
			'title' => __( 'Select Wishlist Position', 'wishglut' ),
			'options' => array(
				'after-cart' => __( 'After Add To Cart Button', 'wishglut' ),
				'before-cart' => __( 'Before Add To Cart Button', 'wishglut' ),
				'after-product-meta' => __( 'After Product Meta', 'wishglut' ),
			),
			'default' => 'after-cart',
			'dependency' => array( 'wishlist-enable-product-page', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-product-option',
			'type' => 'button_set',
			'title' => __( 'Wishlist Option', 'wishglut' ),
			'options' => array(
				'button-with-icon' => __( 'Button Text With Icon', 'wishglut' ),
				'only-button' => __( 'Button Text Only', 'wishglut' ),
				'only-icon' => __( 'Icon Only', 'wishglut' ),
			),
			'default' => 'button-with-icon',
			'dependency' => array( 'wishlist-enable-product-page', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-product-button-text',
			'type' => 'text',
			'title' => __( 'Button Text', 'wishglut' ),
			'default' => __( 'Add To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-product-page|wishlist-product-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-product-button-text-after-added',
			'type' => 'text',
			'title' => __( 'Button Text After Added', 'wishglut' ),
			'default' => __( 'Added To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-product-page|wishlist-product-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-product-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Icon', 'wishglut' ),
			'default' => 'fa-regular fa-heart',
			'dependency' => array(
				array( 'wishlist-enable-product-page', '==', 'true' ),
				array( 'wishlist-product-option', 'any', 'button-with-icon,only-icon' ),
			),
		),
		array(
			'id' => 'wishlist-product-added-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Added Icon', 'wishglut' ),
			'default' => 'fa fa-heart',
			'dependency' => array( 'wishlist-enable-product-page|wishlist-product-option', '==|any', 'true|button-with-icon,only-icon' ),
		),

		array(
			'id' => 'wishlist-product-icon-position',
			'type' => 'button_set',
			'title' => __( 'Icon Position', 'wishglut' ),
			'options' => array(
				'text-left' => __( 'Text Left', 'wishglut' ),
				'text-right' => __( 'Text Right', 'wishglut' ),
			),
			'default' => 'text-right',
			'dependency' => array( 'wishlist-enable-product-page|wishlist-product-option', '==|==', 'true|button-with-icon' ),
		),

	),
) );

// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab',
	'title' => __( 'Shop Page', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-enable-shop-page',
			'type' => 'switcher',
			'title' => __( 'Enable Wishlist for Shop Page', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),

		array(
			'id' => 'wishlist-shop-second-click',
			'type' => 'select',
			'title' => __( 'After Added Click Action', 'wishglut' ),
			'options' => array(
				'remove-wishlist' => __( 'Remove From Wishlist', 'wishglut' ),
				'goto-wishlist' => __( 'Goto Wishlist Page', 'wishglut' ),
				'show-already-exist' => __( 'Show Already Product Added', 'wishglut' ),
				'redirect-to-checkout' => __( 'Redirect to Checkout Page', 'wishglut' ),
			),
			'default' => 'remove-wishlist',
			'dependency' => array( 'wishlist-enable-shop-page', '==', 'true' ),

		),

		array(
			'id' => 'wishlist-shop-position',
			'type' => 'select',
			'title' => __( 'Select Wishlist Position', 'wishglut' ),
			'options' => array(
				'after-cart' => __( 'After Add To Cart Button', 'wishglut' ),
				'before-cart' => __( 'Before Add To Cart Button', 'wishglut' ),
				'after-product-meta' => __( 'After Product Meta', 'wishglut' ),
			),
			'default' => 'after-cart',
			'dependency' => array( 'wishlist-enable-shop-page', '==', 'true' ),
		),


		array(
			'id' => 'wishlist-shop-option',
			'type' => 'button_set',
			'title' => __( 'Wishlist Option', 'wishglut' ),
			'options' => array(
				'button-with-icon' => __( 'Button Text With Icon', 'wishglut' ),
				'only-button' => __( 'Button Text Only', 'wishglut' ),
				'only-icon' => __( 'Icon Only', 'wishglut' ),
			),
			'default' => 'button-with-icon',
			'dependency' => array( 'wishlist-enable-shop-page', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-shop-button-text',
			'type' => 'text',
			'title' => __( 'Button Text', 'wishglut' ),
			'default' => __( 'Add To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-shop-page|wishlist-shop-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-shop-button-text-after-added',
			'type' => 'text',
			'title' => __( 'Button Text After Added', 'wishglut' ),
			'default' => __( 'Added To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-shop-page|wishlist-shop-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-shop-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Icon', 'wishglut' ),
			'default' => 'fa-regular fa-heart',
			'dependency' => array(
				array( 'wishlist-enable-shop-page', '==', 'true' ),
				array( 'wishlist-shop-option', 'any', 'button-with-icon,only-icon' ),
			),
		),

		array(
			'id' => 'wishlist-shop-added-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Added Icon', 'wishglut' ),
			'default' => 'fa fa-heart',
			'dependency' => array(
				array( 'wishlist-enable-shop-page', '==', 'true' ),
				array( 'wishlist-shop-option', 'any', 'button-with-icon,only-icon' ),
			),
		),

		array(
			'id' => 'wishlist-shop-icon-position',
			'type' => 'button_set',
			'title' => __( 'Icon Position', 'wishglut' ),
			'options' => array(
				'text-left' => __( 'Text Left', 'wishglut' ),
				'text-right' => __( 'Text Right', 'wishglut' ),
			),
			'default' => 'text-right',
			'dependency' => array( 'wishlist-enable-shop-page|wishlist-shop-option', '==|==', 'true|button-with-icon' ),
		),

	),
) );

// Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'primary_tab',
	'title' => __( 'Archive Page', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-enable-archive-page',
			'type' => 'switcher',
			'title' => __( 'Enable Wishlist for Archive Page', 'wishglut' ),
			'text_on' => __( 'Yes', 'wishglut' ),
			'text_off' => __( 'No', 'wishglut' ),
			'default' => 1,
		),

		array(
			'id' => 'wishlist-archive-second-click',
			'type' => 'select',
			'title' => __( 'After Added Click Action', 'wishglut' ),
			'options' => array(
				'remove-wishlist' => __( 'Remove From Wishlist', 'wishglut' ),
				'goto-wishlist' => __( 'Goto Wishlist Page', 'wishglut' ),
				'show-already-exist' => __( 'Show Already Product Added', 'wishglut' ),
				'redirect-to-checkout' => __( 'Redirect to Checkout Page', 'wishglut' ),
			),
			'default' => 'remove-wishlist',
			'dependency' => array( 'wishlist-enable-archive-page', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-archive-position',
			'type' => 'select',
			'title' => __( 'Select Wishlist Position', 'wishglut' ),
			'options' => array(
				'after-cart' => __( 'After Add To Cart Button', 'wishglut' ),
				'before-cart' => __( 'Before Add To Cart Button', 'wishglut' ),
				'after-product-meta' => __( 'After Product Meta', 'wishglut' ),
			),
			'default' => 'after-cart',
			'dependency' => array( 'wishlist-enable-archive-page', '==', 'true' ),
		),

		// array(
		// 	'id' => 'wishlist-archive-enable-movelist',
		// 	'type' => 'switcher',
		// 	'pro' => 'https://www.appglut.com/plugin/wishglut',
		// 	'title' => __( 'Enable MoveList Button', 'wishglut' ),
		// 	'text_on' => __( 'Yes', 'wishglut' ),
		// 	'text_off' => __( 'No', 'wishglut' ),
		// 	'default' => 1,
		// 	'dependency' => array( 'wishlist-enable-archive-page', '==', 'true' ),
		// ),

		array(
			'id' => 'wishlist-archive-select-cat-option',
			'type' => 'button_set',
			'title' => __( 'Wishlist to Show', 'wishglut' ),
			'options' => array(
				'all-categories' => __( 'All Categories & Tags', 'wishglut' ),
				'select-category' => __( 'Select Category', 'wishglut' ),
				'select-tag' => __( 'Select Tag', 'wishglut' ),
			),
			'default' => 'all-categories',
			'dependency' => array(
				array( 'wishlist-enable-archive-page', '==', 'true' ),
			),
		),

		array(
			'id' => 'wishlist-archive-select-category',
			'type' => 'select',
			'title' => esc_html__( 'Select Categories', 'wishglut' ),
			'chosen' => true,
			'multiple' => true,
			'placeholder' => esc_html__( 'Choose Category', 'wishglut' ),
			'options' => 'categories',
			'query_args' => array(
				'taxonomy' => 'product_cat',
			),
			'dependency' => array(
				array( 'wishlist-enable-archive-page', '==', 'true' ),
				array( 'wishlist-archive-select-cat-option', '==', 'select-category' ),
			),
		),

		array(
			'id' => 'wishlist-archive-select-tag',
			'type' => 'select',
			'title' => esc_html__( 'Select Tags', 'wishglut' ),
			'chosen' => true,
			'multiple' => true,
			'placeholder' => esc_html__( 'Choose Tag', 'wishglut' ),
			'options' => 'categories',
			'query_args' => array(
				'taxonomy' => 'product_tag',
			),
			'dependency' => array(
				array( 'wishlist-enable-archive-page', '==', 'true' ),
				array( 'wishlist-archive-select-cat-option', '==', 'select-tag' ),
			),
		),

		array(
			'id' => 'wishlist-archive-option',
			'type' => 'button_set',
			'title' => __( 'Wishlist Option', 'wishglut' ),
			'options' => array(
				'button-with-icon' => __( 'Button Text With Icon', 'wishglut' ),
				'only-button' => __( 'Button Text Only', 'wishglut' ),
				'only-icon' => __( 'Icon Only', 'wishglut' ),
			),
			'default' => 'button-with-icon',
			'dependency' => array( 'wishlist-enable-archive-page', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-archive-button-text',
			'type' => 'text',
			'title' => __( 'Button Text', 'wishglut' ),
			'default' => __( 'Add To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-archive-page|wishlist-archive-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-archive-button-text-after-added',
			'type' => 'text',
			'title' => __( 'Button Text After Added', 'wishglut' ),
			'default' => __( 'Added To Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-enable-archive-page|wishlist-archive-option', '==|any', 'true|button-with-icon,only-button' ),
		),

		array(
			'id' => 'wishlist-archive-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Icon', 'wishglut' ),
			'default' => 'fa-regular fa-heart',
			'dependency' => array(
				array( 'wishlist-enable-archive-page', '==', 'true' ),
				array( 'wishlist-archive-option', 'any', 'button-with-icon,only-icon' ),
			),
		),

		array(
			'id' => 'wishlist-archive-added-icon',
			'type' => 'icon',
			'title' => __( 'Wishlist Added Icon', 'wishglut' ),
			'default' => 'fa fa-heart',
			'dependency' => array(
				array( 'wishlist-enable-archive-page', '==', 'true' ),
				array( 'wishlist-archive-option', 'any', 'button-with-icon,only-icon' ),
			),
		),

		array(
			'id' => 'wishlist-archive-icon-position',
			'type' => 'button_set',
			'title' => __( 'Icon Position', 'wishglut' ),
			'options' => array(
				'text-left' => __( 'Text Left', 'wishglut' ),
				'text-right' => __( 'Text Right', 'wishglut' ),
			),
			'default' => 'text-right',
			'dependency' => array( 'wishlist-enable-archive-page|wishlist-archive-option', '==|==', 'true|button-with-icon' ),
		),

	),
) );

// Create a sub-tab for Share Buttons
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
    'parent' => 'primary_tab', // The slug id of the parent section
    'title' => __( 'Share Buttons', 'wishglut' ),
    'fields' => array(

        array(
            'id' => 'enable-social-share',
            'type' => 'switcher',
            'title' => __( 'Enable Social Share Buttons', 'wishglut' ),
            'subtitle' => __( 'Allow sharing on social platforms', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
        ),

        array(
            'id' => 'social-share-title',
            'type' => 'text',
            'title' => __( 'Share Section Title', 'wishglut' ),
            'subtitle' => __( 'Heading of share buttons', 'wishglut' ),
            'default' => 'Share Wishlist:',
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'enable-facebook-share',
            'type' => 'switcher',
            'title' => __( 'Enable Facebook Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'enable-twitter-share',
            'type' => 'switcher',
            'title' => __( 'Enable Twitter Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'twitter-share-text',
            'type' => 'text',
            'title' => __( 'Twitter Share Text', 'wishglut' ),
            'subtitle' => __( 'Default text for Twitter shares', 'wishglut' ),
            'default' => 'Check out my wishlist!',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-twitter-share', '==', 'true' )
            ),
        ),

        array(
            'id' => 'enable-whatsapp-share',
            'type' => 'switcher',
            'title' => __( 'Enable WhatsApp Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'whatsapp-share-text',
            'type' => 'textarea',
            'title' => __( 'WhatsApp Share Text', 'wishglut' ),
            'subtitle' => __( 'Default text for WhatsApp shares', 'wishglut' ),
            'default' => 'Check out my wishlist:',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-whatsapp-share', '==', 'true' )
            ),
        ),

        array(
            'id' => 'enable-pinterest-share',
            'type' => 'switcher',
            'title' => __( 'Enable Pinterest Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'enable-linkedin-share',
            'type' => 'switcher',
            'title' => __( 'Enable LinkedIn Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'linkedin-share-title',
            'type' => 'text',
            'title' => __( 'LinkedIn Share Title', 'wishglut' ),
            'subtitle' => __( 'Title for LinkedIn shares', 'wishglut' ),
            'default' => 'My Wishlist',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-linkedin-share', '==', 'true' )
            ),
        ),

        array(
            'id' => 'enable-telegram-share',
            'type' => 'switcher',
            'title' => __( 'Enable Telegram Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'telegram-share-text',
            'type' => 'text',
            'title' => __( 'Telegram Share Text', 'wishglut' ),
            'subtitle' => __( 'Default text for Telegram shares', 'wishglut' ),
            'default' => 'Check out my wishlist!',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-telegram-share', '==', 'true' )
            ),
        ),

        array(
            'id' => 'enable-email-share',
            'type' => 'switcher',
            'title' => __( 'Enable Email Share', 'wishglut' ),
            'text_on' => __( 'Yes', 'wishglut' ),
            'text_off' => __( 'No', 'wishglut' ),
            'default' => 1,
            'dependency' => array( 'enable-social-share', '==', 'true' ),
        ),

        array(
            'id' => 'email-share-subject',
            'type' => 'text',
            'title' => __( 'Email Share Subject', 'wishglut' ),
            'subtitle' => __( 'Subject line for email shares', 'wishglut' ),
            'default' => 'My Wishlist',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-email-share', '==', 'true' )
            ),
        ),

        array(
            'id' => 'email-share-body',
            'type' => 'textarea',
            'title' => __( 'Email Share Body', 'wishglut' ),
            'subtitle' => __( 'Default email body text', 'wishglut' ),
            'default' => 'Check out my wishlist:',
            'dependency' => array( 
                array( 'enable-social-share', '==', 'true' ),
                array( 'enable-email-share', '==', 'true' )
            ),
        ),

    )
) );

// Create a top-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'id' => 'secondry_tab', // Set a unique slug-like ID
	'icon' => 'fa fa-palette', // Set a unique slug-like ID
	'title' => __( 'Appearance', 'wishglut' ),
) );

// // Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'General', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-locked-background',
			'type' => 'color',
			'title' => __( 'Wishlist Locked Background', 'wishglut' ),
			'default' => '#dd3333',
		),

		array(
			'id' => 'wishlist-locked-font-color',
			'type' => 'color',
			'title' => __( 'Wishlist Locked Font Color', 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-locked-icon-color',
			'type' => 'color',
			'title' => __( 'Wishlist Locked Icon Color', 'wishglut' ),
			'default' => '#fff',
		),


		array(
			'id' => 'wishlist-notification-added-bg-color',
			'type' => 'color',
			'title' => __( 'Notification Button Color(Added)', 'wishglut' ),
			'default' => 'rgba(45,206,24,0.68)',
		),

		array(
			'id' => 'wishlist-notification-removed-bg-color',
			'type' => 'color',
			'title' => __( 'Notification Button Color(Removed)', 'wishglut' ),
			'default' => 'rgba(221,8,8,0.68)',
		),

		array(
			'id' => 'wishlist-notification-font-color',
			'type' => 'color',
			'title' => __( 'Notification Font Color', 'wishglut' ),
			'default' => '#fff',
		),

	),
) );

// // Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Wishlist Page Style', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-page-table-header-color',
			'type' => 'color',
			'title' => __( 'Table Head Background Color', 'wishglut' ),
			'default' => '#a3a3a3',
		),

		array(
			'id' => 'wishlist-page-table-head-font-color',
			'type' => 'color',
			'title' => __( 'Table Head Font Color', 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-page-subscription-btn-color',
			'type' => 'color',
			'title' => __( 'Subscribe Button Color', 'wishglut' ),
			'default' => '#0073aa',
		),

		array(
			'id' => 'wishlist-page-subscription-btn-font-color',
			'type' => 'color',
			'title' => __( 'Subscribe Button Font Color', 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-page-body-color-choice',
			'type' => 'select',
			'title' => __( 'Body Color Option', 'wishglut' ),
			'options' => array(
				'body-same-color' => __( 'Body Same Color', 'wishglut' ),
				'body-oddeven-color' => __( 'Body Odd Even Color', 'wishglut' ),
			),
			'default' => 'body-same-color',
		),

		array(
			'id' => 'wishlist-page-body-color',
			'type' => 'color',
			'title' => __( 'Table Body Color', 'wishglut' ),
			'default' => '#fff',
			'dependency' => array( 'wishlist-page-body-color-choice', '==', 'body-same-color' ),
		),

		array(
			'id' => 'wishlist-page-body-hover-color',
			'type' => 'color',
			'title' => __( 'Table Body Hover Color', 'wishglut' ),
			'default' => '#f1f1f1',
		),
		array(
			'id' => 'wishlist-page-body-odd-color',
			'type' => 'color',
			'title' => __( 'Body Odd Row Color', 'wishglut' ),
			'default' => '#fff',
			'dependency' => array( 'wishlist-page-body-color-choice', '==', 'body-oddeven-color' ),
		),

		array(
			'id' => 'wishlist-page-body-even-color',
			'type' => 'color',
			'title' => __( 'Body Even Row Color', 'wishglut' ),
			'default' => '#fff',
			'dependency' => array( 'wishlist-page-body-color-choice', '==', 'body-oddeven-color' ),
		),

		array(
			'id' => 'wishlist-page-table-head-font-color',
			'type' => 'color',
			'title' => __( 'Table Head Font Color', 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-page-table-body-font-color',
			'type' => 'color',
			'title' => __( 'Table Body Font Color', 'wishglut' ),
			'default' => '#000',
		),

		array(
			'id' => 'wishlist-page-addtocart-button-color',
			'type' => 'color',
			'title' => __( 'Add to Cart Button Color', 'wishglut' ),
			'default' => '#0073aa',
		),
		array(
			'id' => 'wishlist-page-addtocart-button-font-color',
			'type' => 'color',
			'title' => __( 'Add to Cart Button Font Color', 'wishglut' ),
			'default' => '#fff',
		),
		array(
			'id' => 'wishlist-page-checkout-button-color',
			'type' => 'color',
			'title' => __( 'Button Checkout Color', 'wishglut' ),
			'default' => '#0073aa',
		),
		array(
			'id' => 'wishlist-page-checkout-button-font-color',
			'type' => 'color',
			'title' => __( 'Button Checkout Font Color', 'wishglut' ),
			'default' => '#fff',
		),

	),
) );
// // Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Product Page Style', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-product-button-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Color", 'wishglut' ),
			'default' => '#0073aa',
		),
		array(
			'id' => 'wishlist-product-button-font-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Font Color", 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-product-wishlist-button-width',
			'type' => 'dimensions',
			'title' => __( 'Wishlist Button Width', 'wishglut' ),
			'height' => false,
			'default' => array(
				'width' => '175',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'wishlist-product-button-padding',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Padding", 'wishglut' ),
			'default' => array(
				'top' => '15',
				'right' => '20',
				'bottom' => '15',
				'left' => '20',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'wishlist-product-button-margin',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Margin", 'wishglut' ),
			'default' => array(
				'top' => '0',
				'right' => '0',
				'bottom' => '0',
				'left' => '0',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'wishlist-product-icon-color',
			'type' => 'color',
			'title' => __( "Wishlist Icon Color", 'wishglut' ),
			'default' => '#fff',
		),

		
		// array(
		// 	'id' => 'wishlist-product-movelist-button-width',
		// 	'type' => 'dimensions',
		// 	'pro' => 'https://www.appglut.com/plugin/wishglut',
		// 	'title' => __( 'Movelist Button Width', 'wishglut' ),
		// 	'height' => false,
		// 	'default' => array(
		// 		'width' => '125',
		// 		'unit' => 'px',
		// 	),
		// ),

		// array(
		// 	'id' => 'wishlist-product-move-button-padding',
		// 	'type' => 'spacing',
		// 	'pro' => 'https://www.appglut.com/plugin/wishglut',
		// 	'title' => __( "Move List Button Padding", 'wishglut' ),
		// 	'default' => array(
		// 		'top' => '15',
		// 		'right' => '20',
		// 		'bottom' => '15',
		// 		'left' => '20',
		// 		'unit' => 'px',
		// 	),
		// ),

		// array(
		// 	'id' => 'wishlist-product-move-button-margin',
		// 	'type' => 'spacing',
		// 	'pro' => 'https://www.appglut.com/plugin/wishglut',
		// 	'title' => __( "Move List Button Margin", 'wishglut' ),
		// 	'default' => array(
		// 		'top' => '0',
		// 		'right' => '0',
		// 		'bottom' => '0',
		// 		'left' => '0',
		// 		'unit' => '0',
		// 	),
		// ),

	),
) );
// // Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Shop Page Style', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-shop-button-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Color", 'wishglut' ),
			'default' => '#0073aa',
		),
		array(
			'id' => 'wishlist-shop-button-text-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Font Color", 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-shop-wishlist-button-width',
			'type' => 'dimensions',
			'title' => __( 'Wishlist Button Width', 'wishglut' ),
			'height' => false,
			'default' => array(
				'width' => '175',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'wishlist-shop-button-padding',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Padding", 'wishglut' ),
			'default' => array(
				'top' => '15',
				'right' => '20',
				'bottom' => '15',
				'left' => '20',
				'unit' => 'px',
			),
		),
		array(
			'id' => 'wishlist-shop-button-margin',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Margin", 'wishglut' ),
			'default' => array(
				'top' => '0',
				'right' => '0',
				'bottom' => '0',
				'left' => '0',
				'unit' => 'px',
			),
		),
		array(
			'id' => 'wishlist-shop-icon-color',
			'type' => 'color',
			'title' => __( "Wishlist Icon Color", 'wishglut' ),
			'default' => '#fff',
		),
		
		
	),
) );

// // Create a sub-tab
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Archive Page Style', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'wishlist-archive-button-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Color", 'wishglut' ),
			'default' => '#0073aa',
		),
		array(
			'id' => 'wishlist-archive-button-text-color',
			'type' => 'color',
			'title' => __( "Wishlist Button Font Color", 'wishglut' ),
			'default' => '#fff',
		),

		array(
			'id' => 'wishlist-archive-wishlist-button-width',
			'type' => 'dimensions',
			'title' => __( 'Wishlist Button Width', 'wishglut' ),
			'height' => false,
			'default' => array(
				'width' => '175',
				'unit' => 'px',
			),
		),


		array(
			'id' => 'wishlist-archive-button-padding',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Padding", 'wishglut' ),
			'default' => array(
				'top' => '15',
				'right' => '20',
				'bottom' => '15',
				'left' => '20',
				'unit' => 'px',
			),
		),
		array(
			'id' => 'wishlist-archive-button-margin',
			'type' => 'spacing',
			'title' => __( "Wishlist Button Margin", 'wishglut' ),
			'default' => array(
				'top' => '0',
				'right' => '0',
				'bottom' => '0',
				'left' => '0',
				'unit' => 'px',
			),
		),
		array(
			'id' => 'wishlist-archive-icon-color',
			'type' => 'color',
			'title' => __( "Wishlist Icon Color", 'wishglut' ),
			'default' => '#fff',
		),
		
	),
) );

// Create a sub-tab for Share Buttons Style
AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Share Buttons Style', 'wishglut' ),
	'fields' => array(

		array(
			'id' => 'social-share-container-margin',
			'type' => 'spacing',
			'title' => __( 'Share Container Margin', 'wishglut' ),
			'subtitle' => __( 'Margin around the entire share buttons container', 'wishglut' ),
			'default' => array(
				'top' => '20',
				'right' => '0',
				'bottom' => '20',
				'left' => '0',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-container-padding',
			'type' => 'spacing',
			'title' => __( 'Share Container Padding', 'wishglut' ),
			'subtitle' => __( 'Padding inside the share buttons container', 'wishglut' ),
			'default' => array(
				'top' => '15',
				'right' => '15',
				'bottom' => '15',
				'left' => '15',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-title-color',
			'type' => 'color',
			'title' => __( 'Share Title Color', 'wishglut' ),
			'subtitle' => __( 'Color of the "Share Wishlist:" text', 'wishglut' ),
			'default' => '#333333',
		),

		array(
			'id' => 'social-share-title-font-size',
			'type' => 'dimensions',
			'title' => __( 'Share Title Font Size', 'wishglut' ),
			'height' => false,
			'width_icon' => 'T',
			'default' => array(
				'width' => '16',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-button-size',
			'type' => 'dimensions',
			'title' => __( 'Share Button Size', 'wishglut' ),
			'subtitle' => __( 'Width and height of each share button', 'wishglut' ),
			'default' => array(
				'width' => '40',
				'height' => '40',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-button-spacing',
			'type' => 'dimensions',
			'title' => __( 'Button Spacing', 'wishglut' ),
			'subtitle' => __( 'Space between share buttons', 'wishglut' ),
			'height' => false,
			'default' => array(
				'width' => '8',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-button-border-radius',
			'type' => 'dimensions',
			'title' => __( 'Button Border Radius', 'wishglut' ),
			'subtitle' => __( 'Rounded corners for share buttons', 'wishglut' ),
			'height' => false,
			'default' => array(
				'width' => '5',
				'unit' => 'px',
			),
		),

		array(
			'id' => 'social-share-facebook-color',
			'type' => 'color',
			'title' => __( 'Facebook Button Color', 'wishglut' ),
			'default' => '#1877f2',
		),

		array(
			'id' => 'social-share-twitter-color',
			'type' => 'color',
			'title' => __( 'Twitter Button Color', 'wishglut' ),
			'default' => '#1da1f2',
		),

		array(
			'id' => 'social-share-whatsapp-color',
			'type' => 'color',
			'title' => __( 'WhatsApp Button Color', 'wishglut' ),
			'default' => '#25d366',
		),

		array(
			'id' => 'social-share-pinterest-color',
			'type' => 'color',
			'title' => __( 'Pinterest Button Color', 'wishglut' ),
			'default' => '#bd081c',
		),

		array(
			'id' => 'social-share-linkedin-color',
			'type' => 'color',
			'title' => __( 'LinkedIn Button Color', 'wishglut' ),
			'default' => '#0077b5',
		),

		array(
			'id' => 'social-share-telegram-color',
			'type' => 'color',
			'title' => __( 'Telegram Button Color', 'wishglut' ),
			'default' => '#0088cc',
		),

		array(
			'id' => 'social-share-email-color',
			'type' => 'color',
			'title' => __( 'Email Button Color', 'wishglut' ),
			'default' => '#666666',
		),

		array(
			'id' => 'social-share-icon-color',
			'type' => 'color',
			'title' => __( 'Icon Color', 'wishglut' ),
			'subtitle' => __( 'Color of the icons inside share buttons', 'wishglut' ),
			'default' => '#ffffff',
		),

		array(
			'id' => 'social-share-button-hover-opacity',
			'type' => 'slider',
			'title' => __( 'Button Hover Opacity', 'wishglut' ),
			'subtitle' => __( 'Opacity when hovering over buttons (0-100)', 'wishglut' ),
			'min' => 0,
			'max' => 100,
			'step' => 5,
			'default' => 80,
		),

	),
) );

AGWISHGLUT::createSection( $AGWISHGLUT_WISHLIST_OPTIONS, array(
	'parent' => 'secondry_tab', // The slug id of the parent section
	'title' => __( 'Menu Counter Styling', 'wishglut' ),
	'fields' => array(

		// Basic Colors
		array(
			'id' => 'wishlist-page-menu-button-text-color',
			'type' => 'color',
			'title' => __( 'Menu Button Text Color', 'wishglut' ),
			'default' => '#000000',
		),

		array(
			'id' => 'wishlist-page-menu-button-icon-color',
			'type' => 'color',
			'title' => __( 'Menu Button Icon Color', 'wishglut' ),
			'default' => '#000000',
		),

		array(
			'id' => 'wishlist-page-menu-button-background-color',
			'type' => 'color',
			'title' => __( 'Menu Button Background Color', 'wishglut' ),
			'default' => '#ffffff',
		),

		// Counter Bubble Colors
		array(
			'id' => 'wishlist-page-menu-counter-bubble-bg-color',
			'type' => 'color',
			'title' => __( 'Counter Bubble Background Color', 'wishglut' ),
			'default' => '#ff4444',
		),

		array(
			'id' => 'wishlist-page-menu-counter-bubble-text-color',
			'type' => 'color',
			'title' => __( 'Counter Bubble Text Color', 'wishglut' ),
			'default' => '#ffffff',
		),

		// Spacing & Margins
		array(
			'id' => 'wishlist-page-menu-button-text-margin',
			'type' => 'spacing',
			'title' => __( 'Menu Button Margin', 'wishglut' ),
			'default' => array(
				'top' => '5',
				'right' => '5',
				'bottom' => '5',
				'left' => '5',
			),
			'units' => array( 'px', 'em', 'rem', '%' ),
		),

		array(
			'id' => 'wishlist-page-menu-button-padding',
			'type' => 'spacing',
			'title' => __( 'Menu Button Padding', 'wishglut' ),
			'default' => array(
				'top' => '8',
				'right' => '12',
				'bottom' => '8',
				'left' => '12',
			),
			'units' => array( 'px', 'em', 'rem' ),
		),

		// Typography
		array(
			'id' => 'wishlist-page-menu-button-font-size',
			'type' => 'slider',
			'title' => __( 'Menu Button Font Size', 'wishglut' ),
			'desc' => __( 'Font size for the wishlist menu button text', 'wishglut' ),
			'default' => 14,
			'min' => 10,
			'max' => 24,
			'step' => 1,
			'unit' => 'px',
		),

		array(
			'id' => 'wishlist-page-menu-button-font-weight',
			'type' => 'select',
			'title' => __( 'Menu Button Font Weight', 'wishglut' ),
			'desc' => __( 'Font weight for the wishlist menu button text', 'wishglut' ),
			'options' => array(
				'300' => __( 'Light (300)', 'wishglut' ),
				'400' => __( 'Normal (400)', 'wishglut' ),
				'500' => __( 'Medium (500)', 'wishglut' ),
				'600' => __( 'Semi Bold (600)', 'wishglut' ),
				'700' => __( 'Bold (700)', 'wishglut' ),
			),
			'default' => '500',
		),

		array(
			'id' => 'wishlist-page-menu-icon-size',
			'type' => 'slider',
			'title' => __( 'Menu Icon Size', 'wishglut' ),
			'desc' => __( 'Size for the wishlist menu icon', 'wishglut' ),
			'default' => 16,
			'min' => 12,
			'max' => 32,
			'step' => 1,
			'unit' => 'px',
		),

		// Design Settings
		array(
			'id' => 'wishlist-page-menu-button-border-radius',
			'type' => 'slider',
			'title' => __( 'Menu Button Border Radius', 'wishglut' ),
			'desc' => __( 'Border radius for the wishlist menu button', 'wishglut' ),
			'default' => 4,
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'unit' => 'px',
		),

		array(
			'id' => 'wishlist-page-menu-button-border-width',
			'type' => 'slider',
			'title' => __( 'Menu Button Border Width', 'wishglut' ),
			'desc' => __( 'Border width for the wishlist menu button', 'wishglut' ),
			'default' => 0,
			'min' => 0,
			'max' => 5,
			'step' => 1,
			'unit' => 'px',
		),

		array(
			'id' => 'wishlist-page-menu-button-border-color',
			'type' => 'color',
			'title' => __( 'Menu Button Border Color', 'wishglut' ),
			'desc' => __( 'Border color for the wishlist menu button', 'wishglut' ),
			'default' => '#cccccc',
			'dependency' => array( 'wishlist-page-menu-button-border-width', '!=', '0' ),
		),

		// Gap between elements
		array(
			'id' => 'wishlist-page-menu-elements-gap',
			'type' => 'slider',
			'title' => __( 'Elements Gap', 'wishglut' ),
			'desc' => __( 'Gap between icon, text and counter bubble', 'wishglut' ),
			'default' => 8,
			'min' => 0,
			'max' => 20,
			'step' => 1,
			'unit' => 'px',
		),

		// Hover Effects
		array(
			'id' => 'wishlist-page-menu-button-hover-bg-color',
			'type' => 'color',
			'title' => __( 'Menu Button Hover Background', 'wishglut' ),
			'default' => '#f5f5f5',
		),

		array(
			'id' => 'wishlist-page-menu-button-hover-text-color',
			'type' => 'color',
			'title' => __( 'Menu Button Hover Text Color', 'wishglut' ),
			'default' => '#000000',
		),

		array(
			'id' => 'wishlist-page-menu-button-hover-icon-color',
			'type' => 'color',
			'title' => __( 'Menu Button Hover Icon Color', 'wishglut' ),
			'default' => '#000000',
		),

		// Animation Settings
		array(
			'id' => 'wishlist-page-menu-button-transition-duration',
			'type' => 'slider',
			'title' => __( 'Hover Transition Duration', 'wishglut' ),
			'desc' => __( 'Duration of hover transition effect in milliseconds', 'wishglut' ),
			'default' => 300,
			'min' => 100,
			'max' => 1000,
			'step' => 50,
			'unit' => 'ms',
		),

		array(
			'id' => 'wishlist-page-menu-button-hover-transform',
			'type' => 'switcher',
			'title' => __( 'Enable Hover Transform', 'wishglut' ),
			'desc' => __( 'Enable slight upward movement on hover', 'wishglut' ),
			'default' => true,
		),

		// Counter Bubble Advanced Settings
		array(
			'id' => 'wishlist-page-menu-counter-bubble-font-size',
			'type' => 'slider',
			'title' => __( 'Counter Bubble Font Size', 'wishglut' ),
			'desc' => __( 'Font size for the counter bubble text', 'wishglut' ),
			'default' => 12,
			'min' => 8,
			'max' => 18,
			'step' => 1,
			'unit' => 'px',
		),

		array(
			'id' => 'wishlist-page-menu-counter-bubble-min-width',
			'type' => 'slider',
			'title' => __( 'Counter Bubble Min Width', 'wishglut' ),
			'desc' => __( 'Minimum width for the counter bubble', 'wishglut' ),
			'default' => 18,
			'min' => 16,
			'max' => 30,
			'step' => 1,
			'unit' => 'px',
		),

		// Visibility Settings
		array(
			'id' => 'wishlist-page-menu-button-show-text',
			'type' => 'switcher',
			'title' => __( 'Show Menu Button Text', 'wishglut' ),
			'desc' => __( 'Display text alongside the wishlist icon', 'wishglut' ),
			'default' => true,
		),

		array(
			'id' => 'wishlist-page-menu-button-text',
			'type' => 'text',
			'title' => __( 'Menu Button Text', 'wishglut' ),
			'desc' => __( 'Custom text for the wishlist menu button', 'wishglut' ),
			'default' => __( 'Wishlist', 'wishglut' ),
			'dependency' => array( 'wishlist-page-menu-button-show-text', '==', 'true' ),
		),

		array(
			'id' => 'wishlist-page-menu-button-show-counter',
			'type' => 'switcher',
			'title' => __( 'Show Counter Bubble', 'wishglut' ),
			'desc' => __( 'Display counter bubble with wishlist item count', 'wishglut' ),
			'default' => true,
		),

		array(
			'id' => 'wishlist-page-menu-button-hide-empty-counter',
			'type' => 'switcher',
			'title' => __( 'Hide Counter When Empty', 'wishglut' ),
			'desc' => __( 'Hide counter bubble when wishlist is empty', 'wishglut' ),
			'default' => true,
			'dependency' => array( 'wishlist-page-menu-button-show-counter', '==', 'true' ),
		),

	),
) );





// Allow pro plugin to add settings

do_action( 'wishglut_wishlist_pro_settings', $AGWISHGLUT_WISHLIST_OPTIONS );
