<?php 
/**
 * v1.1.8
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

        add_shortcode( 'wc_registration_form', array($this, 'wc_registration_form_function') );

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


    public function wc_registration_form_function() {
        if ( is_admin() ) return;
        if ( is_user_logged_in() ) return;
        
        ob_start();
        
        do_action( 'woocommerce_before_customer_login_form' );
        
        ?>

            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

                <?php do_action( 'woocommerce_register_form_start' ); ?>

                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    </p>

                <?php endif; ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                </p>

                <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                    </p>

                <?php else : ?>

                    <p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

                <?php endif; ?>

                <?php do_action( 'woocommerce_register_form' ); ?>

                <p class="woocommerce-form-row form-row">
                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
                </p>

                <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>
        
        <?php
            
        return ob_get_clean();
    }
} // end class

$gl_woocommerce = new GL_WooCommerce();