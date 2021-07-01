<?php 
/**
 * v1.1.7
 */
namespace GineicoLighting\Theme;

class GL_WooCommerce {

    public function __construct() {

        add_filter( 'woocommerce_breadcrumb_defaults', array($this, 'gm_woocommerce_set_breadcrumbs'));

        // Remove product structured data
        add_filter( 'woocommerce_structured_data_product', array($this, 'gm_remove_product_structured_data') );

    } // end function construct

    /**
     * Modify WooCommerce breadcrumb delimiters
     */
    public function gm_woocommerce_set_breadcrumbs( $defaults ) {
        // Change the breadcrumb delimeter from '/' to '>'
        $defaults['delimiter'] = ' &gt; ';
        return $defaults;
    }


    /**
     * Remove Product structured data
     */
    public function gm_remove_product_structured_data( $markup ) {
        return '';
    }

} // end class

$gl_woocommerce = new GL_WooCommerce();