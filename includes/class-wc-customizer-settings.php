<?php
 /**
 * Plugin Name:       Field Editor
 * Plugin URI:        http://www.webmasteryagency.com
 * Description:       Edita los campos del formulario de woocommerce, importa y exporta los ajustes para que sea facil la mugracion o reemplazar ajustes
 * Version:           1.1.3
 * Requires at least: 5.2
 * Requires PHP:      7.2.2
 * Author:            Jose Pinto
 * Author URI:        http://www.webmasteryagency.com
 * License:           GPL v3 or later
 * Domain Path: /lang
 * Text Domain _JPinto
 */

defined( 'ABSPATH' ) or exit;

/**
 * Settings
 *
 * Adds UX for adding/modifying customizations
 *
 * @since 2.0.0
 */
class WC_Customizer_Settings extends WC_Settings_Page {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->id    = 'customizer';
		$this->label = __( 'Customizer', '_JPinto' );

		parent::__construct();

		$this->customizations = get_option( 'wc_customizer_active_customizations', array() );
	}


	/**
	 * Get sections
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_sections() {

		return array(
			'shop_loop'    => __( 'Shop Loop', '_JPinto' ),
			'product_page' => __( 'Product Page', '_JPinto' ),
			'checkout'     => __( 'Checkout', '_JPinto' ),
			'misc'         => __( 'Misc', '_JPinto' )
		);
	}


	/**
	 * Render the settings for the current section
	 *
	 * @since 2.0.0
	 */
	public function output() {

		$settings = $this->get_settings();

		// inject the actual setting value before outputting the fields
		// ::output_fields() uses get_option() but customizations are stored
		// in a single option so this dynamically returns the correct value
		foreach ( $this->customizations as $filter => $value ) {

			add_filter( "pre_option_{$filter}", array( $this, 'get_customization' ) );
		}

		WC_Admin_Settings::output_fields( $settings );
	}


	/**
	 * Return the customization value for the given filter
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_customization() {

		$filter = str_replace( 'pre_option_', '', current_filter() );

		return isset( $this->customizations[ $filter ] ) ? $this->customizations[ $filter ] : '';
	}


	/**
	 * Save the customizations
	 *
	 * @since 2.0.0
	 */
	public function save() {

		foreach ( $this->get_settings() as $field ) {

			// skip titles, etc
			if ( ! isset( $field['id'] ) ) {
				continue;
			}

			if ( ! empty( $_POST[ $field['id'] ] ) ) {

				$this->customizations[ $field['id'] ] = wp_kses_post( stripslashes( $_POST[ $field['id'] ] ) );

			} elseif ( isset( $this->customizations[ $field['id'] ] ) ) {

				unset( $this->customizations[ $field['id'] ] );
			}
		}

		update_option( 'wc_customizer_active_customizations', $this->customizations );
	}


	/**
	 * Return admin fields in proper format for outputting / saving
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = array(

			'shop_loop' =>

				array(

					array(
						'title' => __( 'Add to Cart Button Text', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'add_to_cart_text',
						'title'    => __( 'Simple Product', '_JPinto' ),
						'desc_tip' => __( 'Changes the add to cart button text for simple products on all loop pages', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'variable_add_to_cart_text',
						'title'    => __( 'Variable Product', '_JPinto' ),
						'desc_tip' => __( 'Changes the add to cart button text for variable products on all loop pages', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'grouped_add_to_cart_text',
						'title'    => __( 'Grouped Product', '_JPinto' ),
						'desc_tip' => __( 'Changes the add to cart button text for grouped products on all loop pages', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'out_of_stock_add_to_cart_text',
						'title'    => __( 'Out of Stock Product', '_JPinto' ),
						'desc_tip' => __( 'Changes the add to cart button text for out of stock products on all loop pages', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Sale Flash', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'loop_sale_flash_text',
						'title'    => __( 'Sale badge text', '_JPinto' ),
						'desc_tip' => __( 'Changes text for the sale flash on all loop pages. Default: "Sale!"', '_JPinto' ),
						'type'     => 'text',
						/* translators: Placeholders: %1$s - <code>, %2$s - </code> */
						'desc'     => sprintf( __( 'Use %1$s{percent}%2$s to insert percent off, e.g., "{percent} off!"', '_JPinto' ), '<code>', '</code>' ) . '<br />' . __( 'Shows "up to n%" for grouped or variable products if multiple percentages are possible.', '_JPinto' ),
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Layout', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'loop_shop_per_page',
						'title'    => __( 'Products displayed per page', '_JPinto' ),
						'desc_tip' => __( 'Changes the number of products displayed per page', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'loop_shop_columns',
						'title'    => __( 'Product columns displayed per page', '_JPinto' ),
						'desc_tip' => __( 'Changes the number of columns displayed per page', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_thumbnails_columns',
						'title'    => __( 'Product thumbnail columns displayed', '_JPinto' ),
						'desc_tip' => __( 'Changes the number of product thumbnail columns displayed', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' )

				),

			'product_page' =>

				array(

					array(
						'title' => __( 'Tab Titles', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_product_description_tab_title',
						'title'    => __( 'Product Description', '_JPinto' ),
						'desc_tip' => __( 'Changes the Production Description tab title', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_additional_information_tab_title',
						'title'    => __( 'Additional Information', '_JPinto' ),
						'desc_tip' => __( 'Changes the Additional Information tab title', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Tab Content Headings', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_product_description_heading',
						'title'    => __( 'Product Description', '_JPinto' ),
						'desc_tip' => __( 'Changes the Product Description tab heading', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_additional_information_heading',
						'title'    => __( 'Additional Information', '_JPinto' ),
						'desc_tip' => __( 'Changes the Additional Information tab heading', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Add to Cart Button Text', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_add_to_cart_text',
						'title'    => __( 'All Product Types', '_JPinto' ),
						'desc_tip' => __( 'Changes the Add to Cart button text on the single product page for all product type', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Out of Stock Text', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_out_of_stock_text',
						'title'    => __( 'Out of Stock text', '_JPinto' ),
						'desc_tip' => __( 'Changes text for the out of stock on product pages. Default: "Out of stock"', '_JPinto' ),
						'type'     => 'text',
					),

					array(
						'id'       => 'single_backorder_text',
						'title'    => __( 'Backorder text', '_JPinto' ),
						'desc_tip' => __( 'Changes text for the backorder on product pages. Default: "Available on backorder"', '_JPinto' ),
						'type'     => 'text',
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Sale Flash', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_sale_flash_text',
						'title'    => __( 'Sale badge text', '_JPinto' ),
						'desc_tip' => __( 'Changes text for the sale flash on product pages. Default: "Sale!"', '_JPinto' ),
						'type'     => 'text',
						/* translators: Placeholders: %1$s - <code>, %2$s - </code> */
						'desc'     => sprintf( __( 'Use %1$s{percent}%2$s to insert percent off, e.g., "{percent} off!"', '_JPinto' ), '<code>', '</code>' ) . '<br />' . __( 'Shows "up to n%" for grouped or variable products if multiple percentages are possible.', '_JPinto' ),
					),

					array( 'type' => 'sectionend' ),
				),

			'checkout' =>

				array(

					array(
						'title' => __( 'Messages', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_checkout_must_be_logged_in_message',
						'title'    => __( 'Must be logged in text', '_JPinto' ),
						'desc_tip' => __( 'Changes the message displayed when a customer must be logged in to checkout', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_checkout_coupon_message',
						'title'    => __( 'Coupon text', '_JPinto' ),
						'desc_tip' => __( 'Changes the message displayed if the coupon form is enabled on checkout', '_JPinto' ),
						'type'     => 'text',
						'desc'     => sprintf( '<code>%s ' . esc_attr( '<a href="#" class="showcoupon">%s</a>' ) . '</code>', 'Have a coupon?', 'Click here to enter your code' ),
					),

					array(
						'id'       => 'woocommerce_checkout_login_message',
						'title'    => __( 'Login text', '_JPinto' ),
						'desc_tip' => __( 'Changes the message displayed if customers can login at checkout', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Misc', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_create_account_default_checked',
						'title'    => __( 'Create Account checkbox default' ),
						'desc_tip' => __( 'Control the default state for the Create Account checkbox', '_JPinto' ),
						'type'     => 'select',
						'options'  => array(
							'customizer_true'  => __( 'Checked', '_JPinto' ),
							'customizer_false' => __( 'Unchecked', '_JPinto' ),
						),
						'default'  => 'customizer_false',
					),

					array(
						'id'       => 'woocommerce_order_button_text',
						'title'    => __( 'Submit Order button', '_JPinto' ),
						'desc_tip' => __( 'Changes the Place Order button text on checkout', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' )

				),

			'misc' =>

				array(

					array(
						'title' => __( 'Tax', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_rate_label',
						'title'    => __( 'Tax Label', '_JPinto' ),
						'desc_tip' => __( 'Changes the Taxes label. Defaults to Tax for USA, VAT for European countries', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_countries_inc_tax_or_vat',
						'title'    => __( 'Including Tax Label', '_JPinto' ),
						'desc_tip' => __( 'Changes the Including Taxes label. Defaults to Inc. tax for USA, Inc. VAT for European countries', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_countries_ex_tax_or_vat',
						'title'    => __( 'Excluding Tax Label', '_JPinto' ),
						'desc_tip' => __( 'Changes the Excluding Taxes label. Defaults to Exc. tax for USA, Exc. VAT for European countries', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

				),
		);

		/**
		 * Filters the available customizer settings.
		 *
		 * @since 2.6.0
		 *
		 * @param array $settings the plugin settings
		 */
		$settings = apply_filters( 'wc_customizer_settings', $settings );

		$current_section = isset( $GLOBALS['current_section'] ) ? $GLOBALS['current_section'] : 'shop_loop';

		return isset( $settings[ $current_section ] ) ?  $settings[ $current_section ] : $settings['shop_loop'];
	}


}

// setup settings
return wc_customizer()->settings = new WC_Customizer_Settings();
