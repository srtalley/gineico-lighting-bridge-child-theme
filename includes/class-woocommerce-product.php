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
        add_action( 'woocommerce_product_options_advanced', array($this, 'gl_add_quote_description_to_advanced_tab') ); 
        add_action( 'woocommerce_process_product_meta', array($this, 'gl_save_quote_description_main_product') );

        // Add and save Quote Description field for variable products
        add_action( 'woocommerce_product_after_variable_attributes', array($this, 'gl_add_quote_description_to_variations'), 20, 3 );
        add_action( 'woocommerce_save_product_variation', array($this, 'gl_save_quote_description_variations'), 10, 2 );

        add_action( 'woocommerce_before_save_order_items', array($this, 'gl_save_custom_order_items'), 10, 2 );
        add_filter('woocommerce_hidden_order_itemmeta', array($this, 'gl_hide_quote_description_custom_meta'), 10, 1);

        // Filter for Zoho to create a product id
        add_filter( 'get_zoho_product_id', array($this, 'get_zoho_product_id'), 10, 1 );

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
    public function gl_add_quote_description_to_advanced_tab($product) {

        $product = wc_get_product(get_the_ID());

        woocommerce_wp_textarea_input(
            array(
                'id'            => "quote_description_adv",
                'name'          => "quote_description_adv",
                'value'         => get_post_meta( get_the_ID(), 'quote_description', true ),
                'label'         => __( 'Quote Description', 'woocommerce' ),
                'wrapper_class' => 'form-row form-row-full',
                'class'         => 'long quote-description',
            )
        );
        echo '<style>.quote-description {min-height: 200px;}</style>';
        echo '<p class="form-field quote_description_field_label form-row form-row-full">';
        if($product->is_type( 'variable' )) {
            echo '<label></label>This field will show on the PDF, but if you write a different description in the options for a variation, then this field will be ignored.</p>';
        } else {
            echo '<label></label>This field will show on the PDF.</p>';
        }

    }

    /**
     * Save the Quote Description field for simple products
     */
    public function gl_save_quote_description_main_product($post_id) {
        $quote_description = $_POST['quote_description_adv'];
        if ( isset( $quote_description ) ) update_post_meta( $post_id, 'quote_description', sanitize_textarea_field( $quote_description ) );       
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
                'class'         => 'long quote-description',
            )
        );
    }
    
    /** 
     * Save the variation Quote Description field
     */   
    public function gl_save_quote_description_variations( $variation_id, $i ) {
        $quote_description = $_POST['quote_description'][$i];
        if ( isset( $quote_description ) ) update_post_meta( $variation_id, 'quote_description', sanitize_textarea_field( $quote_description ) );
    }
    
    /**
     * Handle the custom quote description field when saving line items
     * in a manual order
     */
    public function gl_save_custom_order_items($order_id, $items) {
        $gl_quote_description = isset($items['gl-quote-description']) ? $items['gl-quote-description'] : '';
        $gl_quote_description_is_custom = isset($items['gl-quote-description-is-custom']) ? $items['gl-quote-description-is-custom'] : '';
        $gl_quote_description_update_product_variation = isset($items['gl-quote-description-update-product-variation']) ? $items['gl-quote-description-update-product-variation'] : '';
        $gl_quote_description_update_product_parent = isset($items['gl-quote-description-update-product-parent']) ? $items['gl-quote-description-update-product-parent'] : '';

        if(is_array($gl_quote_description_is_custom)) {
            foreach ($gl_quote_description_is_custom as $item_id => $custom) {
                if($custom == 'yes') {
                    $custom_quote_description = $gl_quote_description[$item_id];
                    wc_update_order_item_meta($item_id, '_gl_quote_description_custom', sanitize_textarea_field($custom_quote_description), false);
                    if(is_array($gl_quote_description_update_product_variation) && isset($gl_quote_description_update_product_variation[$item_id])) {
                        // get the product ID
                        $product_id = wc_get_order_item_meta($item_id, '_product_id');
                        $variation_id = wc_get_order_item_meta($item_id, '_variation_id');
                        update_post_meta( $variation_id, 'quote_description', sanitize_textarea_field( $custom_quote_description ) );

                    }
                    if(is_array($gl_quote_description_update_product_parent) && isset($gl_quote_description_update_product_parent[$item_id])) {
                        // get the product ID
                        $product_id = wc_get_order_item_meta($item_id, '_product_id');
                        update_post_meta( $product_id, 'quote_description', sanitize_textarea_field( $custom_quote_description ) );

                    }
                }
            }
        }     

    }
    /**
     * Hide the custom meta field if added
     */
    function gl_hide_quote_description_custom_meta($arr) {
        $arr[] = '_gl_quote_description_custom';
        return $arr;
    }
    
    /**
     * Filter to return a custom product ID for Zoho
     */
    public function get_zoho_product_id($product_id) {

        $product = wc_get_product($product_id);

        // detect if product is variation
        if($product->is_type('variation')) {
            $parent_id = $product->get_parent_id();
            $id =  $parent_id . '-' . $product->get_id();
        } else {
            $id = $product->get_id();
        }
        return 'GL-' . $id;
    }
 
} // end class

$gl_woocommerce_product = new GL_WooCommerce_Product();