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

        // Override parent construtor
        function __construct() {

            parent::__construct( array(
                'singular'  => __( 'gl_active_member', gl_TEXT_DOMAIN ),     //singular name of the listed records
                'plural'    => __( 'gl_active_members', gl_TEXT_DOMAIN ),   //plural name of the listed records
                'ajax'      => false // should this table support ajax?
            ) );
        }

        /**
         * Prepare the items for the table to process
         */
        public function prepare_items() {

            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();
            $data = $this->table_data();

            usort( $data, array( &$this, 'sort_data' ) );

            $perPage = 30;
            $currentPage = $this->get_pagenum();
            $totalItems = count( $data );

            $this->set_pagination_args(
                array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                )
            );

            $data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

            $this->_column_headers = array( $columns, $hidden, $sortable );

            $this->items = $data;
        }

        /**
         * Override the parent columns method. Defines the columns to use in your listing table
         */
        public function get_columns() {
            $columns = array(
                'name' => 'Name',
                'memberships' => 'Memberships',
                'start_date' => 'Start Date',
                'phone' => 'Phone',
                'email' => 'Email',
                'address' => 'Address'
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
                'name' => array( 'name', false ),
                'start_date' => array( 'start_date', true )
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
                 case 'name':
                 case 'company':
                 case 'start_date':
                 case 'phone':
                 case 'email':
                 case 'address':
                     return $item[ $column_name ];

                 default:
                     return print_r( $item, true ) ;
             }
         }

         /**
          * Allows you to sort the data by the variables set in the $_GET
          */
         private function sort_data( $a, $b ) {
             // Set defaults
             $orderby = 'name';
             $order = 'asc';

             // If orderby is set, use this as the sort column
             if( !empty( $_GET['orderby'] ) ) {
                 $orderby = $_GET['orderby'];
             }

             // If order is set use this as the order
             if( !empty( $_GET['order'] ) ) {
                 $order = $_GET['order'];
             }

             $result = strcmp( $a[$orderby], $b[$orderby] );

             return $result;
         }

         /**
          * Override table nav
          */
         function extra_tablenav( $which ) {
             global $wpdb;

             $membership_filter = (int) filter_input( INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT );
             $newsletter_filter = filter_input( INPUT_GET, 'newsletter_consent', FILTER_SANITIZE_STRING );

             if( $which == 'top' ) {

                 ?>
                 <div class="alignleft actions">
                     <form action="<?php echo admin_url( 'admin.php?page=gl_active_members' ); ?>" method="get">
                         <label for="filter-by-membership" style="float: left; line-height: 30px; font-weight: 600; padding-right: 5px; font-size: 14px;">Filter by Membership</label>
                         <select name="product" id="filter-by-membership">
                             <option value="0">All memberships</option>
                             <?php

                              // get all membership products
                              $params = array(
                                 'post_type' => 'product',
                                 //'post_status' => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ),
                                 'posts_per_page' => 9999,
                                 'meta_query' => array(
                                     array(
                                         'key' => 'gl_allocation_number', //meta key name here
                                         'value' => 0,
                                         'compare' => '>',
                                     )
                                 )
                             );
                             $wc_query = new WP_Query( $params );

                             if ( $wc_query->have_posts() ) {
                                while ( $wc_query->have_posts() ) {

                                    $wc_query->the_post();
                                    $product_id = get_the_ID();
                                    $product = wc_get_product( $product_id );

                                    if( $membership_filter == $product_id ) {
                                        $membership_selected = 'selected';
                                    } else {
                                        $membership_selected = '';
                                    }

                                    ?>
                                    <option <?php echo $membership_selected; ?> value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?></option>
                                    <?php
                                }
                             }

                             wp_reset_query();

                             ?>
                         </select>

                         <label for="filter-by-newsletter" style="float: left; line-height: 30px; font-weight: 600; padding-right: 5px; padding-left:10px; font-size: 14px;">Filter by Email Consent</label>
                         <select name="newsletter_consent" id="filter-by-newsletter">
                             <option value="0">All</option>
                             <option <?php echo ( $newsletter_filter == 'no' ) ? 'selected' : ''; ?> value="no">No emails.</option>
                             <option <?php echo ( $newsletter_filter == 'yes' ) ? 'selected' : ''; ?> value="yes">Yes, please email me...</option>
                         </select>

                         <input type="hidden" name="page" value="gl_active_members">
                         <input type="submit" name="filter_action" id="query-submit" class="button" value="Filter">
                         <a href="<?php echo admin_url( 'admin.php?page=gl_active_members' ); ?>" class="button">Reset</a>
                     </form>
                 </div>
                 <?php
             }
         }
    }
}
