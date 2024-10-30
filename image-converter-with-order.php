<?php
/**
 * Plugin Name: Image Converter With Order
 * Plugin URI: https://wordpress.org/plugins/image-converter-with-order/
 * Description: Add custom image with woocommerce order, after upload your image, image will be convert in black and white and also in original format, and both image will be send in order and with email.
 * Version: 1.0
 * Tags: woocommerce, woocommerce order, order custom image, images, black and white image, black and white, black & white
 * Author: Mubeen Khan
 * Author URI: http://mubeenkhan.com/
 * Author Email: wpmubeenkhan@gmail.com
 * Requires at least: WP 4.5
 * Tested up to: WP 5.5.1
 * Text Domain: image-converter-order
 * Domain Path: /language
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Here we are adding plugin final class
 * it will work around the plugin.
 */
if ( ! class_exists( 'WooBWImageOrder' ) ){
    final class WooBWImageOrder{
        
        /**
         * 
         * Woo B&W Image Order plugin version
         * Woo B&W Image Order text domain
         * 
         */
        public $version   = '1.0';
		public $slug      = 'image-converter-order';
        
        /**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __clone(){
            _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version );
        }
        
        /**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __wakeup(){
            _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version );
        }
        
        /**
		 * Define
		 * @since 1.0
		 * @version 1.0
		 */
		public function define( $name, $value, $definable = true ){
			if ( ! defined( $name ) )
				define( $name, $value );
			elseif ( ! $definable && defined( $name ) )
				_doing_it_wrong( 'WooBWImageOrder->define()', 'Could not define: ' . $name . ' as it is already defined somewhere else!', WC_API_VERSION );
		}
        
        /**
		 * Require File
		 * @since 1.0
		 * @version 1.0
		 */
		public function file( $required_file ){
			if ( file_exists( $required_file ) )
				require_once $required_file;
			else
				_doing_it_wrong( 'WooBWImageOrder->file()', 'Requested file ' . $required_file . ' not found.', WC_API_VERSION );
		}
        
        /**
		 * Construct
		 * @since 1.0
		 * @version 1.0
		 */
		public function __construct(){
            $this->define_constants();
            $this->wordpress();
            $this->includes();
		}
        
        /**
		 * Define Constants
		 * First, we start with defining all requires constants if they are not defined already.
		 * @since 1.0
		 * @version 1.0
		 */
		private function define_constants(){
			/**
             * Here we define all plugin dir paths
             */
            $this->define( 'WC_BW_ORDER_VERSION', $this->version );
            $this->define( 'WC_BW_ORDER_TEXT_DOMAIN', $this->slug );
			$this->define( 'WC_BW_ORDER_THIS', __FILE__, false );
            $this->define( 'WC_BW_ORDER_BASE', plugin_basename( WC_BW_ORDER_THIS ) );
			$this->define( 'WC_BW_ORDER_ROOT_DIR', plugin_dir_path( WC_BW_ORDER_THIS ), false );
			$this->define( 'WC_BW_ORDER_INCLUDES', WC_BW_ORDER_ROOT_DIR . 'includes/', false );
            /**
             * Here we define all plugin urls
             */
            $this->define( 'WC_BW_ORDER_URL', plugin_dir_url(__FILE__), false );
            $this->define( 'WC_BW_ORDER_ASSETS', WC_BW_ORDER_URL . 'assets/', false );
            $this->define( 'WC_BW_ORDER_JS', WC_BW_ORDER_ASSETS . 'js/', false );
            $this->define( 'WC_BW_ORDER_CSS', WC_BW_ORDER_ASSETS . 'css/', false );
            $this->define( 'WC_BW_ORDER_IMG', WC_BW_ORDER_ASSETS . 'images/', false );
		}
        
        /**
		 * Include Plugin Files
		 * @since 1.0
		 * @version 1.0
		 */
		public function includes(){
            $this->file( WC_BW_ORDER_INCLUDES . 'enqueue.php' );
            $this->file( WC_BW_ORDER_INCLUDES . 'image-uploads.php' );
		}
        
        /**
		 * WordPress
		 * Next we hook into WordPress
		 * @since 1.0
		 * @version 1.0
		 */
		public function wordpress() {
            add_action( 'admin_init', array( $this , 'woo_bw_image_order_check_woocommerce_plugin' ) );
            add_action( 'in_plugin_update_message-woocommerce-api/woo-bw-image-order.php', array( $this, 'woo_bw_image_order_update_warning' ) );
			add_action( 'init', array( $this, 'woo_bw_image_order_load_textdomain' ), 5 );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__) , array( $this, 'woo_bw_image_order_plugin_links' ), 10, 4 );
            add_filter( 'plugin_row_meta', array( $this, 'woo_bw_image_order_description_links' ), 10, 2 );
        }
        
        /**
		 * Plugin Check Woocommerce Active or Install
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_check_woocommerce_plugin() {
	        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                deactivate_plugins( WC_BW_ORDER_BASE );
                if ( isset( $_GET['activate'] ) )
                    unset( $_GET['activate'] );
            
                wp_die( '<b>'.__( 'Woo Image With Order Plugin ', WC_BW_ORDER_TEXT_DOMAIN ).'</b> '.__('requires you to install & activate', WC_BW_ORDER_TEXT_DOMAIN ).'<b> '.__( 'WooCommerce Plugin', WC_BW_ORDER_TEXT_DOMAIN ).'</b> '.__( 'before activating it!', WC_BW_ORDER_TEXT_DOMAIN ).'<br><br><a href="javascript:history.back()"><< '.__( 'Go Back To Plugins Page', WC_BW_ORDER_TEXT_DOMAIN ).'</a>' ); 
	        }
        }
        
        /**
		 * Plugin Update Warning
		 * @since 1.0
		 * @version 1.0
		 */
		public function woo_bw_image_order_update_warning(){
			echo '<div style="color:#cc0000;">' . __( 'Make sure to backup your database and files before updating, in case anything goes wrong!', WC_BW_ORDER_TEXT_DOMAIN ) . '</div>';
		}
        
        /**
		 * Load Plugin Textdomain
		 * @since 1.0
		 * @version 1.0
		 */
		public function woo_bw_image_order_load_textdomain(){
			$locale = apply_filters( 'plugin_locale', get_locale(), WC_BW_ORDER_TEXT_DOMAIN );
			load_textdomain( WC_BW_ORDER_TEXT_DOMAIN , WP_LANG_DIR . '/woo-bw-image-order/wc-' . $locale . '.mo' );
			load_plugin_textdomain( WC_BW_ORDER_TEXT_DOMAIN , false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
		}
        
        /**
		 * Plugin Links
		 * @since 1.0
		 * @version 1.0
		 */
		public function woo_bw_image_order_plugin_links( $actions, $plugin_file, $plugin_data, $context ){
			$actions['_settings'] = '<a href="' . admin_url( 'plugins.php#' ) . '" >' . __( 'Settings', WC_BW_ORDER_TEXT_DOMAIN ) . '</a>';
			ksort( $actions );
			return $actions;
		}
        
        /**
		 * Plugin Description Links
		 * @since 1.0
		 * @version 1.0
		 */
		public function woo_bw_image_order_description_links( $links, $file ){
			if ( $file != WC_BW_ORDER_BASE ) return $links;
			// Usefull links
			$links[] = '<a href="#" target="_blank">Documentation</a>';
            $links[] = '<a href="#" target="_blank">About</a>';
			$links[] = '<a href="#" target="_blank">Premium support</a>';
            return $links;
		}
    }
    new WooBWImageOrder();
}
?>