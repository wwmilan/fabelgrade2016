<?php

if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( !class_exists( 'TC_Settings_General' ) ) {

	class TC_Settings_General {

		function __construct() {
			
		}

		function TC_Settings_General() {
			$this->__construct();
		}

		function get_settings_general_sections() {
			global $tc_gateway_plugins;
			$sections = array(
				array(
					'name'			 => 'store_settings',
					'title'			 => __( 'Store Settings', 'tc' ),
					'description'	 => '',
				),
				array(
					'name'			 => 'page_settings',
					'title'			 => __( 'Pages', 'tc' ),
					'description'	 => '',
				),
				array(
					'name'			 => 'menu_settings',
					'title'			 => __( 'Menu', 'tc' ),
					'description'	 => '',
				),
				array(
					'name'			 => 'miscellaneous_settings',
					'title'			 => __( 'Miscellaneous', 'tc' ),
					'description'	 => '',
				)
			);

			if ( count( $tc_gateway_plugins ) > 10 ) {
				if ( !defined( 'TC_LCK' ) && !defined( 'TC_NU' ) ) {
					$sections[] = array(
						'name'			 => 'license',
						'title'			 => __( 'License Key', 'tc' ),
						'description'	 => '',
					);
				}
			}

			$sections = apply_filters( 'tc_settings_general_sections', $sections );

			return $sections;
		}

		function get_settings_general_fields() {

			$tc_general_settings = get_option( 'tc_general_setting', false );

			if ( !defined( 'TC_LCK' ) && !defined( 'TC_NU' ) ) {
				$license_settings_default_fields = array(
					array(
						'field_name'		 => 'license_key',
						'field_title'		 => __( 'License Key', 'tc' ),
						'field_type'		 => 'option',
						'default_value'		 => '',
						'field_description'	 => __( 'License Key is required if you want to update plugin from within the WordPress. You can obtain the key from you account page.', 'tc' ),
						'section'			 => 'license'
					),
				);
			}

			$store_settings_default_fields = array(
				array(
					'field_name'		 => 'currencies',
					'field_title'		 => __( 'Currency', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_global_currencies',
					'default_value'		 => 'USD',
					'field_description'	 => __( 'This is currency used for display purposes. You have to match gateway currency with this one.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'currency_symbol',
					'field_title'		 => __( 'Currency Symbol', 'tc' ),
					'field_type'		 => 'option',
					'default_value'		 => '$',
					'field_description'	 => __( 'Put currency symbol (e.g $) which will be shown instead of the currency ISO code (e.g USD)' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'currency_position',
					'field_title'		 => __( 'Currency Position', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_currency_positions',
					'field_description'	 => '',
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'price_format',
					'field_title'		 => __( 'Price Format', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_price_formats',
					'field_description'	 => '',
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'tax_rate',
					'field_title'		 => __( 'Tax Rate (%)', 'tc' ),
					'field_type'		 => 'option',
					'default_value'		 => '0',
					'field_description'	 => __( 'Empty or zero means that no tax will be applied on orders', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'tax_inclusive',
					'field_title'		 => __( 'Prices inclusive of tax', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_tax_inclusive',
					'default_value'		 => 'no',
					'field_description'	 => __( 'All prices set for tickets will be inclusive of tax.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'show_tax_rate',
					'field_title'		 => __( 'Show Tax in Cart', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_tax_rate',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'Show Tax in Cart. You may hide tax if you do not use it.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'tax_label',
					'field_title'		 => __( 'Tax Label', 'tc' ),
					'field_type'		 => 'option',
					'default_value'		 => 'Tax',
					'field_description'	 => '',
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'use_global_fees',
					'field_title'		 => __( 'Use Global Fees', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_yes_no',
					'default_value'		 => 'no',
					'field_description'	 => __( 'If set to Yes, each ticket type will have the same ticket fees. If set to No, ticket fees could be defined on the ticket type level.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'global_fee_type',
					'field_title'		 => __( 'Global Fee Type', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_global_fee_type',
					'default_value'		 => 'percentage',
					'field_description'	 => __( 'Set type of global fees.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'global_fee_value',
					'field_title'		 => __( 'Global Fee (value)', 'tc' ),
					'field_type'		 => 'option',
					'default_value'		 => '0',
					'field_description'	 => __( 'Example: 10. Value would be percentage of fixed based on the above selected option.' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'show_fees',
					'field_title'		 => __( 'Show Fees', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_fees',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'Show Fees in Cart. You may hide fees if you do not use it.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'fees_label',
					'field_title'		 => __( 'Fees Label', 'tc' ),
					'field_type'		 => 'option',
					'default_value'		 => 'Fees',
					'field_description'	 => '',
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'force_login',
					'field_title'		 => __( 'Force Login', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_force_login',
					'default_value'		 => 'no',
					'field_description'	 => __( 'Users must log in first in order to purchase and/or download tickets', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'show_owner_fields',
					'field_title'		 => __( 'Show Ticket Owner Fields', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_owner_fields',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'Show Ticket Owner fields on the Cart page. If this option is not selected, owner info fields will not be collected and shown on the ticket.', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'show_owner_email_field',
					'field_title'		 => __( 'Show E-mail for Option For Ticket Owners', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_owner_fields',
					'default_value'		 => 'no',
					'field_description'	 => __( 'Show / Hide field for owner to write theirs e-mail', 'tc' ),
					'section'			 => 'store_settings'
				),
				array(
					'field_name'		 => 'show_discount_field',
					'field_title'		 => __( 'Show Discount Code', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_discount_code_field',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'Show / Hide discount code field on the cart page', 'tc' ),
					'section'			 => 'store_settings'
				),
			);

			$store_settings_default_fields = apply_filters( 'tc_general_settings_store_fields', $store_settings_default_fields );

			$pages_settings_default_fields = array(
				array(
					'field_name'		 => 'tc_cart_page_id',
					'field_title'		 => __( 'Cart Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_cart_page_settings',
					'default_value'		 => get_option( 'tc_cart_page_id', -1 ),
					'field_description'	 => __( 'Users will be able to see their cart contents, insert buyer and ticket owner(s) info on this page. <strong>You can add this page to the site menu for easy accessibility.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_payment_page_id',
					'field_title'		 => __( 'Payment Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_payment_page_settings',
					'default_value'		 => get_option( 'tc_payment_page_id', -1 ),
					'field_description'	 => __( 'Users will choose payment method on this page. <br /><strong>Do NOT add this page directly to the site menu since it will be automatically used by the plugin.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_confirmation_page_id',
					'field_title'		 => __( 'Payment Confirmation Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_confirmation_page_settings',
					'default_value'		 => get_option( 'tc_confirmation_page_id', -1 ),
					'field_description'	 => __( 'This page will be shown after completed payment. Information about payment status and link to order page will be visible on confimation page. <br /><strong>Do NOT add this page directly to the site menu since it will be automatically used by the plugin.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_order_page_id',
					'field_title'		 => __( 'Order Details Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_order_page_settings',
					'default_value'		 => get_option( 'tc_order_page_id', -1 ),
					'field_description'	 => __( 'The page where buyers will be able to check order status and / or download their ticket(s). <br /><strong>Do NOT add this page directly to the site menu since it will be automatically used by the plugin.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_process_payment_use_virtual',
					'field_title'		 => __( 'Use Virtual Process Payment Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_yes_no',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'If you\'re experiencing errors with process payment page, set this option to "No" and set "Process Payment Page".', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_process_payment_page_id',
					'field_title'		 => __( 'Process Payment Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_process_payment_page_settings',
					'default_value'		 => get_option( 'tc_process_payment_page_id', -1 ),
					'field_description'	 => __( 'The page used by the plugin internally to process payments. <br /><strong>Do NOT add this page directly to the site menu since it will be automatically used by the plugin.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_ipn_page_use_virtual',
					'field_title'		 => __( 'Use Virtual IPN (instant payment notification) Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_yes_no',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'If you\'re experiencing errors with IPN page, set this option to "No" and set the "IPN Payment Page".', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_ipn_page_id',
					'field_title'		 => __( 'IPN (instant payment notification) Page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_ipn_page_settings',
					'default_value'		 => get_option( 'tc_process_payment_page_id', -1 ),
					'field_description'	 => __( 'The page used by the plugin internally to receive payment statuses from various payment gateways like PayPal Standard, VoguePay, 2Checkout. <br /><strong>Do NOT add this page directly to the site menu since it will be automatically used by the plugin.</strong>', 'tc' ),
					'section'			 => 'page_settings'
				),
				array(
					'field_name'		 => 'tc_pages_id',
					'field_title'		 => __( 'Pages', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_pages_settings',
					'default_value'		 => '',
					'field_description'	 => __( 'Create pages required by the plugin', 'tc' ),
					'section'			 => 'page_settings'
				),
			);

			$pages_settings_default_fields = apply_filters( 'tc_general_settings_page_fields', $pages_settings_default_fields );

			$menu_settings_default_fields = array(
				array(
					'field_name'		 => 'show_cart_menu_item',
					'field_title'		 => __( 'Show Cart Menu', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_show_cart',
					'default_value'		 => 'no',
					'field_description'	 => __( 'Show link to Cart in Menu on the front automatically.', 'tc' ),
					'section'			 => 'menu_settings'
				),
			);

			$menu_settings_default_fields = apply_filters( 'tc_general_settings_menu_fields', $menu_settings_default_fields );

			$miscellaneous_settings_default_fields = array(
				array(
					'field_name'		 => 'global_admin_per_page',
					'field_title'		 => __( 'Admin results per page', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_get_global_admin_per_page',
					'default_value'		 => '10',
					'field_description'	 => __( 'Set number of result rows show in the admin tables of the plugin', 'tc' ),
					'section'			 => 'miscellaneous_settings'
				),
				array(
					'field_name'		 => 'use_order_details_pretty_links',
					'field_title'		 => __( 'Order Details Pretty Links', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_yes_no',
					'default_value'		 => 'yes',
					'field_description'	 => __( 'Choose whether you want to use pretty permalinks for order details page or not. Set it to "No" in case that you see 404 page for order details (this could be caused by a third-party plugin or a theme). ', 'tc' ),
					'section'			 => 'miscellaneous_settings'
				),
				array(
					'field_name'		 => 'ticket_template_auto_pagebreak',
					'field_title'		 => __( 'Multipage Ticket Template', 'tc' ),
					'field_type'		 => 'function',
					'function'			 => 'tc_yes_no',
					'default_value'		 => 'no',
					'field_description'	 => __( 'Allow ticket template to show on more than one page in the PDF.', 'tc' ),
					'section'			 => 'miscellaneous_settings'
				)
			);

			$miscellaneous_settings_default_fields = apply_filters( 'tc_general_settings_miscellaneous_fields', $miscellaneous_settings_default_fields );

			//

			$default_fields	 = array_merge( $store_settings_default_fields, $pages_settings_default_fields );
			$default_fields	 = array_merge( $menu_settings_default_fields, $default_fields );
			$default_fields	 = array_merge( $miscellaneous_settings_default_fields, $default_fields );

			if ( !defined( 'TC_LCK' ) && !defined( 'TC_NU' ) ) {
				$default_fields = array_merge( $license_settings_default_fields, $default_fields );
			}

			return apply_filters( 'tc_settings_general_fields', $default_fields );
		}

	}

}
?>
