<?php 

namespace GineicoLighting\Theme;

class GL_WooCommerce {

    public function __construct() {

        add_filter( 'woocommerce_breadcrumb_defaults', array($this, 'gm_woocommerce_set_breadcrumbs'));

    } // end function construct

    /**
     * Modify WooCommerce breadcrumb delimiters
     */
    public function gm_woocommerce_set_breadcrumbs( $defaults ) {
        // Change the breadcrumb delimeter from '/' to '>'
        $defaults['delimiter'] = ' &gt; ';
        return $defaults;
    }

} // end class

$gl_woocommerce = new GL_WooCommerce();