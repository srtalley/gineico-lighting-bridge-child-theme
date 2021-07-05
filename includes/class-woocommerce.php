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
} // end class

$gl_woocommerce = new GL_WooCommerce();