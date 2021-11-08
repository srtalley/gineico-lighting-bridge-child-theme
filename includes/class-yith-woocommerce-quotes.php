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
    }

    /** 
     * Add a random string to the end of the URL to break the cache so that the 
     * proper PDF downloads.
     */
    public function gl_ywraq_pdf_url_string($url) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz'; 
        $randomString = ''; 
    
        for ($i = 0; $i < 6; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $random_string .= $characters[$index]; 
        } 
        return $url . '?ver=' .$random_string;
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
} // end class

$gl_yith_woocommerce_quotes = new GL_YITH_WooCommerce_Quotes();