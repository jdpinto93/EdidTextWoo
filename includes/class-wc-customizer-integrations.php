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
 * Class WC_Customizer_Integrations.
 *
 * Adds integration code for other WooCommerce extensions.
 *
 * @since 2.6.0
 */
class WC_Customizer_Integrations {


	/**
	 * WC_Customizer_Integrations constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {

		if ( WC_Customizer::is_plugin_active( 'woocommerce-product-bundles.php' ) ) {

			add_filter( 'wc_customizer_settings', array( $this, 'add_bundles_settings' ) );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'customize_bundle_add_to_cart_text' ), 150, 2 );
		}
	}


	/**
	 * Adds settings when Product Bundles is active.
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings the settings array
	 * @return array updated settings
	 */
	public function add_bundles_settings( $settings ) {

		$new_settings = array();

		foreach ( $settings as $section => $settings_group ) {

			$new_settings[ $section ] = array();

			foreach ( $settings_group as $setting ) {

				$new_settings[ $section ][] = $setting;

				if ( 'shop_loop' === $section && isset( $setting['id'] ) && 'grouped_add_to_cart_text' === $setting['id'] ) {

					// insert bundle settings after the grouped product text
					$new_settings[ $section ][] = array(
						'id'       => 'bundle_add_to_cart_text',
						'title'    => __( 'Bundle Product', '_JPinto' ),
						'desc_tip' => __( 'Changes the add to cart button text for bundle products on all loop pages', '_JPinto' ),
						'type'     => 'text'
					);
				}
			}
		}

		return $new_settings;
	}


	/**
	 * Customizes the add to cart button for bundle products.
	 *
	 * @since  2.6.0
	 *
	 * @param string $text add to cart text
	 * @param WC_Product $product product object
	 * @return string modified add to cart text
	 */
	public function customize_bundle_add_to_cart_text( $text, $product ) {

		if ( isset( wc_customizer()->filters['bundle_add_to_cart_text'] ) && $product->is_type( 'bundle' ) ) {

			// bundle add to cart text
			$text = wc_customizer()->filters['bundle_add_to_cart_text'];
		}

		return $text;
	}


}
