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

        // Save the PDF name on a new quote
        add_action( 'woocommerce_new_order', array($this, 'gl_add_pdf_name_new_order'), 10, 1 );
        // update the PDF name on save
        // add_action( 'save_post', array($this, 'gl_update_pdf_name'), 9999 );
        // Ajax to update the PDF name
        add_action( 'wp_ajax_gl_save_pdf_name', array($this, 'gl_save_pdf_name') );

        add_action( 'add_meta_boxes', array($this, 'gl_shop_order_add_meta_boxes'), 40 );

        add_filter( 'yith_ywraq_metabox_fields', array($this, 'gl_yith_ywraq_metabox_fields'), 10, 3 );



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
        $pdf_revision_name_extension = '';
        $project_name = '';

        $order = wc_get_order($order_id);
        // get custom PDF name
        $gl_ywraq_pdf_revision_number = get_post_meta( $order_id, '_gl_ywraq_pdf_revision_number', true );
        if(is_array($gl_ywraq_pdf_revision_number) && isset($gl_ywraq_pdf_revision_number['html'])) {
            $pdf_revision = $gl_ywraq_pdf_revision_number['html'];
            if($pdf_revision != 0) {
                $pdf_revision_name_extension = '-REV' . $pdf_revision;
            }
        } 
        
        $ywraq_other_email_fields = get_post_meta( $order_id, 'ywraq_other_email_fields', true );
        if(is_array($ywraq_other_email_fields) && isset($ywraq_other_email_fields['Project Name'])) {
            $project_name = $ywraq_other_email_fields['Project Name'];
            $project_name = str_replace(' ', '_', $project_name);
        } 

        $customer_lastname   = yit_get_prop( $order, '_billing_last_name', true );

        if($customer_lastname == null || $customer_lastname == '') {
            $user_name = yit_get_prop( $order, 'ywraq_customer_name', true );
            $user_name_parts = explode(" ", $user_name);
            $customer_lastname = array_pop($user_name_parts);
        }

        $order_date = yit_get_prop($order, 'date_created', true);
        $order_date = substr($order_date, 0, 10);
        // $order_date = str_replace('-', '_', $order_date);
        $order_date = str_replace('-', '', $order_date);
        $pdf_file_name = 'Quote-' . $order_id . '-' . $project_name . '-' . $order_date . $pdf_revision_name_extension;
        $YITH_Request_Quote = YITH_Request_Quote_Premium();
        $path = $YITH_Request_Quote->create_storing_folder($order_id);
        $file = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '.pdf';
        // if(file_exists($file)) {

        //     // $new_pdf_file_name = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '-' . $customer_lastname . '.pdf';
        //     $new_pdf_file_name = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '.pdf';

        //     rename($file, $new_pdf_file_name);
        //     return $pdf_file_name . '.pdf';
        // }
        return $pdf_file_name .'.pdf';
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

                    addCustomVersionStringToPDFurl();
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
                    // $('.gl-other-shipping-method').css('display', 'none').removeClass('hide');
                    $('.gl-custom-label').css('display', 'none').removeClass('hide');
                    $('.gl-shipping-amount').css('display', 'none').removeClass('hide');

                    // show the custom shipping name
                    $('.gl-edit-shipping-label').on('click', function(e) {
                        e.preventDefault();
                        var that = this;
                        var current_row = $(that).parentsUntil('.gl-label-col').parent();
                        $(current_row).find('.gl-regular-label').slideUp();
                        $(current_row).find('.gl-custom-label').slideDown();
                        $(current_row).find('.gl-use-custom-name-hidden').val('true');
                    });
                    // hide the custom shipping name
                    $('.gl-hide-shipping-label').on('click', function(e) {
                        e.preventDefault();
                        var that = this;
                        var current_row = $(that).parentsUntil('.gl-label-col').parent();
                        $(current_row).find('.gl-custom-label').slideUp();
                        $(current_row).find('.gl-use-custom-name-hidden').val('false');
                        $(current_row).find('.gl-regular-label').slideDown();
                    });
                    // reset the form
                    $('#gl_reset_form').on('click', function(e) {
                        e.preventDefault();
                        $('.gl-hide-shipping-label').click();
                        $('.gl-shipping-checkbox').each(function() {
                            $(this).prop( "checked", false );
                        })
                        $('.gl-custom-shipping-name').each(function() {
                            $(this).val($(this).attr('value'));
                        });
                        $('.gl-shipping-amount').each(function() {
                            $(this).val('0');
                            $(this).hide();
                        });
                    });

                    // show amounts with checkboxes 
                    $('.gl-shipping-checkbox').on('click', function(e) {
                        if( $(this).is(':checked') ) {
                            $(this).parentsUntil('.gl-options-row').parent().find('.gl-shipping-amount').show();
                        } else {
                            $(this).parentsUntil('.gl-options-row').parent().find('.gl-shipping-amount').hide();
                        }
                    });
                    $('#gl_add_shipping').on('click', function(e) {
                        e.preventDefault();

                        // get the checkboxes
                        var shipping_options = new Array();
                        $('.gl-shipping-checkbox').each(function(){
                            current_checkbox = this;
                            if( $(current_checkbox).is(':checked') ) {
                                // get the id and then split to component parts
                                var checkbox_id = $(current_checkbox).attr('id');
                                var shipping_id = checkbox_id.split("[")[0];

                                // see if there's an amount set
                                var shipping_amount = $('#' + shipping_id + '\\[amount\\]').val();
                                if(shipping_amount < 0) {
                                    // show error
                                    $('#' + shipping_id + '\\[error\\]').show();
                                } else{
                                    $('#' + shipping_id + '\\[error\\]').hide();
                                    // see if custom name is being used
                                    var use_custom_name = $('#' + shipping_id + '\\[use_custom_name\\]').val();
                                    if(use_custom_name === 'true' || shipping_id == 'other') {
                                        // get the custom name
                                        var shipping_name = $('#' + shipping_id + '\\[custom_name\\]').val();
                                    } else {
                                        var shipping_name = $(current_checkbox).val();
                                    }

                                    // we can define the array options
                                    var this_shipping_option = { 
                                        'method' : shipping_name,
                                        'amount' : shipping_amount
                                    };
                                    // add to the array
                                    shipping_options.push(this_shipping_option);

                                }

                            }

                        });
                        // see if no items have been checked and show error
                        if (shipping_options.length === 0) {
                            // show error
                            $('#gl-shipping-error').show();
                            setTimeout(function() {
                                $('#gl-shipping-error').hide();
                            }, 10000);
                            return false;
                        }
                        /**
                         * Async function to click the add shipping button
                         * and add options
                         */
                        async function loop_shipping_options () {
                            for(var i=0;i<shipping_options.length;i++){
                                const result = await shipping_observer(shipping_options[i].method, shipping_options[i].amount);
                            }
                            return;
                        };

                        /**
                         * Waits for the loop to finish and then clicks the
                         * save button to save the shipping options
                         */
                        async function save_shipping_options(){
                            
                            $('html, body').animate({
                                scrollTop: ($('#order_shipping_line_items').first().offset().top - 150)
                            }, 500);
                            await loop_shipping_options();
                            var result = $('.button.save-action').click();
                            $('#gl_reset_form').click();
                            
                        };
                        save_shipping_options();
                      
                    });
                }

                /**
                 * function that returns a promise, which clicks
                 * the add shipping button and enters the info
                 */
                function shipping_observer(method, amount, rejectTime = 50) {

                    return new Promise((resolve,reject) => {

                        // click the button
                        $('.button.add-order-shipping').click(); 

                        // let hasChanged = false;

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

                                            // hasChanged = true;
                                            observer.disconnect();

                                            resolve(method);

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
                    });
                }

                /**
                 * Update the custom version string on the create PDF
                 * link if it has been clicked.
                 */
                function addCustomVersionStringToPDFurl() {
                    $(document).on('click', '#ywraq_pdf_button', function() {

                        var pdf_revision_number = $('#_gl_ywraq_pdf_revision_number_html').val();
                        var order_id = $('#post_ID').val();

                        var currentUrl = $(this).data('pdf');
                        var url_without_params = currentUrl.split('?')[0];
                        var url_without_extension = url_without_params.split('.pdf')[0];
                        var url_without_rev = url_without_extension.split('-REV')[0];

                        if(pdf_revision_number >= 1) {
                            var url_new_name = url_without_rev + '-REV' + pdf_revision_number + '.pdf';
                        } else {
                            var url_new_name = url_without_rev + '.pdf';
                        }
                        var url = new URL(url_new_name);

                        url.searchParams.set("ver", makeid(6)); // setting your param
                        $(this).data('pdf', url.href);


                        // AJAX
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                            data: {
                                'action': 'gl_save_pdf_name',
                                'nonce': '<?php echo wp_create_nonce( 'gl_mods_init_nonce' ); ?>',
                                'pdf_name': pdf_revision_number,
                                'order_id': order_id,
                            },
                            success: function(data) {
                                console.log(data);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown);
                            }
                        });
                    });
                }
                /**
                 * Random string generator
                 */
                function makeid(length) {
                    var result           = '';
                    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    var charactersLength = characters.length;
                    for ( var i = 0; i < length; i++ ) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                    }
                    return result;
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
            .gl-options-row.header {
                margin-bottom: 40px !important;
                padding: 0 40px;
                position: relative;
            }
            .gl-options-row.header:after {
                content: '';
                display: block;
                border-bottom: 1px solid #c5c5c5;
                position: absolute;
                bottom: -15px;
                left:  0;
                width: 100%;
            }
            .gl-options-row.header p {
                font-size: 18px;
                margin-bottom: 0;
                margin-top: 0;
                width: 100%;
            }
            .gl-options-row.header h4 {
                margin: 0;
            }
            .gl-other-shipping-method.hide {
                /* display: none; */
            }
            .gl-options-row {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
            }
            .gl-options-row-left {
                flex: 1 1 50%;
                display: flex;
                flex-direction: row;
                align-items: flex-start;
            }
            .gl-options-row-right {
                flex: 1 1 20%;
                display: flex;
                flex-direction: row;
                align-items: flex-start;
            }
            .gl-options-row input[type="text"] {
                width: 90%;
            }
            #gl_shipping_error {
                color: #e60000;
                font-weight: bold;
                font-size: 15px;
            }
            .gl-edit-shipping-label,
            .gl-hide-shipping-label {
                display: block;
                font-size: 12px;
                text-decoration: none;
            }
            .gl-checkbox-col {
                flex: 0 1 40px;
            }
            .gl-label-col {
                flex: 1 1 50%;
            }
            .gl-label-col label {
                display: block;
                font-size: 16px;
            }
            .gl-custom-label.hide {
                display: none;
            }
            .gl-shipping-amount.hide {
                display: none;
            }
            .gl-amount-error {
                display: none;
                color: #e60000;
                font-weight: bold;
                font-size: 15px;
                padding-left: 20px;
            }
            #gl-shipping-error {
                display: none;
                color: #e60000;
                font-weight: bold;
                font-size: 18px;
            }
            .button.add-order-shipping {
                display: none !important;
            }
            .wc-backbone-modal .wc-backbone-modal-content {
                min-width: 800px;
            }
            .wc-backbone-modal .wc-backbone-modal-content .widefat>tbody>tr>td:first-child {
                width: 90%;
            }
        </style>

        <?php
    }

    /**
     * Add a PDF name on new order
     */
    public function gl_add_pdf_name_new_order($order_id) {
        $value =  array(
            'html' => 0
        );
        update_post_meta( $order_id, '_gl_ywraq_pdf_revision_number', $value );
    }
    /**
     * Save the PDF name that is entered
     */
    public function gl_save_pdf_name() {
        $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );
        $value =  array(
            'html' => sanitize_text_field($_POST['pdf_name'])
        );
        $order_id = sanitize_text_field($_POST['order_id']);
        update_post_meta( $order_id, '_gl_ywraq_pdf_revision_number', $value );
    }

    public function gl_shop_order_add_meta_boxes() {
        add_meta_box( 
            'gl-order-custom', 
            __( 'Add Shipping Methods' ), 
            array($this, 'gl_shop_order_custom_metabox_callback'), 
            'shop_order', 
            'normal', 
            'high'
        );
    }
    public function gl_shop_order_custom_metabox_callback() {

        $freight_name = 'Freight - Delivery From Gineico QLD Warehouse To Client To Be Confirmed';

        $local_freight_name = 'Local Freight - Delivery From Gineico QLD Warehouse';
        
        $international_freight_name = 'International Freight - From Manufacturer Warehouse';
        ?>
        <!-- <p><strong>Shipping</strong></p> -->
        <form id="gl-shipping-options">

            <div class="gl-options-row header">
                <p>Check the shipping options below you wish to add to the quote, and the amount field will appear.</p>
                <p>When done, click the Add Shipping button to add the chosen methods to the quote.</p>
                <hr>
            </div>
            <div class="gl-options-row header">
                <div class="gl-options-row-left">
                    <h4>Shipping Method</h4>
                </div>
                <div class="gl-options-row-right">
                    <h4 style="padding-left: 60px;">Amount</h4>
                </div>
            </div>
            <div class="gl-options-row">
                <div class="gl-options-row-left">
                    <!-- <label for="gl_shipping_option">Shipping Method</label> -->
                    <div class="gl-checkbox-col">
                        <input type="checkbox" class="gl-shipping-checkbox" name="freight[name]" id="freight[name]" value="<?php echo $freight_name; ?>">
                    </div>
                    <div class="gl-label-col">
                        <div class="gl-regular-label">
                            <label for="freight[name]"><?php echo $freight_name; ?></label> <a href="#" class="gl-edit-shipping-label">(Edit Name)</a>
                        </div>
                        <div class="gl-custom-label hide">
                            <input type="text" class="gl-custom-shipping-name" id="freight[custom_name]" value="<?php echo $freight_name; ?>">
                            <a href="#" class="gl-hide-shipping-label">(Discard)</a>
                            <input type="hidden" class="gl-use-custom-name-hidden" id="freight[use_custom_name]" value="false">
                        </div>
                    </div>

                </div>
                <div class="gl-options-row-right">
                    <label for="freight[amount]"></label>
                    <input type="number" class="gl-shipping-amount hide" id="freight[amount]" name="freight[amount]"  step="0.01" value="0" min="0" style="max-width: 100px; text-align: right;">
                    <span id="freight[error]" class="gl-amount-error">Please enter an amount.</span>
                </div>
            </div>
            <div class="gl-options-row">
                <div class="gl-options-row-left">
                    <div class="gl-checkbox-col">
                        <input type="checkbox" class="gl-shipping-checkbox" name="local_freight[name]" id="local_freight[name]" value="<?php echo $local_freight_name; ?>">
                    </div>
                    <div class="gl-label-col">
                        <div class="gl-regular-label">
                            <label for="local_freight[name]"><?php echo $local_freight_name; ?></label> <a href="#" class="gl-edit-shipping-label">(Edit Name)</a>
                        </div>
                        <div class="gl-custom-label hide">
                            <input type="text" class="gl-custom-shipping-name"  id="local_freight[custom_name]" value="<?php echo $local_freight_name; ?>">
                            <a href="#" class="gl-hide-shipping-label">(Discard)</a>
                            <input type="hidden" class="gl-use-custom-name-hidden" id="local_freight[use_custom_name]" value="false">
                        </div>

                    </div>
                </div>
                <div class="gl-options-row-right">
                    <label for="local_freight[amount]"></label>
                    <input type="number" class="gl-shipping-amount hide" id="local_freight[amount]" name="local_freight[amount]"  step="0.01" value="0" min="0" style="max-width: 100px; text-align: right;">
                    <span id="local_freight[error]" class="gl-amount-error">Please enter an amount.</span>

                </div>
            </div>
            <div class="gl-options-row">
                <div class="gl-options-row-left">
                    <div class="gl-checkbox-col">
                        <input type="checkbox" class="gl-shipping-checkbox" name="international_freight[name]" id="international_freight[name]" value="<?php echo $international_freight_name; ?>">
                    </div>
                    <div class="gl-label-col">
                        <div class="gl-regular-label">
                            <label for="international_freight[name]"><?php echo $international_freight_name; ?></label> <a href="#" class="gl-edit-shipping-label">(Edit Name)</a>
                        </div>
                        <div class="gl-custom-label hide">
                            <input type="text" class="gl-custom-shipping-name"  id="international_freight[custom_name]" value="<?php echo $international_freight_name; ?>">
                            <a href="#" class="gl-hide-shipping-label">(Discard)</a>
                            <input type="hidden" class="gl-use-custom-name-hidden" id="international_freight[use_custom_name]" value="false">
                        </div>
                    </div>
                </div>
                <div class="gl-options-row-right">
                    <label for="international_freight[amount]"></label>
                    <input type="number" class="gl-shipping-amount hide" id="international_freight[amount]" name="international_freight[amount]"  step="0.01" value="0" min="0" style="max-width: 100px; text-align: right;">
                    <span id="international_freight[error]" class="gl-amount-error">Please enter an amount.</span>

                </div>
            </div>
            <div class="gl-options-row">
                <div class="gl-options-row-left">
                    <div class="gl-checkbox-col">
                        <input type="checkbox" class="gl-shipping-checkbox" name="other[name]" id="other[name]">
                    </div>
                    <div class="gl-label-col">
                        <input type="text" class="gl-custom-shipping-name" id="other[custom_name]" placeholder="Other - Enter Custom Shipping Name">
                        <input type="hidden" class="gl-use-custom-name-hidden" id="other[use_custom_name]" value="false">
                    </div>
                    </div>
                <div class="gl-options-row-right">
                    <label for="other[amount]"></label>
                    <input type="number" class="gl-shipping-amount hide" id="other[amount]" name="other[amount]"  step="0.01" value="0" min="0" style="max-width: 100px; text-align: right;">
                    <span id="other[error]" class="gl-amount-error">Please enter an amount.</span>
                </div>
            </div>
            <!-- <div class="gl-other-shipping-method hide gl-options-row">
                <div class="gl-options-row-left">
                    <label for="gl_other_shipping_name">Other Shipping Method</label>
                </div>
                <div class="gl-options-row-right">
                    <input type="text" id="gl_other_shipping_name" name="gl_other_shipping_name" style="width: 100%; max-width: 516px;">
                </div>
            </div> -->
           
            <div class="gl-options-row">
                <div class="gl-options-row-left">
                <button class="button button-primary" id="gl_reset_form">Reset Form</button>

                </div>
                <div class="gl-options-row-right">
                    <div id="gl_shipping_error"></div>
                    <button class="button button-primary" id="gl_add_shipping">Add Shipping</button>
                </div>
            </div>
            <div class="gl-options-row">
                <div id="gl-shipping-error">
                    Please choose some shipping options before clicking "Add Shipping."
                </div>
            </div>
        </form>
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

    /**
     * Add the PDF revision field
     */
    public function gl_yith_ywraq_metabox_fields( $array_fields, $fields, $group_2 ) {
        $array_fields['gl_ywraq_pdf_revision_number'] = array(
            'type'   => 'inline-fields',
            'label'  => esc_html__( 'PDF Revision Number', 'yith-woocommerce-request-a-quote' ),
            'fields' => array(
                'html' => array(
                    'type' => 'number',
                    'custom_attributes' => 'placeholder="0"',
                    'std'               => '',
                    'class'             => 'number-short',
    
                ),
            ),
        );
        return $array_fields;
    }

    /**
     * Increase the revision number on save
     */
    function gl_update_pdf_name( $post_id ){

        // Only for shop order 
        if ( 'shop_order' != $_POST[ 'post_type' ] )
            return $post_id;

        // Checking that is not an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user’s permissions (for 'shop_manager' and 'administrator' user roles)
        if ( ! current_user_can( 'edit_shop_order', $post_id ) && ! current_user_can( 'edit_shop_orders', $post_id ) )
            return $post_id;

        // Updating custom field data
        if( isset( $_POST['yit_metaboxes'] ) ) {
            if( isset( $_POST['yit_metaboxes']['_gl_ywraq_pdf_revision_number'])) {
                $current_number = sanitize_text_field($_POST['yit_metaboxes']['_gl_ywraq_pdf_revision_number']['html']);

                if($current_number >= 1) {
                    $new_number = (int) $current_number;
                    $new_number++;

                    // The new value
                    $value = array(
                        'html' => $new_number
                    );

                    // Replacing and updating the value
                    update_post_meta( $post_id, '_gl_ywraq_pdf_revision_number', $value );
                }
                // this was for text revision names
                // if (preg_match('/^[1-9][0-9]*$/', substr($current_name, -3))) { 
                //     $current_number = (int) substr($current_name, -3);
                //     $new_number = substr($current_name, 0, -3) . $current_number++;
                // } else if (preg_match('/^[1-9][0-9]*$/', substr($current_name, -2))) { 
                //     $current_number = (int) substr($current_name, -2);
                //     $new_number = substr($current_name, 0, -2) . $current_number++;
                // } else if (preg_match('/^[1-9][0-9]*$/', substr($current_name, -1))) { 
                //     $current_number = (int) substr($current_name, -1);
                //     if($current_number == 0) {
                //         $new_number = substr($current_name, 0, -1) . '1';  
                //     } else {
                //         $new_number = substr($current_name, 0, -1) . $current_number++;
                //     }
                // } else {
                //     return false;
                // }
            }


        }
    }
} // end class

$gl_yith_woocommerce_quotes = new GL_YITH_WooCommerce_Quotes();