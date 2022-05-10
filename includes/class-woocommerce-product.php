<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_WooCommerce_Product {

    public function __construct() {

        // Ajax to get product price
        add_action( 'wp_ajax_nopriv_gl_get_product_price_from_order_item_id', array($this, 'gl_get_product_price_from_order_item_id') );
        add_action( 'wp_ajax_gl_get_product_price_from_order_item_id', array($this, 'gl_get_product_price_from_order_item_id') );

        // Add and save Quote Description field for variable products
        add_action( 'woocommerce_product_options_general_product_data', array($this, 'gl_add_quote_description_to_simple_product') ); 
        add_action( 'woocommerce_process_product_meta', array($this, 'gl_save_quote_description_simple_product') );

        // Add and save Quote Description field for variable products
        add_action( 'woocommerce_product_after_variable_attributes', array($this, 'gl_add_quote_description_to_variations'), 20, 3 );
        add_action( 'woocommerce_save_product_variation', array($this, 'gl_save_quote_description_variations'), 10, 2 );

    }


    /**
     * Function for the Ajax call to return the product price
     */
    public function gl_get_product_price_from_order_item_id() {
        $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );

        $order_item_id = sanitize_text_field($_POST['order_item_id']);

        try {
            $product_id = wc_get_order_item_meta($order_item_id, '_product_id');
            $variation_id = wc_get_order_item_meta($order_item_id, '_variation_id');

            if($variation_id != 0) {
                $product = new \WC_Product_Variation($variation_id);
            } else {
                $product = new \WC_Product( $product_id);
            }
            $price = wc_price($product->get_price());

            $return_arr = array(
                'status_msg' => 'Success',
                'price' => $price,
                'order_item_id_exists' => true
            );
        } catch (Exception $e) {
            $return_arr = array(
                'status_msg' => 'Caught exception: ',  $e->getMessage(),
                'order_item_id_exists' => false
            );
        }
        
        wp_send_json($return_arr);

    }


    /**
     * Add the Quote Description field for simple products under the general tab
     */
    public function gl_add_quote_description_to_simple_product() {

        woocommerce_wp_textarea_input(
            array(
                'id'            => "quote_description",
                'name'          => "quote_description",
                'value'         => get_post_meta( get_the_ID(), 'quote_description', true ),
                'label'         => __( 'Quote Description', 'woocommerce' ),
                'desc_tip'      => true,
                'description'   => __( 'Enter a description that will show on the quote for this variation.', 'woocommerce' ),
                'wrapper_class' => 'form-row form-row-full',
            )
        );
    }

    /**
     * Save the Quote Description field for simple products
     */
    public function gl_save_quote_description_simple_product($post_id) {
        $quote_description = $_POST['quote_description'];
        if ( isset( $quote_description ) ) update_post_meta( $post_id, 'quote_description', esc_attr( $quote_description ) );       
    }
    /**
     * Add the Quote Description field for Variations under Product Data
     */
    public function gl_add_quote_description_to_variations( $loop, $variation_data, $variation ) {
        woocommerce_wp_textarea_input(
            array(
                'id'            => "quote_description{$loop}",
                'name'          => "quote_description[{$loop}]",
                'value'         => get_post_meta( $variation->ID, 'quote_description', true ),
                'label'         => __( 'Quote Description', 'woocommerce' ),
                'desc_tip'      => true,
                'description'   => __( 'Enter a description that will show on the quote for this variation.', 'woocommerce' ),
                'wrapper_class' => 'form-row form-row-full',
            )
        );
    }
    
    /** 
     * Save the variation Quote Description field
     */   
    public function gl_save_quote_description_variations( $variation_id, $i ) {
        $quote_description = $_POST['quote_description'][$i];
        if ( isset( $quote_description ) ) update_post_meta( $variation_id, 'quote_description', esc_attr( $quote_description ) );
    }
    
 
} // end class

$gl_woocommerce_product = new GL_WooCommerce_Product();