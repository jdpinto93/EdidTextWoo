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
		$this->label = __( 'Personalizador', '_JPinto' );

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
			'shop_loop'    => __( 'Listado', '_JPinto' ),
			'product_page' => __( 'Pagina de Productos', '_JPinto' ),
			'checkout'     => __( 'Pafina de Pagos', '_JPinto' ),
			'misc'         => __( 'Impuestos', '_JPinto' )
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
						'title' => __( 'Texto del botón Agregar al carrito', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'add_to_cart_text',
						'title'    => __( 'Producto Simple', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Agregar al carrito para productos simples en todas las páginas de Listados', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'variable_add_to_cart_text',
						'title'    => __( 'Variable Product', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Agregar al carrito para productos Variables en todas las páginas de Listados', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'grouped_add_to_cart_text',
						'title'    => __( 'Grupo de Productos', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Agregar al carrito para productos simples en todas las páginas de bucle', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'out_of_stock_add_to_cart_text',
						'title'    => __( 'Productos Fuera de Stock', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Agregar al carrito para productos agotados en todas las páginas de Listados', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Venta Rapida', '_JPinto' ),
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
						'title' => __( 'Diseño', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'loop_shop_per_page',
						'title'    => __( 'Productos mostrados por página', '_JPinto' ),
						'desc_tip' => __( 'Cambia el número de productos mostrados por página', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'loop_shop_columns',
						'title'    => __( 'Columnas de productos mostradas por página', '_JPinto' ),
						'desc_tip' => __( 'Cambia el número de columnas mostradas por página', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_thumbnails_columns',
						'title'    => __( 'Columnas de miniaturas de productos mostradas', '_JPinto' ),
						'desc_tip' => __( 'Cambia el número de columnas de miniaturas de productos que se muestran', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' )

				),

			'product_page' =>

				array(

					array(
						'title' => __( 'Titulo de las Tablas', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_product_description_tab_title',
						'title'    => __( 'Descripcion', '_JPinto' ),
						'desc_tip' => __( 'Cambia el título de la pestaña Descripción de Productos', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_additional_information_tab_title',
						'title'    => __( 'Informacion Adicional', '_JPinto' ),
						'desc_tip' => __( 'Cambia el título de la pestaña Información adicional', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Encabezados de pestañas', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_product_description_heading',
						'title'    => __( 'Descripción del producto', '_JPinto' ),
						'desc_tip' => __( 'Cambia el encabezado de la pestaña Descripción del producto', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_product_additional_information_heading',
						'title'    => __( 'Informacion Adicional', '_JPinto' ),
						'desc_tip' => __( 'Cambia el encabezado de la pestaña Informacion Adicional del producto', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Boton de Añadir Al Carrito', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_add_to_cart_text',
						'title'    => __( 'Todos los tipos de productos', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Agregar al carrito en la página de un solo producto para todo tipo de producto', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Texto de Producto agotado', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_out_of_stock_text',
						'title'    => __( 'Texto de Producto agotado', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto de agotado en las páginas de productos. Predeterminado: "Agotado"', '_JPinto' ),
						'type'     => 'text',
					),

					array(
						'id'       => 'single_backorder_text',
						'title'    => __( 'Texto de pedido pendiente', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del pedido pendiente en las páginas de productos. Predeterminado: "Disponible en pedido pendiente"', '_JPinto' ),
						'type'     => 'text',
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Venta Rapida', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'single_sale_flash_text',
						'title'    => __( 'Texto de la insignia de Oferta', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del flash de venta en las páginas de productos. Default: "Sale!"', '_JPinto' ),
						'type'     => 'text',
						/* translators: Placeholders: %1$s - <code>, %2$s - </code> */
						'desc'     => sprintf( __( 'Use %1$s{percent}%2$s insertar porcentaje de descuento, e.g., "{percent} off!"', '_JPinto' ), '<code>', '</code>' ) . '<br />' . __( 'Shows "up to n%" for grouped or variable products if multiple percentages are possible.', '_JPinto' ),
					),

					array( 'type' => 'sectionend' ),
				),

			'checkout' =>

				array(

					array(
						'title' => __( 'Mensajes', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_checkout_must_be_logged_in_message',
						'title'    => __( 'Texto Debe iniciar sesión', '_JPinto' ),
						'desc_tip' => __( 'Cambia el mensaje que se muestra cuando un cliente debe iniciar sesión para pagar', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_checkout_coupon_message',
						'title'    => __( 'Texto del cupón', '_JPinto' ),
						'desc_tip' => __( 'Cambia el mensaje que se muestra si el formulario de cupón está habilitado al finalizar la compra', '_JPinto' ),
						'type'     => 'text',
						'desc'     => sprintf( '<code>%s ' . esc_attr( '<a href="#" class="showcoupon">%s</a>' ) . '</code>', '¿Tiene un cupón?', 'Haga clic aquí para ingresar su código' ),
					),

					array(
						'id'       => 'woocommerce_checkout_login_message',
						'title'    => __( 'Tento de Inicio de Sesion', '_JPinto' ),
						'desc_tip' => __( 'Cambia el mensaje que se muestra si los clientes pueden iniciar sesión al finalizar la compra', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' ),

					array(
						'title' => __( 'Otros', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_create_account_default_checked',
						'title'    => __( 'Crear casilla de cuenta por defecto' ),
						'desc_tip' => __( 'Controle el estado predeterminado para la casilla de verificación Crear cuenta', '_JPinto' ),
						'type'     => 'select',
						'options'  => array(
							'customizer_true'  => __( 'Activo', '_JPinto' ),
							'customizer_false' => __( 'Inactivo', '_JPinto' ),
						),
						'default'  => 'customizer_false',
					),

					array(
						'id'       => 'woocommerce_order_button_text',
						'title'    => __( 'Botón Enviar pedido', '_JPinto' ),
						'desc_tip' => __( 'Cambia el texto del botón Realizar pedido al finalizar la compra', '_JPinto' ),
						'type'     => 'text'
					),

					array( 'type' => 'sectionend' )

				),

			'misc' =>

				array(

					array(
						'title' => __( 'Impuestos', '_JPinto' ),
						'type'  => 'title'
					),

					array(
						'id'       => 'woocommerce_rate_label',
						'title'    => __( 'Etiqueta de impuestos', '_JPinto' ),
						'desc_tip' => __( 'Cambia la etiqueta de Impuestos. Los valores predeterminados son impuestos para EE. UU., IVA para países europeos', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_countries_inc_tax_or_vat',
						'title'    => __( 'etiqueta de impuestos Incluido', '_JPinto' ),
						'desc_tip' => __( 'Cambia la etiqueta Impuestos incluidos. El valor predeterminado es Inc. tax para EE. UU., Inc. VAT para países europeos', '_JPinto' ),
						'type'     => 'text'
					),

					array(
						'id'       => 'woocommerce_countries_ex_tax_or_vat',
						'title'    => __( 'etiqueta de impuestos No Incluidos', '_JPinto' ),
						'desc_tip' => __( 'Cambia la etiqueta Impuestos excluidos. Predeterminado a Exc. impuestos para EE. UU., Exc. IVA para países europeos', '_JPinto' ),
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
