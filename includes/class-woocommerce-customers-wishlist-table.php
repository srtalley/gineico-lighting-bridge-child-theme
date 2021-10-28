<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCWL_Admin_Table' ) ) {
    require_once(  YITH_WCWL_INC . 'tables/class.yith-wcwl-admin-table.php');
}

if ( !class_exists( 'GL_Customers_Wishlist_Table' ) ) {

    // class for functions related to ...
    class GL_Customers_Wishlist_Table extends \YITH_WCWL_Admin_Table {
        private $usersearch;
        public function __construct() {
			global $status, $page;
            parent::__construct(
                array(
					'singular'  => __( 'wishlist', 'yith-woocommerce-wishlist' ),     // singular name of the listed records.
					'plural'    => __( 'wishlists', 'yith-woocommerce-wishlist' ),    // plural name of the listed records.
					'ajax'      => false,                                             // does this table support ajax?
				)
            );

        }
        public function prepare_items() {
			// sets pagination arguments.
            $per_page = $this->get_items_per_page( 'lists_per_page', 20 );

			$current_page = $this->get_pagenum();
			$total_items = count(
				YITH_WCWL()->get_wishlists(
					array(
						'user_id' => false,
						'user_search' => isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : false,
						'wishlist_visibility' => isset( $_REQUEST['wishlist_privacy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_privacy'] ) ) : 'all',
						'show_empty' => apply_filters( 'yith_wcwl_admin_table_show_empty_list', false ),
					)
				)
			);

            // set up the user search 
            $this->usersearch = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : false;

			// sets columns headers.
			$columns = $this->get_columns();
			$hidden = array();
            if($this->usersearch !== false && $this->usersearch != '') {
                $sortable = false;
            } else {
    			$sortable = $this->get_sortable_columns();
            }
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// process bulk actions.
			$this->process_bulk_action();

            $lists = array();

            // search the company field if there's a search and get the IDs
            if($this->usersearch !== false && $this->usersearch != '') {

                $search_results_count = 0;

                $args = array(
                    array('ID'),
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => 'company',
                            'value' => $this->usersearch,
                            'compare' => 'LIKE'
                        )
                    )
                );
                $wp_user_query = new WP_User_Query($args);
                $wp_user_results = $wp_user_query->results;
                foreach($wp_user_results as $result){
                    // retrieve data for table.
                    // removes the offsets and limits
                    $search_results = YITH_WCWL()->get_wishlists(
                        array(
                            'user_id' => $result->ID,
                            'orderby' => ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'dateadded',
                            'order' => ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc',
                            'wishlist_visibility' => isset( $_REQUEST['wishlist_privacy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_privacy'] ) ) : 'all',
                            'show_empty' => apply_filters( 'yith_wcwl_admin_table_show_empty_list', false ),
                        )
                    );
                    $search_results_count = $search_results_count + count($search_results);
                    $lists = array_merge($lists, $search_results);
                }
                // increase the results count
                $total_items = $total_items + $search_results_count;
            } // end if search

            // retrieve data for table.
            $main_results = YITH_WCWL()->get_wishlists(
                array(
                    'user_id' => false,
                    'orderby' => ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'dateadded',
                    'order' => ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc',
                    'limit' => $per_page,
                    'offset' => ( ( $current_page - 1 ) * $per_page ),
                    's' => $this->usersearch,
                    'wishlist_visibility' => isset( $_REQUEST['wishlist_privacy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_privacy'] ) ) : 'all',
                    'show_empty' => apply_filters( 'yith_wcwl_admin_table_show_empty_list', false ),
                )
            );  
            $lists = array_merge($lists, $main_results);

            // we need to sort the list again by date if there were
            // search results
            if($this->usersearch !== false && $this->usersearch != '') {
                $list_date_array = array();
                foreach($lists as $key => $list) {
                    $list_date_array[$key] = $list->get_date_added();
                }

            }
            usort($lists, array($this, 'sort_list_by_date_desc')); 

            $this->items = $lists;

			// sets pagination args.
			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil( $total_items / $per_page ),
				)
			);
		}

        /**
         * Sort the list array by date
         */
        private function sort_list_by_date_desc( $a, $b ) {
            return strtotime($b->get_date_added()) - strtotime($a->get_date_added());
        }
        /**
         * Override the parent columns method. Defines the columns to use in your listing table
         */
        public function get_columns() {

            $columns = array(
				// 'cb'        => '<input type="checkbox" />',
				'name'      => __( 'Name', 'yith-woocommerce-wishlist' ),
				'username'  => __( 'Username', 'yith-woocommerce-wishlist' ),
				'privacy'   => __( 'Privacy', 'yith-woocommerce-wishlist' ),
				'items'     => __( 'Items in wishlist', 'yith-woocommerce-wishlist' ),
                'email' => 'Email',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'company' => 'Company',
				'date'      => __( 'Date', 'yith-woocommerce-wishlist' ),
			);
            return $columns;
        }

        public function column_email( $item ) {
            $row = '';
            if ( isset( $item['user_id'] ) ) {
                $user = get_user_by( 'id', $item['user_id'] );
				if ( ! empty( $user ) ) {
					$row = $user->user_email;
				} 
			}
            return $row;
        }
        public function column_first_name( $item ) {
            $row = '';
            if ( isset( $item['user_id'] ) ) {
                $row = get_user_meta($item['user_id'], 'first_name', true);
			}
            return $row;

        }
        public function column_last_name( $item ) {
            $row = '';
            if ( isset( $item['user_id'] ) ) {
                $row = get_user_meta($item['user_id'], 'last_name', true);
			}
            return $row;

        }
        public function column_company( $item ) {
            $row = '';
            if ( isset( $item['user_id'] ) ) {
				$row = get_user_meta($item['user_id'], 'company', true);
			}
            return $row;
        }
        /**
         * Override table nav
         */
        function extra_tablenav( $which ) {
            global $wpdb;

            if( $which == 'top' ) {

                ?>
                <div class="alignleft actions">
                    <form action="<?php echo admin_url( 'admin.php?page=wishlist_report' ); ?>" method="get">
                        <style>.bulkactions {display: none!important;}div.submit,p.search-box{float:none !important;display:inline-block !important; margin-top: 0 !important;margin-bottom: 0 !important;}</style>
                        <input type="hidden" name="page" value="wishlist_report">
                        <?php $this->search_box('Search', 'search'); ?>
                        <a href="<?php echo admin_url( 'admin.php?page=wishlist_report' ); ?>" class="button">Reset</a>

                    </form>
                </div>
                <?php
            }
        }
    } // end class
}
