<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_WooCommerce {

    public function __construct() {


        add_filter( 'woocommerce_breadcrumb_defaults', array($this, 'gl_woocommerce_set_breadcrumbs'));

        // Remove product structured data
        add_filter( 'woocommerce_structured_data_product', array($this, 'gl_remove_product_structured_data') );

        // Add a Quantity label
        add_action( 'woocommerce_before_add_to_cart_quantity', array($this, 'gl_add_quantity_label') );
        add_action( 'woocommerce_after_add_to_cart_quantity', array($this, 'gl_add_quantity_label_close') );

        add_shortcode( 'gl_wc_login_reg_messages', array($this, 'gl_wc_login_reg_messages_function') );

        // login and registration shortcodes
        add_shortcode( 'gl_wc_login_form', array($this, 'gl_wc_login_form_function') );
        add_shortcode( 'gl_wc_registration_form', array($this, 'gl_wc_registration_form_function') );

        // validate the required registration fields
        add_filter( 'woocommerce_registration_errors', array($this,'gl_wc_validate_user_frontend_fields'), 10, 3 );

        // add a redirect that will go to the my account page
        add_filter( 'woocommerce_login_redirect', array($this, 'gl_my_account_login_redirect'), 10, 1);

        // Single product PDF buttons
        add_action( 'woocommerce_single_product_summary', array($this, 'dl_after_add_to_cart_download_pdf'),39 );
        // Change the product tabs
        add_filter( 'woocommerce_product_description_tab_title', array($this, 'gl_change_single_product_description_tab_title') );
        // Move the product tabs
        add_action( 'woocommerce_before_single_product', array($this, 'gl_change_single_product_layout'), 60);

        // Set a default price if none set
        add_filter('woocommerce_product_get_price', array($this, 'set_default_price'), 10, 2);
        // add_filter('woocommerce_product_get_regular_price', array($this, 'set_default_price'), 10, 2);
        // add_filter('woocommerce_product_get_sale_price', array($this, 'set_default_price'), 10, 2);

        // My Account
        add_filter( 'woocommerce_account_menu_items', array($this, 'gl_wc_account_menu_items'), 9999 );

        add_filter( 'gettext', array($this,'gl_change_wc_text_strings'), 20, 3 );

        add_action( 'woocommerce_after_my_account', array($this, 'gl_wc_after_my_account'), 10, 1);

        add_filter( 'woocommerce_get_endpoint_url', array($this, 'gl_change_myaccount_quotation_url'), 10, 2 );

        // Registration redirect
        add_filter( 'woocommerce_registration_redirect', array($this, 'gl_redirection_after_registration'), 10, 1 );

        // Customize new user email
        add_filter( 'woocommerce_email_heading_customer_new_account', array($this, 'gl_change_customer_new_account_email_heading'), 10, 2);

        // add_filter( 'gettext', array($this,'gl_change_customer_new_account_email_text_strings'), 20, 3 );

        // Send a new user email to the admins
        add_action( 'woocommerce_created_customer', array($this, 'gl_woocommerce_created_customer_admin_notification'), 1000 );
    }

    /**
     * Modify WooCommerce breadcrumb delimiters
     */
    public function gl_woocommerce_set_breadcrumbs( $defaults ) {
        // Change the breadcrumb delimeter from '/' to '>'
        $defaults['delimiter'] = ' &gt; ';
        return $defaults;
    }


    /**
     * Remove Product structured data
     */
    public function gl_remove_product_structured_data( $markup ) {
        return '';
    }

    /**
     * Add a quantity label on the product pages
     */
    public function gl_add_quantity_label() {
        echo '<div class="gl-quantity-container"><div class="gl-quantity-label">Quantity </div>'; 
    }

    /**
     * Add a quantity label on the product pages
     */
    public function gl_add_quantity_label_close() {
        echo '</div> <!-- .gl-quantity-container -->'; 
    }

    /**
     * Shortcode to show the actions before the login form
     */
    public function gl_wc_login_reg_messages_function() {
        ob_start();
        echo '<div class="gl-wc-login-reg-messages">';
        do_action( 'woocommerce_before_customer_login_form' );
        echo '</div>';
        return ob_get_clean();
    }


    /**
     * Shortcode that outputs the WooCommerce login form
     */
    public function gl_wc_login_form_function() {
        // if ( !is_admin() ) return;
        if ( is_user_logged_in() && !current_user_can('administrator')) wp_redirect( site_url() . '/my-account' );

        // these two lines enqueue the login nocaptcha scripts
        wp_enqueue_script('login_nocaptcha_google_api');
        wp_enqueue_style('login_nocaptcha_css');

        ob_start();
        ?>
        <div class="woocommerce gl-wc-registration-form gl-wc-shortcode">

            <form class="woocommerce-form woocommerce-form-login login" method="post">

            <?php do_action( 'woocommerce_login_form_start' ); ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
            </p>

            <?php do_action( 'woocommerce_login_form' ); ?>

            <p class="form-row">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
                </label>
                <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
            </p>
            <p class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
            </p>

            <?php do_action( 'woocommerce_login_form_end' ); ?>

            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    
    /**
     * Shortcode that outputs the WooCommerce registration fields
     */
    public function gl_wc_registration_form_function() {
        // if ( is_admin() ) return;
        if ( is_user_logged_in() && !current_user_can('administrator')) wp_redirect( site_url() . '/my-account' );
        
        // these two lines enqueue the login nocaptcha scripts
        wp_enqueue_script('login_nocaptcha_google_api');
        wp_enqueue_style('login_nocaptcha_css');
                
        ob_start();

        ?>
            <div class="woocommerce gl-wc-registration-form gl-wc-shortcode">
            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

            <?php do_action( 'woocommerce_register_form_start' ); ?>

            <?php
                if(function_exists('get_field')) {
                    ?>
                    
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" value="<?php echo ( ! empty( $_POST['account_first_name'] ) ) ? esc_attr( wp_unslash( $_POST['account_first_name'] ) ) : ''; ?>" />
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" value="<?php echo ( ! empty( $_POST['account_last_name'] ) ) ? esc_attr( wp_unslash( $_POST['account_last_name'] ) ) : ''; ?>" />
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_company"><?php esc_html_e( 'Company', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_company" id="account_company" value="<?php echo ( ! empty( $_POST['account_company'] ) ) ? esc_attr( wp_unslash( $_POST['account_company'] ) ) : ''; ?>" />
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_elect"><?php esc_html_e( 'Category', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <select class="woocommerce-Input woocommerce-Input--select input-select" name="account_elect" id="account_elect">
                            <option value=""></option>
                            <?php
                                $elect_value =  !empty($_POST['account_elect']) ? esc_attr($_POST['account_elect']) : '';
                                $elect_choices = qode_get_select_field_choices('acf_user-additional-information', 'elect');
                                foreach ($elect_choices as $key => $value) {
                                    echo '<option value="' . $key . '"' . selected($elect_value, $key) . '>' . $value . '</option>';
                                }
                            ?>
                        </select>
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_phone_number"><?php esc_html_e( 'Phone Number', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_phone_number" id="account_phone_number" value="<?php echo ( ! empty( $_POST['account_phone_number'] ) ) ? esc_attr( wp_unslash( $_POST['account_phone_number'] ) ) : ''; ?>" />
                    </p>
                    <?php
                }
            ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
            </p>

            <?php
                if(function_exists('get_field')) {
                    ?>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="account_state"><?php esc_html_e( 'State', 'woocommerce' ); ?> <span class="required">*</span></label>
                        <select class="woocommerce-Input woocommerce-Input--select input-select" name="account_state" id="account_state">
                            <option value=""></option>
                            <?php
                                $state_value =  !empty($_POST['account_state']) ? esc_attr($_POST['account_state']) : '';
                                $state_choices = qode_get_select_field_choices('acf_user-additional-information', 'state');
                                foreach ($state_choices as $key => $value) {
                                    echo '<option value="' . $key . '"' . selected($state_value, $key) . '>' . $value . '</option>';
                                }
                            ?>
                        </select>
                    </p>
                    <?php
                }
            ?>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>

            <?php endif; ?>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                </p>

            <?php else : ?>

                <!-- <p><?php //esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p> -->

            <?php endif; ?>
            <?php
                if(function_exists('get_field')) {
                    ?>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label><?php esc_html_e( 'Newsletter', 'woocommerce' ); ?> <small>(<?php esc_html_e( 'would you like to join our newsletter', 'woocommerce' ); ?>)</small></label>
                        <span class="radio-container">
                            <label class="radio"><input type="radio" name="account_subscribe" value="yes" <?php echo ( ! empty( $_POST['account_subscribe'] ) and $_POST['account_subscribe'] == 'yes' or empty( $_POST['account_subscribe'] ) ) ? 'checked' : ''; ?>>Yes</label>
                            <label class="radio"><input type="radio" name="account_subscribe" value="no"  <?php echo ( ! empty( $_POST['account_subscribe'] ) and $_POST['account_subscribe'] == 'no' ) ? 'checked' : ''; ?>>No</label>
                        </span>
                    </p>
                    <?php
                }
            ?>


            <?php
            // Add a hidden field signifying our custom registration shortcode form
            echo '<input type="hidden" class="input-hidden" name="gl_wc_registration_form" id="gl_wc_registration_form" value="true">';
            ?>
            <?php do_action( 'woocommerce_register_form' ); ?>

            <p class="woocommerce-form-row form-row">
                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
            </p>

            <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>
        </div>
        <?php
            
        return ob_get_clean();
        
    }
    /**
    * Validate WooCommerce registration fields from the shortcode
    */
    public function gl_wc_validate_user_frontend_fields( $errors, $email, $username) {

        // see if the submitted form was our custom shortcode form
        $gl_wc_registration_form = sanitize_text_field(isset( $_POST[ 'gl_wc_registration_form' ] ) ? $_POST[ 'gl_wc_registration_form' ] : '');

        if($gl_wc_registration_form == 'true') {
            $required_fields = array(
                array(
                    'name' => 'account_first_name', 
                    'label' => 'First name'
                ),
                array(
                    'name' => 'account_last_name', 
                    'label' => 'Last name'
                ),
                array(
                    'name' => 'account_company', 
                    'label' => 'Company'
                ),
                array(
                    'name' => 'account_elect',
                    'label' => 'Category'
                ),
                array(
                    'name' => 'account_phone_number', 
                    'label' => 'Phone number'
                ),
                array(
                    'name' => 'email', 
                    'label' => 'Email'
                ),
                array(
                    'name' => 'account_state', 
                    'label' => 'State'
                )
            );
            // error messages
            $error_messages = '';
            foreach ( $required_fields as $field ) {
                if ( empty( $_POST[ $field['name'] ] ) ) {
                    $error_messages .= sprintf( __( '<li>%s is a required field.</li>', 'gineico' ), '<strong>' . $field['label'] . '</strong>' );
                }
            }

            if($error_messages != '') {
                $errors->add( 'validation_errors', '<ul class="gl_login_registration_errors">' . $error_messages . '</ul>');
            }

        } // end if gl_wc_registration_form
        return $errors;
    }

    
    /**
     * add a redirect that will go back to the my account page if they
     * log in from there
     */
    public function gl_my_account_login_redirect (){
        $referer = wp_get_referer();
        if($referer == '') {
            $referer = $_SERVER['HTTP_REFERER'];
        }
        if($referer == site_url( '/login/') ){
            wp_redirect( site_url( '/my-account/') );
        }
    }
    /**
     * Add PDF Download buttons on products
     */

    public function dl_after_add_to_cart_download_pdf(){

        global $product;

        if(function_exists('get_field')) {
            echo '<!-- Product Attached PDF Files -->';
            $product_details = get_field('product_details', $product->get_id());
            $installation_instructions = get_field('installation_instructions', $product->get_id());
            $photometry = get_field('photometry', $product->get_id());
            $dwg_file   = get_field('dwg_file', $product->get_id());
            if(!empty($product_details) or !empty($installation_instructions) or !empty($photometry) or !empty($dwg_file)) {
                // prepare product details link
                $product_details_alt       = $product_details['alt'];
                $product_details_url       = $product_details['url'];
                $product_details_link_text = get_field('product_details_link_text', $product->get_id());
                $product_details_link_obj  = get_field_object('product_details_link_text', $product->get_id());
                $product_details_title     = $product_details_link_obj['default_value'];
                if(!empty($product_details_link_text)) {
                    $product_details_title = $product_details_link_text;
                }
                echo '<div class="product-attached-pdf-buttons" style="margin: 0 0 20px;">';

                if(!empty($product_details)) {
                    echo '<p><a class="button" alt="' . $product_details_alt . '" href="' . $product_details_url . '" target="_blank">Download: ' . $product_details_title . '</a></p>';
                }
                echo '</div>';
            }
        }
    }
    /**
     * Change the tab title on the single product page
     */
    public function gl_change_single_product_description_tab_title() {
        return 'More Details';
    }

    /**
     * Move the tabs (buttons) above the request a quote button
     */
    public function gl_change_single_product_layout() {
        remove_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60);

        // global $product;
        // if ( $product->is_type( 'variable' ) ) {
        //     add_action('woocommerce_after_single_variation', 'woocommerce_output_product_data_tabs', 10);

        // } else {
        //     // add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_button_single_page' ), 15 );
        //     add_action('woocommerce_after_add_to_cart_button', 'woocommerce_output_product_data_tabs', 10);
        // }
        // add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 38 );

        add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 20 );

        // remove the additional information tab
        add_filter('woocommerce_product_tabs', array($this, 'gl_woocommerce_product_tabs'), 10);

        // add to the description tab
        add_filter( 'the_content', array($this, 'gl_add_additional_information_to_description_tab') );

    }

    /**
     * Set a default price of zero if none defined
     */
    public function set_default_price($price, $product){
        if($price == '') {
            $price = "0"; 
        }
        return $price; 
    }
    /**
     * Remove the additional information tab/button
     */
    public function gl_woocommerce_product_tabs($tabs){
        
        // if only the additional_information tab exists, set 
        // the description tab array so we can show the info there
        if(!array_key_exists('description', $tabs) && array_key_exists('additional_information', $tabs)) {
            $tabs['description'] = array(
                'title' => 'Description',
                'priority' => 10,
                'callback' => 'woocommerce_product_description_tab'
            );
        }

        unset( $tabs['additional_information'] ); 
        return $tabs;
    }
    /**
     * Add the information that is usually under Additional Information to
     * the Description tab
     */
    public function gl_add_additional_information_to_description_tab($content) {
        if(is_product()) {
            global $product;
            ob_start();
            $product->list_attributes();
            $product_attributes = ob_get_clean();
            if($product_attributes != '') {

                if(strip_tags($content) == '') {
                    $content = $content  . $product_attributes;

                } else {
                    $content = $content . '<h4 class="additional-information">Additional Information</h4>' . $product_attributes;

                }
            }
        }
        return $content;
    }
    /**
     * 
     * MY ACCOUNT
     * 
     */

    /**
     * Rename/Add My Account tabs
     */
    public function gl_wc_account_menu_items( $items ) {
	    unset($items['downloads']);
	    $new = array();

        if(class_exists('YITH_WCWL')) {
            $new['favourites'] = 'Favourites';
            $new['projects'] = 'Projects/Schedules';
        }
		if(class_exists('YITH_Request_Quote')) {
            $new['quotation'] = 'Request Quotes';
		}
        
        $items['quotes'] = 'Quotes Submitted';

	    $items  = array_slice( $items, 0, 1, true ) 
				+ $new 
				+ array_slice( $items, 1, NULL, true );

	    return $items;
	}
    /**
     * Change the endpoint URL for the quotation (renamed Schedule/Quote)
     * item in the WC My Account nav
     */
    public function gl_change_myaccount_quotation_url( $url, $endpoint ){
        if( $endpoint == 'favourites' ) {
            $url = site_url() . '/my-favourites/'; // Your custom URL to add to the My Account menu
        }
        if( $endpoint == 'projects' ) {
            $url = site_url() . '/my-projects/manage/'; // Your custom URL to add to the My Account menu
        }
        if( $endpoint == 'quotation' ) {
            $url = site_url() . '/request-quote/'; // Your custom URL to add to the My Account menu
        }
        return $url;
    }
    /**
     * Redirect user after login
     */
    public function gl_redirection_after_registration( $redirection_url ){
        // Change the redirection Url
        $redirection_url = site_url('/account-thank-you/'); // Home page
        return $redirection_url;
    }

    /**
     * Change various text strings
     */
    public function gl_change_wc_text_strings($translated_text, $text, $domain) {

        if($domain == 'woocommerce') {
            switch ( $translated_text ) {
               
                case (substr( $translated_text, 0, 45 ) == "From your account dashboard you can view your"):
                    $translated_text = __( 'From your account dashboard you can view', 'woocommerce');
                    break;
            }
        } 
        return $translated_text;
    }
    /**
     * Change my account links
     */
    public function gl_wc_after_my_account() {
        ?>
        <ul>
            <li><a href="<?php echo site_url('/my-favourites/'); ?>" target="_blank">Favourites</a> your favourite products</li>
            <li><a href="<?php echo site_url('/my-projects/manage/'); ?>" target="_blank">Projects/Schedules</a> that you are creating for different jobs</li>
            <li><a href="<?php echo site_url('/request-quote/'); ?>" target="_blank">Request Quotes</a> that you are getting ready for pricing</li>
            <li><a href="<?php echo wc_get_page_permalink( 'myaccount' ) . 'quotes/'; ?>">Quotes Submitted</a> your list of quotations</li>
            <li><a href="<?php echo wc_get_endpoint_url('/orders'); ?>">Orders</a></li>
        </ul>
        <p>You can manage your <a href="<?php echo site_url(); ?>/my-account/edit-address/">shipping</a> and <a href="<?php echo site_url(); ?>/my-account/edit-address/">billing addresses</a>, and edit your <a href="<?php echo site_url(); ?>/my-account/edit-account/">password</a> and <a href="<?php echo site_url(); ?>/my-account/edit-account/">account details</a>.</p>

        <?php 
    }

    /**
     * Remove the heading from the new customer email since 
     * we are using the template
     */
    public function gl_change_customer_new_account_email_heading($heading, $object) {
        return '';
    }

    /**
     * Notify admin when a new customer account is created
     */
    public function gl_woocommerce_created_customer_admin_notification( $customer_id ) {

        add_filter( 'wp_new_user_notification_email_admin', array($this, 'gl_wp_new_user_notification_email_admin'), 10, 3 );

        wp_send_new_user_notifications( $customer_id, 'admin' );

    } // end function construct

    /**
     * Customize the admin email that is sent
     */
    public function gl_wp_new_user_notification_email_admin($notification, $user, $blogname) {

        $notification['subject'] = 'Gineico Lighting New Account User Registration';
        $notification['to'] = 'admin@gineicolighting.com.au, showroom@gineico.com';

        $fields = $this->gl_get_customer_account_fields();
        // see if this is a user created by YITH Request a Quote. If so we will need to 
        // grab the additional fields from the POST array.
        $is_yith_wraq = isset($_POST['yith-ywraq-default-form-sent']) && $_POST['yith-ywraq-default-form-sent'] == 1 ? true : false;

        if($is_yith_wraq) {
            $notification['message'] = 'A new user was created on https://www.gineicolighting.com.au because the customer filled out a quote request form:' . "\n\n";
        } else {
            $notification['message'] = 'A new user has registered on https://www.gineicolighting.com.au/login/ to create a New Account to use My Account full Features ( Favourites/Projects):' . "\n\n";
        }

        $registered = $user->data->user_registered;

        $notification['message'] .= 'Registered Date: ' . date( "d/m/Y", strtotime( $registered )) . "\n\n";
        $notification['message'] .= 'Email Address: ' . $user->data->user_email . "\n\n";


        foreach ( $fields as $key => $field_args ) { 

            if($is_yith_wraq) {
                // handle the email fields if this user was created
                // from a quote
                if($key == 'elect' || $key == 'state') {
                    continue;
                } 

                if($key == 'company') $key = 'company_name';

                $current_value = sanitize_text_field($_POST[$key]);

            } else {
                // handle the fields if the user was registered
                // on the register page
                $current_value = get_user_meta($user->ID ,$key, true);
                if($key == 'state') {
                    $state_choices = qode_get_select_field_choices('acf_user-additional-information', 'state');
                    foreach ($state_choices as $key => $value) {
                        if($current_value == $key) {
                            $current_value = $value;
                        }
                    }
                }
                if($key == 'elect') {
                    $elect_choices = qode_get_select_field_choices('acf_user-additional-information', 'elect');
                    foreach ($elect_choices as $key => $value) {
                        if($current_value == $key) {
                            $current_value = $value;
                        }
                    }
                }
            }
            $notification['message'] .= $field_args['label'] . ': ' . $current_value . "\n\n";
            
        }
        return $notification;
    }

    /**
    * Get additional account fields.
    * @return array
    */
    public function gl_get_customer_account_fields() {
        return apply_filters( 'gl_customer_account_fields', array(
            'first_name' => array(
                'label'       => __( 'First Name', 'gineicolighting' )
            ),
            'last_name' => array(
                'label'       => __( 'Last Name', 'gineicolighting' )
            ),
            'company' => array(
                'label'       => __( 'Company', 'gineicolighting' )
            ),
            'elect' => array(
                'label'       => __( 'Category', 'gineicolighting' )
            ),
            'phone_number' => array(
                'label'       => __( 'Phone Number', 'gineicolighting' )
            ),
            'state' => array(
                'label'       => __( 'State', 'gineicolighting' )
            ),
        ) );
    }
} // end class

$gl_woocommerce = new GL_WooCommerce();