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

if ( ! class_exists( 'WooBWImageOrderWooCommerce' ) ){
    
    class WooBWImageOrderWooCommerce{
        
        /**
		 * Construct
		 * @since 1.0
		 * @version 1.0
		 */
        public function __construct(){
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woo_bw_image_order_add_file_input' ) );
            add_action( 'woocommerce_add_to_cart_validation', array( $this, 'woo_bw_image_order_upload_image' ), 10, 3 );
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woo_bw_image_order_cart_item_data' ), 20, 2 );
            add_filter( 'woocommerce_get_item_data', array( $this, 'woo_bw_image_order_get_item_data' ), 10, 2 );
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'woo_bw_image_order_create_order_line_item' ), 20, 4 );
            add_filter( 'woocommerce_order_item_name', array( $this, 'woo_bw_image_order_item_email' ), 10, 2 );
            add_action( 'woocommerce_product_options_general_product_data', array( $this, 'woo_bw_image_order_custom_fields') );
            add_action( 'woocommerce_process_product_meta', array( $this, 'woo_bw_image_order_custom_fields_save' ) );
        }
        
        /**
		 * Add File Input to Products page.
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_add_file_input(){
            global $post;
            $check_upload = get_post_meta($post->ID, '_woo_bw_image_enable', true);
            if( $check_upload == 'yes' ){
            echo '<div class="woo-bw-image-order">
                    <input type="file" name="woo_bw_image_order_upload_btn" id="woo_bw_image_order_upload_btn" required="required" />
                    <img id="woo_bw_image_order_upload_preview" src="#" alt="Your Logo" style="height: 200px; width: 200px; display: none" />
                    <input type="hidden" name="woo-bw-image-order-id" value="'. get_the_ID() .'" />
                  </div>';
            }
        }
        
        /**
		 * Upload file form Products page.
		 * @since 1.0
		 * @version 1.0
		 */
        function woo_bw_image_order_upload_image(){
            
            $target_dir = WC_BW_ORDER_ROOT_DIR . "uploads/";
            $target_file = $target_dir . basename($_FILES["woo_bw_image_order_upload_btn"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            if(isset($_POST["add-to-cart"])) {
                if( $_FILES["woo_bw_image_order_upload_btn"]["tmp_name"] != '' ){
                    $bw_product_id = $_POST['add-to-cart'];
                    $check = getimagesize($_FILES["woo_bw_image_order_upload_btn"]["tmp_name"]);
                    // Check if file is image.
                    if($check !== false){
                        $uploadOk = 1;
                    }else{
                        wc_add_notice( sprintf( __( 'Sorry, it is not image file, you need to upload <b>jpg, jpeg or png</b> file only.' ) ), 'error' );
                        $uploadOk = 0;
                        return false;
                    }
                
                    // Check if file already exists
                    if (file_exists($target_file)) {
                        wc_add_notice( sprintf( __( 'Sorry, file already exists.' ) ), 'error' );
                        $uploadOk = 0;
                        return false;
                    }
                
                    // Check file size
                    if ($_FILES["woo_bw_image_order_upload_btn"]["size"] > 50000000 ){
                        wc_add_notice( sprintf( __( 'Sorry, your file is too large.' ) ), 'error' );
                        $uploadOk = 0;
                        return false;
                    }
                
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
                        wc_add_notice( sprintf( __( 'Sorry, only JPG, JPEG & PNG files are allowed.' ) ), 'notice' );
                        $uploadOk = 0;
                        return false;
                    }
                    
                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0) {
                        wc_add_notice( sprintf( __( 'Sorry, your file was not uploaded.' ) ), 'notice' );
                        return false;
                    }else{
                        if (move_uploaded_file($_FILES["woo_bw_image_order_upload_btn"]["tmp_name"], $target_file)){
                            move_uploaded_file($_FILES["woo_bw_image_order_upload_btn"]["tmp_name"], $this->woo_bw_image_order_converter( $target_file, basename( $_FILES["woo_bw_image_order_upload_btn"]["name"]), $imageFileType ) );
                            wc_add_notice( sprintf( __( 'The file %s has been uploaded.' ) , basename( $_FILES["woo_bw_image_order_upload_btn"]["name"] ) ), 'notice' );
                            session_start();
                            $_SESSION['woo_bw_image_order_image_name'] = basename( $_FILES["woo_bw_image_order_upload_btn"]["name"] );
                            $_SESSION['woo_bw_image_order_image_name_bw'] = 'bw-' . basename( $_FILES["woo_bw_image_order_upload_btn"]["name"] );
                            return true;
                        }else{
                            wc_add_notice( sprintf( __( 'Sorry, there was an error uploading your file.' ) ), 'error' );
                            return false;
                        }
                    }
                }else{
                    return true;
                }
            }
        }
        
        /**
		 * Convert Image in Black and White
		 * @since 1.0
		 * @version 1.0
		 */
        private function woo_bw_image_order_converter( $image_path, $filename, $filetype ) {
            
            if( $filetype == 'jpg' || $filetype == 'jpeg' ){
                $im = ImageCreateFromJpeg($image_path);
            }
            
            if( $filetype == 'png' ){
                $im = imagecreatefrompng($image_path);
            }
            
            if($im && imagefilter($im, IMG_FILTER_GRAYSCALE)){
                imagepng($im, WC_BW_ORDER_ROOT_DIR . "uploads/bw-$filename" );
            }

            imagedestroy($im);
        }
        
        /**
		 * Add image url in cart data.
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_cart_item_data( $cart_item, $product_id ){
            $woo_bw_main_image = $_SESSION['woo_bw_image_order_image_name'];
            $woo_bw_main_image_bw = $_SESSION['woo_bw_image_order_image_name_bw'];
            $cart_item['bw_image_order'] =  WC_BW_ORDER_URL . "uploads/$woo_bw_main_image";
            $cart_item['bw_image_order_bw'] =  WC_BW_ORDER_URL . "uploads/$woo_bw_main_image_bw";
            session_destroy();
            return $cart_item;
        }
        
        /**
		 * Get image url in cart data.
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_get_item_data( $item_data, $cart_item_data ) {
            if( $cart_item_data['bw_image_order'] != WC_BW_ORDER_URL . 'uploads/' ){
            $item_data[] = array(
                'key' => __( 'Your Image Preview', WC_BW_ORDER_TEXT_DOMAIN ),
                'value' => '<img src="' . wc_clean( $cart_item_data['bw_image_order'] ) . '" class="woo_bw_main_image" /><img src="' . wc_clean( $cart_item_data['bw_image_order_bw'] ) . '" class="woo_bw_main_image_bw" />'
            );
            return $item_data;
            }
        }
        
        /**
		 * Get image url in checkout and for
         * order page.
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_create_order_line_item( $item, $cart_item_key, $values, $order ){
            if( $values['bw_image_order'] != WC_BW_ORDER_URL . 'uploads/' ){
                $item->add_meta_data( __( 'Your Image Preview', WC_BW_ORDER_TEXT_DOMAIN ), '<a href="' . wc_clean( $values['bw_image_order'] ) . '" download><img src="' . wc_clean( $values['bw_image_order'] ) . '" class="woo_bw_main_image" /></a><a href="' . wc_clean( $values['bw_image_order_bw'] ) . '" download><img src="' . wc_clean( $values['bw_image_order_bw'] ) . '" class="woo_bw_main_image_bw" /></a>', true );
            }
        }
        
        /**
		 * Send Email when order is done
         * order page.
		 * @since 1.0
		 * @version 1.0
		 */
        public function woo_bw_image_order_item_email( $product_email, $item ) {
            if( $item['bw_image_order'] != WC_BW_ORDER_URL . 'uploads/' ){
                $product_email .= sprintf( '<ul><li>%s: %s</li></ul>', __( 'Your Image Preview', WC_BW_ORDER_TEXT_DOMAIN ), '<img src="' . wc_clean( $item['bw_image_order'] ) . '" height="150" width="150" /><img src="' . wc_clean( $item['bw_image_order_bw'] ) . '" height="150" width="150" />' );
                return $product_email;
            }
        }
        
        public function woo_bw_image_order_custom_fields(){
            global $woocommerce, $post;
            $check_upload = get_post_meta($post->ID, '_woo_bw_image_enable', true);
            if( $check_upload == 'yes' ){
                $wp_attribute_yes = 'checked="checked"';
            }else{
                $wp_attribute_no  = 'checked="checked"';
            }
            ?>
            <div class="product_custom_field">
                <p class="form-field _woo_bw_image_enable_field ">
                    <label for="_woo_bw_image_enable"><?php _e( 'Enable File Upload', WC_BW_ORDER_TEXT_DOMAIN ); ?></label>
                    <?php _e( 'Yes', WC_BW_ORDER_TEXT_DOMAIN ); ?> <input type="radio" class="radio" name="_woo_bw_image_enable" id="_woo_bw_image_enable_one" value="yes" <?php echo $wp_attribute_yes; ?> />
                    <?php _e( 'No', WC_BW_ORDER_TEXT_DOMAIN ); ?> <input type="radio" class="radio" name="_woo_bw_image_enable" id="_woo_bw_image_enable_two" value="no" <?php echo $wp_attribute_no; ?>  /><br />
                    <span class="description"><?php _e( 'Please enable this option if you want to show file upload on product page.', WC_BW_ORDER_TEXT_DOMAIN ); ?></span>
                </p>
            </div>
            <?php
        }
        
        public function woo_bw_image_order_custom_fields_save($post_id){
            $woo_bw_image_enable = $_POST['_woo_bw_image_enable'];
            if (!empty($woo_bw_image_enable)){
                update_post_meta($post_id, '_woo_bw_image_enable', esc_attr($woo_bw_image_enable));
            }
        }
    }
    new WooBWImageOrderWooCommerce();
}
?>