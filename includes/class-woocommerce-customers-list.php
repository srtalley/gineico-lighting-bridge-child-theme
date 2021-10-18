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

            add_action( 'admin_menu', array( &$this, 'GL_Customers_list_menu_link' ), 9999 );

            add_action( 'admin_menu', array( &$this, 
            'GL_Customers_xls_export_page' ) );
        }

        function GL_Customers_list_menu_link() {

           add_submenu_page(
               'woocommerce',
               'Active Members List & Export',
               'Active Members',
               'edit_products',
               'gl_active_members',
               array( &$this, 'GL_Customers_list_page_callback' )
           );
        }

        function GL_Customers_list_page_callback() {

           // Load page template
           require_once( dirname(QODE_CHILD__FILE__) . '/admin/woocommerce-active-members-list.php' );
        }

        function GL_Customers_xls_export_page() {

            $hook = add_submenu_page(
                'admin.php',
                'Export gl Members to Excel Spreadsheet',
                'Export gl Members to Excel Spreadsheet',
                'edit_products',
                'gl-members-excel-export',
                function() {}
                //'pwd_excel_spreadsheet_page'
            );

            add_action('load-' . $hook, function() {

                // Load page template
                require_once( dirname(QODE_CHILD__FILE__) . '/admin/woocommerce-active-members-xls-export.php' );

                exit;
            });
        }

        public static function gl_get_members_data( $membership_filter = null, $newsletter_filter = null ) {

            if( ! $membership_filter ) {
                $membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
            }

            if( ! $newsletter_filter ) {
                $newsletter_filter = filter_input( INPUT_GET, 'newsletter_consent', FILTER_SANITIZE_STRING );
            }

            global  $woocommerce;
            $currency_symbol = get_woocommerce_currency_symbol();

            $data = array();

            // get all users (with customer role)
            $args = array(
                'role' => 'customer',
                'orderby' => 'user_nicename',
                'order' => 'ASC'
            );

            $all_customers = get_users( $args );

            foreach( $all_customers as $customer ) {

                $display_data_membership_filter = false;
                $display_data_newsletter_filter = false;

                $customer_id = $customer->ID;


                $phone = get_user_meta( $customer_id, 'billing_phone', true );
                $company = get_user_meta( $customer_id, 'company', true );
                $address = array(
                    'address_1' => get_user_meta( $customer_id, 'billing_address_1', true ),
                    'address_2' => get_user_meta( $customer_id, 'billing_address_2', true ),
                    'city' => get_user_meta( $customer_id, 'billing_city', true ),
                    'state' => get_user_meta( $customer_id, 'billing_state', true ),
                    'postcode' => get_user_meta( $customer_id, 'billing_postcode', true ),
                    'country' => get_user_meta( $customer_id, 'billing_country', true )
                );

          

                    $data[] = array(
                        'name' => $customer->display_name,
                        'company' => $company,

                        'phone' => $phone,
                        'email' => $customer->user_email,
                        'address' => implode( ', ', array_filter( $address ) )
                    );
                // }
            }

            return $data;
        }

    } // end class

    GL_Customers_List::instance();
}
