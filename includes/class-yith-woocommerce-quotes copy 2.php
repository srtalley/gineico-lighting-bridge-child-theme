<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_YITH_WooCommerce_Quotes {

  
    public function __construct() {


        
        // PDF file url version string
        add_filter('ywraq_pdf_file_url', array($this, 'gl_ywraq_pdf_url_string'), 10, 1);

        add_filter( 'ywraq_pdf_file_name', array($this, 'gl_ywraq_pdf_file_name'), 10, 2 );

        // Fix the issue with color selectors because of the bridge theme
        add_action('admin_head', array($this, 'gl_yith_admin_css'));

        // Add a login form to the request-quote-default-form template
        add_action('gl_ywraq_after_default_form', array($this, 'gl_add_login_form'), 10, 1);

        // add a redirect that will go back to the quote page if they 
        // log in from there
        add_filter( 'woocommerce_login_redirect', array($this, 'gl_login_redirect'), 10, 1);

        // make sure the return to products button on the request quote
        // page actually says return to products because it does not
        // after you submit a quote.
        add_filter('yith_ywraq_return_to_shop_after_sent_the_request_label', array($this, 'gl_yith_ywraq_return_to_shop_after_sent_the_request_label'), 10, 1);
    
        // get the shop URL after the quote has been submitted
        add_filter('yith_ywraq_return_to_shop_after_sent_the_request_url', array($this, 'gl_yith_ywraq_return_to_shop_after_sent_the_request_url'), 10, 1);

        // change the my account quote title
        add_filter('ywraq_my_account_my_quotes_title', array($this, 'gl_change_ywraq_my_account_my_quotes_title'), 10, 1);

        // remove all quote statuses except for new from showing for the my account area
        add_filter('ywraq_my_account_my_quotes_query', array($this, 'gl_change_ywraq_my_account_my_quotes_query'), 10, 1);

        // update the phone and company fields when quote submitted
        add_action('ywraq_checkout_update_customer', array($this, 'gl_ywraq_checkout_update_customer'), 10, 2);

        // Add a meta key to the request a quote items
        add_action( 'ywraq_from_cart_to_order_item', array($this, 'gl_ywraq_from_cart_to_order_item'), 10, 4 );

        // Change the meta key display label
        add_filter( 'woocommerce_order_item_display_meta_key', array($this, 'gl_filter_wc_order_item_display_meta_key'), 20, 3 );

        // Add the missing columns
        add_action( 'woocommerce_after_order_item_object_save', array($this, 'gl_woocommerce_after_order_item_object_save'), 10, 2 );
        
        add_action( 'current_screen', array($this,'gl_woocommerce_order_admin'), 10, 1 );

        add_action( 'add_meta_boxes', array($this, 'gl_shop_order_add_meta_boxes'), 40 );

    }

    /** 
     * Add a random string to the end of the URL to break the cache so that the 
     * proper PDF downloads.
     */
    public function gl_ywraq_pdf_url_string($url) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz'; 
        $random_string = ''; 
    
        for ($i = 0; $i < 6; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $random_string .= $characters[$index]; 
        } 
        return $url . '?ver=' . $random_string;
    }

    /**
     * Change the file name of the quote PDF
     */
    public function gl_ywraq_pdf_file_name($pdf_file_name, $order_id) {
        $order = wc_get_order($order_id);
        $customer_lastname   = yit_get_prop( $order, '_billing_last_name', true );

        if($customer_lastname == null || $customer_lastname == '') {
            $user_name = yit_get_prop( $order, 'ywraq_customer_name', true );
            $user_name_parts = explode(" ", $user_name);
            $customer_lastname = array_pop($user_name_parts);
        }

        $order_date = yit_get_prop($order, 'date_created', true);
        $order_date = substr($order_date, 0, 10);
        $order_date = str_replace('-', '_', $order_date);
        $pdf_file_name = 'Quote-' . $order_id . '-' . $order_date;
        $YITH_Request_Quote = YITH_Request_Quote_Premium();
        $path = $YITH_Request_Quote->create_storing_folder($order_id);
        $file = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '.pdf';
        if(file_exists($file)) {
            $new_pdf_file_name = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '-' . $customer_lastname . '.pdf';
            rename($file, $new_pdf_file_name);
            return $pdf_file_name . '-' . $customer_lastname . '.pdf';
        }

        return $pdf_file_name . '-' . $customer_lastname . '.pdf';
    }
    

    /**
     * Fix the issue with color selectors in the admin area because of the bridge theme
     */
    public function gl_yith_admin_css() {
        echo '<style type="text/css">.yith-plugin-ui .yith-single-colorpicker { position: static; background: none; height: auto; overflow: auto; }</style>';
    }

    /**
     * Add a login form to the request-quote-default-form template
     */
    public function gl_add_login_form() {
        // these two lines enqueue the login nocaptcha scripts
        wp_enqueue_script('login_nocaptcha_google_api');
        wp_enqueue_style('login_nocaptcha_css');
        woocommerce_login_form(
            array(
                'message'  => esc_html__( 'Please log in with your account details below.', 'woocommerce' ),
                'redirect' => wc_get_checkout_url(),
                'hidden'   => true,
            )
        );
    }   
    /**
     * add a redirect that will go back to the quote page if they
     * log in from there
     */
    public function gl_login_redirect (){
        $referer = wp_get_referer();
        if($referer == '') {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        if($referer == site_url( '/request-quote/') ){
            wp_redirect( $referer );
        }
    }

    /**
     * make sure the return to products button on the request quote
     * page actually says return to products because it does not
     * after you submit a quote.
     */

    public function gl_yith_ywraq_return_to_shop_after_sent_the_request_label ($label) {
        return  'Add Products to Quote';
    }
    /**
     * Return the shop URL after the quote has been submitted
     */
    public function gl_yith_ywraq_return_to_shop_after_sent_the_request_url($url) {
        return get_permalink( wc_get_page_id( 'shop' ) );
    }
    /**
     * Change the my account quotes title
     */
    public function gl_change_ywraq_my_account_my_quotes_title() {
        return 'Recent Quotes';
    }
    /**
     * Remove all quote statuses except for new from showing for the my account area
     */
    public function gl_change_ywraq_my_account_my_quotes_query($options) {
        $unset_order_statuses = array('wc-ywraq-pending', 'wc-ywraq-expired', 'wc-ywraq-rejected', 'wc-ywraq-accepted');
        foreach ($options['status'] as $key => $status) {
            if(in_array($status, $unset_order_statuses)) {
                unset($options['status'][$key]);
            }
        }
        return $options;
    }
    /**
     * Update the additional company and phone fields when a quote 
     * is submitted.
     */
    public function gl_ywraq_checkout_update_customer($customer, $filled_form_fields){
        foreach ( $filled_form_fields as $key => $value ) {
            if($key == 'company_name') {
                $customer->update_meta_data('company', $value['value']);
            }
            if($key == 'phone_number') {
                $customer->update_meta_data('phone_number', $value['value']);
            }
        }
    }


    /** 
     * Add custom keys to each submitted ywraq quote order
     */
    public function gl_ywraq_from_cart_to_order_item( $values, $cart_item_key, $item_id, $order ) {
        $item = $order->get_item($item_id);
        // The WC_Product object
        $product = $item->get_product(); 
        $sku = $product->get_sku();
        if($sku == '') {
            $sku = ' ';
        }

        $key = '_gl_quote_type'; 
        $value = ' '; 
        wc_update_order_item_meta($item_id, $key, $value);

        $key = '_gl_quote_part_number'; 
        $value = $sku; 
        wc_update_order_item_meta($item_id, $key, $value);
    }

    /**
     * Change the display of the meta key labels
     */
    public function gl_filter_wc_order_item_display_meta_key( $display_key, $meta, $item ) {
        // Change displayed label for specific order item meta key
        if( is_admin() && $item->get_type() === 'line_item' && $meta->key === '_gl_quote_type' ) {
            $display_key = __("Quote Type", "woocommerce" );
        }
        if( is_admin() && $item->get_type() === 'line_item' && $meta->key === '_gl_quote_part_number' ) {
            $display_key = __("Part Number", "woocommerce" );
        }
        return $display_key;
    }
 
    /**
     * Checks and adds meta key items on order or item save
     */
    public function gl_woocommerce_after_order_item_object_save($item, $data_store) {

        if(is_a($item, 'WC_Order_Item_Product')) {
            $item_id = $item->get_id();
            // The WC_Product object
            $product = $item->get_product(); 
            $sku = $product->get_sku();
            if($sku == '') {
                $sku = ' ';
            }
    
            $this->update_wc_order_item_meta_key($item_id, '_gl_quote_type');
            $this->update_wc_order_item_meta_key($item_id, '_gl_quote_part_number', $sku);
        }

    }

    /**
     * Custom function to check for and add order item meta if 
     * it does not exist.
     */
    private function update_wc_order_item_meta_key($item_id, $meta_key, $value = ' ') {
        $meta_key_search = wc_get_order_item_meta( $item_id, $meta_key, true );
        if($meta_key_search == '') {
            wc_update_order_item_meta($item_id, $meta_key, $value);
        }
    }

    /**
     * Javascript to prevent editing the custom meta field
     * key names or labels
     */
    public function gl_woocommerce_order_admin($current_screen) {
        if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
            if($current_screen->id == 'shop_order') {

                // add the CSS & JS
                add_action('admin_head', array($this, 'gl_shop_order_quote_css_js'), 1000);

                // move the metaboxes
                add_action( 'add_meta_boxes', array($this, 'gl_change_order_metaboxes'), 99 );

            }
        } // end if 
    }

    public function gl_shop_order_quote_css_js() {
        ?>

        <script type="text/javascript">
            jQuery(function($) {

                $('document').ready(function() {

                    setupCustomMeta();

                    // handle an update where the items are reloaded
                    // via ajax and run it a few times in case there's 
                    // a delay in them being added
                    $(document).on('items_saved', function() {
                        setupCustomMetaSupport();
                    });
                    $( '#woocommerce-order-items' ).on( 'click', '.cancel-action', function() {
                        setupCustomMetaSupport();
                    } );
                    
                    setupCustomShipping();
                });

                function setupCustomMetaSupport() {
                    setTimeout(() => {
                        setupCustomMeta();
                    }, 500);
                    setTimeout(() => {
                        setupCustomMeta();
                    }, 2000);
                    setTimeout(() => {
                        setupCustomMeta();
                        // shipping_observer();
                    }, 5000);
                    setTimeout(() => {
                        setupCustomMeta();
                    }, 20000);
                }
                function setupCustomMeta() {

                    $('input[value="_gl_quote_type"]').addClass('gl-hide-label');
                    $('input[value="_gl_quote_type"]').parent().parent().addClass('gl-quote-type');

                    $('input[value="_gl_quote_part_number"]').addClass('gl-hide-label');
                    $('input[value="_gl_quote_part_number"]').parent().parent().addClass('gl-quote-part-number');
                }

                function setupCustomShipping() {

                    // hide the other field
                    $('.gl-other-shipping-method').css('display', 'none').removeClass('hide');

                    // hide any shown errors if something is typed in the other field
                    $("#gl_other_shipping_name").on('change', function() {
                        if($(this).val() != '') {
                            $('#gl_shipping_error').text('');
                        }
                    });
                    $('#gl_shipping_option').on('change', function(e) {
                        if($("#gl_shipping_option option:selected").val() == 'other') {
                            $('.gl-other-shipping-method').slideDown();
                            $('#gl_other_shipping_name').focus();
                        } else {
                            $('.gl-other-shipping-method').slideUp();
                        }
                    });
                    $('#gl_add_shipping').on('click', function(e) {
                        e.preventDefault();

                        var method_text = $("#gl_shipping_option option:selected").text();
                        var method_option = $("#gl_shipping_option option:selected").val();

                        if(method_option == 'other') {
                            var method_text = $("#gl_other_shipping_name").val();
                        }

                        var amount = $("#gl_shipping_cost").val();
                        if(method_text != '') {
                            shipping_observer(method_text, amount);
                            $('.button.add-order-shipping').click();    
                        } else if(method_text == '') {
                                $('#gl_shipping_error').text('Please enter a custom shipping name.');
                        } 
                    });
                }

                function shipping_observer(method, amount) {

                    // Create an observer instance
                    var observer = new MutationObserver(function( mutations ) {
                        mutations.forEach(function( mutation ) {		
                            var newNodes = mutation.addedNodes; 
                            // If there are new nodes added
                            if( newNodes !== null ) { 
                                var $nodes = $( newNodes ); 
                                $nodes.each(function() {
                                    var $node = $( this );
                                    // check if new node added with class 'shipping'
                                    if( $node.hasClass("shipping")){			
                                        // get the id
                                        order_item_id = $node.data('order_item_id');

                                        $('input[name="shipping_method_title[' + order_item_id + ']"]').val(method);
                                        $('input[name="shipping_cost[' + order_item_id + ']"]').val(amount);
                                        $('.button.save-action').click();
                                    }
                                });
                            }
                        });    
                    });
                    // Configuration of the observer:
                    var config = { 
                        childList: true,
                        attributes: true,
                        subtree: true,
                        characterData: true
                    }; 
                    var targetNode = $('#order_shipping_line_items')[0];
                    observer.observe(targetNode, config);  
                }
            });
        </script>

        <style>
            .gl-hide-label {
                display: none;
            }
            .gl-quote-type > td:first-child::before,
            .gl-quote-part-number > td:first-child::before {
                content: '';
                font-weight: 600;
                font-size: 16px;
                color: #000;
                margin-bottom: 5px;
                display: block
            }
            .gl-quote-type > td:last-child button,
            .gl-quote-part-number > td:last-child button{
                display: none;
            }
            .gl-quote-type > td:first-child::before {
                content: 'Quote Type:';
            }
            .gl-quote-part-number > td:first-child::before {
                content: 'Part Number:';
            }
            #gl-order-custom div {
                margin-bottom: 10px;
            }
            #gl-order-custom label {
                font-weight: 600;
            }
            .gl-other-shipping-method.hide {
                display: none;
            }
            .gl-options-row {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
            }
            .gl-options-row-left {
                flex: 0 0 200px;
            }
            .gl-options-row-right {
                flex: 1 1 50%;
            }
            #gl_shipping_error {
                color: #e60000;
                font-weight: bold;
                font-size: 15px;
            }
        </style>

        <?php
    }

    public function gl_shop_order_add_meta_boxes() {
        add_meta_box( 
            'gl-order-custom', 
            __( 'Shipping Options' ), 
            array($this, 'gl_shop_order_custom_metabox_callback'), 
            'shop_order', 
            'normal', 
            'high'
        );
    }
    public function gl_shop_order_custom_metabox_callback() {
        ?>
        <!-- <p><strong>Shipping</strong></p> -->
        <div class="gl-options-row">
            <div class="gl-options-row-left">
                <label for="gl_shipping_option">Shipping Method</label>
            </div>
            <div class="gl-options-row-right">
                <select id="gl_shipping_option" name="gl_shipping_option">
                    <option value="freight">Freight - Delivery From Gineico QLD Warehouse To Client To Be Confirmed</option>
                    <option value="local_freight">Local Freight - Delivery From Gineico QLD Warehouse</option>
                    <option value="internation_freight">International Freight - From Manufacturer Warehouse</option>
                    <option value="other">Other - Enter Custom Shipping Name</option>
                </select>
            </div>
        </div>
        <div class="gl-other-shipping-method hide gl-options-row">
            <div class="gl-options-row-left">
                <label for="gl_other_shipping_name">Other Shipping Method</label>
            </div>
            <div class="gl-options-row-right">
                <input type="text" id="gl_other_shipping_name" name="gl_other_shipping_name" style="width: 100%; max-width: 516px;">
            </div>
        </div>
        <div class="gl-options-row">
            <div class="gl-options-row-left">
                <label for="gl_shipping_cost">Amount</label>
            </div>
            <div class="gl-options-row-right">
                <input type="number" id="gl_shipping_cost" name="gl_shipping_cost"  step="0.01" value="0" min="0" style="max-width: 100px; text-align: right;">
            </div>
        </div>
        <div class="gl-options-row">
            <div class="gl-options-row-left">
            </div>
            <div class="gl-options-row-right">
                <div id="gl_shipping_error"></div>
                <button class="button button-primary" id="gl_add_shipping">Add Shipping</button>
            </div>
        </div>

        <?php
    }

    /**
     * Move the position of the order metaboxes
     */
    public function gl_change_order_metaboxes() {
        global $wp_meta_boxes;
        // Set up the 'normal' location with 'high' priority.
        if ( empty( $wp_meta_boxes['shop_order']['normal'] ) ) {
            $wp_meta_boxes['shop_order']['normal'] = [];
        }
        if ( empty( $wp_meta_boxes['shop_order']['normal']['high'] ) ) {
            $wp_meta_boxes['shop_order']['normal']['high'] = [];
        }

        $yith_ywraq_metabox_order = $wp_meta_boxes['shop_order']['normal']['high']['yith-ywraq-metabox-order'];
        unset($wp_meta_boxes['shop_order']['normal']['high']['yith-ywraq-metabox-order']);


        $wp_meta_boxes['shop_order']['normal']['high']['yith-ywraq-metabox-order'] = $yith_ywraq_metabox_order;
    }
} // end class

$gl_yith_woocommerce_quotes = new GL_YITH_WooCommerce_Quotes();