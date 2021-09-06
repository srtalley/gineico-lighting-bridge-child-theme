<?php 
/**
 * v1.1.9
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

        add_shortcode( 'gl_wc_login_form', array($this, 'gl_wc_login_form_function') );
        add_shortcode( 'gl_wc_registration_form', array($this, 'gl_wc_registration_form_function') );

    } // end function construct

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
                        <label for="account_elect"><?php esc_html_e( 'Elect', 'woocommerce' ); ?> <span class="required">*</span></label>
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
} // end class

$gl_woocommerce = new GL_WooCommerce();