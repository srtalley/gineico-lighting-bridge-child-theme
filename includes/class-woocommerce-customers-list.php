<?php
/**
 * GL Customers and Wishlist Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'GL_Customers_List' ) ) {

    // class for functions related to GL Customers and Wishlist Reports
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

            add_action( 'admin_menu', array( $this, 'gl_customers_wishlist_menu_link' ), 9999 );

        }

        /**
         * Add the menu link
         */
        public function gl_customers_list_menu_link() {

           $hook = add_submenu_page(
               'woocommerce',
               'Customers List',
               'GL Customers',
               'edit_products',
               'gl_customers',
               array( &$this, 'gl_customers_list_page_callback' )
           );
           add_action( "load-$hook", [ $this, 'customer_screen_option' ] );

        }
        public function gl_customers_wishlist_menu_link() {

            $hook = add_submenu_page(
                'woocommerce',
                'Wishlist',
                'Favourites/Projects',
                'edit_products',
                'wishlist_report',
                array( &$this, 'gl_customers_wishlist_page_callback' )
            );
            add_action( "load-$hook", [ $this, 'wishlist_report_screen_option' ] );
 
         }
        /**
        * Screen options
        */
        public function customer_screen_option() {

            $option = 'per_page';
            $args = [
            'label' => 'Customers',
            'default' => 20,
            'option' => 'customers_per_page'
            ];
            
            add_screen_option( $option, $args );
        }
        public function wishlist_report_screen_option() {

            $option = 'per_page';
            $args = [
            'label' => 'Lists',
            'default' => 10,
            'option' => 'lists_per_page'
            ];
            
            add_screen_option( $option, $args );
        }
        /**
         * Save the screen options
         */
        public static function set_screen( $status, $option, $value ) {
            return $value;
        }
            
        public function gl_customers_list_page_callback() {

           // Load page template
           require_once( dirname(QODE_CHILD__FILE__) . '/admin/woocommerce-customers-list.php' );
        }
        public function gl_customers_wishlist_page_callback() {

            // Load page template
            require_once( dirname(QODE_CHILD__FILE__) . '/admin/woocommerce-customers-wishlist.php' );
         }

    } // end class

    GL_Customers_List::instance();
}
