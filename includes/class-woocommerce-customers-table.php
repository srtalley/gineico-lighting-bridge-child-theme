<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'GL_Customers_Table' ) ) {

    // class for functions related to ...
    class GL_Customers_Table extends WP_List_Table {

        private $usersearch;
        // Override parent construtor
        function __construct() {

            parent::__construct( array(
                'singular'  => __( 'gl_customer', 'gineicolighting' ),     //singular name of the listed records
                'plural'    => __( 'gl_customers', 'gineicolighting' ),   //plural name of the listed records
                'ajax'      => false // should this table support ajax?
            ) );
        }

        /**
         * Get the customer data
         */
        public function get_customers() {

            $args1 = array(
                'role'   => 'customer',
                'search' => $this->usersearch,
                'fields' => array('ID', 'user_login', 'user_email', 'user_registered'),
            );
            $wp_user_query1 = new WP_User_Query($args1);

            // do another user query if there's a search term to pull from meta
            if($this->usersearch != '') {

                $args2 = array(
                    'role'   => 'customer',
                    'fields' => array('ID', 'user_login', 'user_email', 'user_registered'),
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => 'user_login',
                            'value' => $this->usersearch,
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key' => 'first_name',
                            'value' => $this->usersearch,
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key' => 'last_name',
                            'value' => $this->usersearch,
                            'compare' => 'LIKE'
                        ),
                        array(
                            'key' => 'company',
                            'value' => $this->usersearch,
                            'compare' => 'LIKE'
                        )
                    )
                );
                $wp_user_query2 = new WP_User_Query($args2);

                $wp_user_search = new WP_User_Query();
                $wp_user_search->results = array_unique( array_merge( $wp_user_query1->results, $wp_user_query2->results ), SORT_REGULAR );
            } else {
                $wp_user_search = $wp_user_query1;
            }// end if usersearch

            $wp_user_search->post_count = count( $wp_user_search->results );

            // Query the user IDs for this page.
            $users = $wp_user_search->get_results();

            $user_data_table = array();
            foreach ($users as $user) {

                $first_name = get_user_meta( $user->ID, 'first_name', true );
                $last_name = get_user_meta( $user->ID, 'last_name', true );
                $company = get_user_meta( $user->ID, 'company', true );
                $registered_date = date( 'd/m/Y', strtotime( $user->user_registered ) );
                $user_data_table[] = array(
                    'id' => $user->ID,
                    'user_name' => $user->user_login,
                    'email' => $user->user_email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'company' => $company,
                    'registered_date' => strtotime( $user->user_registered )
                );
            }
            return $user_data_table;
        }

        /**
         * Prepare the items for the table to process
         */
        public function prepare_items() {
            $hidden = $this->get_hidden_columns();
            $usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
            $this->usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
            
            if($this->usersearch == '') {
                $sortable = $this->get_sortable_columns();
            } else {
                $sortable = array();
            }
            $columns = $this->get_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $user_data = $this->get_customers();

            $users_per_page = $this->get_items_per_page( 'customers_per_page', 20 );

            $current_page = $this->get_pagenum();
            $total_items = count( $user_data );

            $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $users_per_page //WE have to determine how many items to show on a page
            ] );

            usort( $user_data, array( &$this, 'usort_reorder' ) );
            $user_data = array_slice( $user_data, ( ( $current_page - 1 ) * $users_per_page ), $users_per_page );

            $this->items = $user_data;

        }

        /**
         * Override the parent columns method. Defines the columns to use in your listing table
         */
        public function get_columns() {
            $columns = array(
                'user_name' => 'Username',
                'email' => 'Email',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'company' => 'Company',
                'registered_date' => 'Registered'
            );
            return $columns;
        }
        
        /**
         * Define which columns are hidden
         */
        public function get_hidden_columns() {
            return array();
        }

        /**
         * Define the sortable columns
         */
        public function get_sortable_columns() {

            $sortable_columns = array(
                'user_name' => array( 'user_name', false ),
                'email' => array( 'email', false ),
                'first_name' => array( 'first_name', false ),
                'last_name' => array( 'last_name', false ),
                'company' => array( 'company', false ),
                'registered_date' => array( 'registered_date', true )
            );

            return $sortable_columns;
        }

        /**
          * Get the table data
          */
        private function table_data() {

             $data = GL_Customers_List::gl_get_members_data();

             return $data;
         }

         /**
          * Define what data to show on each column of the table
          */
         public function column_default( $item, $column_name ) {
             switch( $column_name ) {
                 case 'user_name':
                    return '<a href="' . admin_url() . '/user-edit.php?user_id=' . $item['id'] . '">' . $item[$column_name] . '</a>';
                 case 'email':
                 case 'first_name':
                 case 'last_name':
                 case 'company':
                    return $item[ $column_name ];
                 case 'registered_date':
                    return date( 'd/m/Y', $item[ $column_name ] );
                 default:
                     return print_r( $item, true ) ;
             }
         }

         /**
          * Allows you to sort the data by the variables set in the $_GET
          */
        function usort_reorder( $a, $b ) {
            // If no sort, default to title
            $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'user_login';
            // If no order, default to asc
            $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

            // Determine sort order
            if($orderby == 'registered_date') {
                $result = strcasecmp( $a[$orderby], $b[$orderby] );

            } else {
                $result = strcasecmp( $a[$orderby], $b[$orderby] );
            }
            // Send final sort direction to usort
            return ( $order === 'asc' ) ? $result : -$result;
        }

         /**
          * Override table nav
          */
         function extra_tablenav( $which ) {
             global $wpdb;

             if( $which == 'top' ) {

                 ?>
                 <div class="alignleft actions">
                     <form action="<?php echo admin_url( 'admin.php?page=gl_customers' ); ?>" method="get">
                        <style>p.search-box{float:none;display:inline-block;}</style>
                         <input type="hidden" name="page" value="gl_customers">
                         <?php $this->search_box('Search', 'search'); ?>
                         <a href="<?php echo admin_url( 'admin.php?page=gl_customers' ); ?>" class="button">Reset</a>

                     </form>
                 </div>
                 <?php
             }
         }
    }
}
