<?php
/**
 * Active Members List & Export Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'GL_Customers_List' ) ) {

    // class for functions related to Active Members List & Export
    class GL_Customers_List {

        /**
         * Instance
         */
        private static $_instance = null;

        /**
         * Instance
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         */
        public static function instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct() {
            add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );

            add_action( 'admin_menu', array( $this, 'gl_customers_list_menu_link' ), 9999 );

        }

        /**
         * Add the menu link
         */
        public function gl_customers_list_menu_link() {

           $hook = add_submenu_page(
               'woocommerce',
               'Customers List & Export',
               'GL Customers',
               'edit_products',
               'gl_customers',
               array( &$this, 'gl_customers_list_page_callback' )
           );
           add_action( "load-$hook", [ $this, 'screen_option' ] );

        }
        /**
        * Screen options
        */
        public function screen_option() {

            $option = 'per_page';
            $args = [
            'label' => 'Customers',
            'default' => 10,
            'option' => 'customers_per_page'
            ];
            
            add_screen_option( $option, $args );
            
        }
        /**
         * Save the screen options
         */
        public static function set_screen( $status, $option, $value ) {
            return $value;
        }
            
        function gl_customers_list_page_callback() {

           // Load page template
           require_once( dirname(QODE_CHILD__FILE__) . '/admin/woocommerce-customers-list.php' );
        }


    } // end class

    GL_Customers_List::instance();
}
