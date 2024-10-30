<?php
/**
 * WooCommerce Image With Order enqueue class.
 *
 * @package WooCommerce/Enqueue
 * @since 1.0
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooBWImageOrderEnqueue' ) ){
    
    class WooBWImageOrderEnqueue{
        
        /**
		 * Construct
		 * @since 1.0
		 * @version 1.0
		 */
         public function __construct(){
            add_action( 'admin_enqueue_scripts', array( $this, 'woo_bw_image_order_admin_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'woo_bw_image_order_admin_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'woo_bw_image_order_view_styles' ) );
		}
        
        /**
		 * Register Scripts Admin
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_admin_scripts() {
            wp_enqueue_script( 'jquery-ui-tooltip', false, array('jquery') );
        	wp_register_script( 'woo-bw-image-order-js-admin', WC_BW_ORDER_JS . 'admin-script.js' , array( 'jquery' ), WC_BW_ORDER_VERSION, true );
            wp_enqueue_script( 'woo-bw-image-order-js-admin' );
        }
        
        /**
		 * Register Scripts Admin
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_admin_styles(){
            wp_register_style( 'woo-bw-image-order-css-admin', WC_BW_ORDER_CSS . 'admin-stylesheet.css', false, WC_BW_ORDER_VERSION );
            wp_enqueue_style( 'woo-bw-image-order-css-admin' );
        }
        
        /**
		 * Register Scripts Admin
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_view_styles(){
            wp_register_style( 'woo-bw-image-order-css-view', WC_BW_ORDER_CSS . 'view-stylesheet.css', false, WC_BW_ORDER_VERSION );
            wp_register_script( 'woo-bw-image-order-js-view', WC_BW_ORDER_JS . 'view-script.js' , array( 'jquery' ), WC_BW_ORDER_VERSION, true );
            wp_enqueue_style( 'woo-bw-image-order-css-view' );
            if( is_product() ){
                wp_enqueue_script( 'woo-bw-image-order-js-view' );
            }
        }
    }
    new WooBWImageOrderEnqueue();
}
?>