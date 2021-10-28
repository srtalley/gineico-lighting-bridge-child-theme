<?php 
/**
 * v1.1.9.4
 * No longer being used.
 */
namespace GineicoLighting\WooCommerce;

class Customers {

    private $customer_columns = array();
  
    public function __construct() {
        add_filter( 'pre_get_users', array($this, 'qode_filter_users_by_course_section' ));
        add_action('admin_head', array($this, 'qode_new_customer_custom_js'));
        add_action('admin_head', array($this, 'qode_edit_customer_custom_js'));
        add_action('admin_head', array($this, 'qode_after_add_customer_custom_js'));
        add_filter( 'views_users', array($this, 'qode_fix_users_counts_in_users_php') );
        // add_action( 'admin_menu', array($this, 'qode_admin_custom_menu' ));

        add_action( 'admin_init', array($this, 'gl_populate_user_columns') );
        add_action('manage_users_columns', array($this, 'modify_customer_user_columns'));
        add_action('admin_head', array($this, 'custom_admin_customer_view_css'));
        add_action('manage_users_custom_column', array($this, 'add_customer_user_columns'), 10, 3);
        add_filter('manage_users_sortable_columns', array($this, 'manage_gl_sortable_user_column'));
        add_filter('pre_get_users', array($this, 'gl_user_sortable_columns'));


    }


    public function qode_filter_users_by_course_section( $query ) {
        global $pagenow;
        if(is_admin() && 'users.php' == $pagenow) {
            if(isset($_GET['role']) and $_GET['role'] == 'customer') {
                $query->set('role__in', 'customer');
                add_filter('parent_file', array($this, 'qode_set_woocommerce_as_current'));
                add_filter('submenu_file', array($this, 'qode_set_woocommerce_customers_as_current'));
                add_action('admin_head', array($this, 'qode_customers_custom_js'));
            } else {
                $query->set('role__not_in', 'customer');
            }
        }
    }

    public function qode_set_woocommerce_as_current( $parent_file ) {
        global $pagenow;
        $pagenow = 'woocommerce';
        $parent_file = 'woocommerce';
        return $parent_file;
    }

    public function qode_set_woocommerce_customers_as_current( $submenu_file ) {
        global $self;
        $self = 'users.php?role=customer';
        $submenu_file = 'users.php?role=customer';
        return $submenu_file;
    }
    
    public function qode_customers_custom_js() {
        if(isset($_GET['user_id']) and intval($_GET['user_id'])) {
            $user = get_user_by( 'ID', intval($_GET['user_id']) );
        }
        if(!empty($user) and in_array('customer', $user->roles)) {
            $_GET['update'] = 'add';
            $_GET['id'] = intval($_GET['user_id']);
        }
        echo '<script type="text/javascript">';
        echo '$j(document).ready(function() {';
        echo '$j("h1.wp-heading-inline").html("Customers");';
        // echo 'document.title = document.title.replace("User", "Customer");';
        echo '$j("a.page-title-action").attr("href", $j("a.page-title-action").attr("href") + "?role=customer");';
        // echo '$j("div#message p").html($j("div#message p").html().replace("New user created", "New customer created"));';
        // echo '$j("div#message p").html($j("div#message p").html().replace("Edit user", "Edit customer"));';
        // echo '$j(".check-column input[#cb-select-all-1]").on("click",function(){ var checked = $j(this).is(":checked");';
        // echo 'if(checked){ $j("#the-list option").each(function() { $j(this).prop("selected", true); }); }';
        // echo 'else { $j("#the-list option").each(function() { $j(this).prop("selected", false); }); } });';
        // echo '});';
        echo '$j(".check-column input[id^=\'#cb-select-all\']").on("click",function(){ ';
        echo 'var checked = $j(this).is(":checked");';
        echo 'if(checked){ $j("#the-list option").each(function() { $j(this).prop("selected", true); });';
        echo '} else { $j("#the-list option").each(function() { $j(this).prop("selected", false); }); } ';
        echo '}); });';
        echo '</script>';
    }

    // Fix the display of Add New User page to be Add New Customer page if URL has role variable equals "customer"
    public function qode_new_customer_custom_js() {
        global $pagenow;
        if(is_admin() && 'user-new.php' == $pagenow) {
            if(isset($_GET['role']) and $_GET['role'] == 'customer') {
                echo '<script type="text/javascript">';
                echo '$j(document).ready(function() {';
                echo '$j("h1#add-new-user").html("Add New Customer");';
                echo 'document.title = document.title.replace("User", "Customer");';
                echo '$j(\'select#role option\').removeAttr("selected");';
                echo '$j(\'select#role option[value="customer"]\').attr("selected", "selected");';
                echo '$j("input#createusersub").val("Add New Customer");';
                echo '});';
                echo '</script>';
                add_filter('parent_file', array($this,'qode_set_woocommerce_as_current'));
                add_filter('submenu_file', array($this,'qode_set_woocommerce_customers_as_current'));
            }
        }
    }

    // Fix the display of Edit User page to be Edit Customer page if user has role "customer"
    public function qode_edit_customer_custom_js() {
        global $pagenow;
        $user = null;
        if(isset($_GET['user_id']) and intval($_GET['user_id'])) {
            $user = get_user_by( 'ID', intval($_GET['user_id']) );
        }
        if(empty($user)) {
            return;
        }
        if(!in_array('customer', $user->roles)) {
            return;
        }
        if(is_admin() && 'user-edit.php' == $pagenow) {
            echo '<script type="text/javascript">';
            echo '$j(document).ready(function() {';
            echo '$j("h1.wp-heading-inline").html($j("h1.wp-heading-inline").html().replace("User", "Customer"));';
            echo 'document.title = document.title.replace("User", "Customer");';
            echo '$j("a.page-title-action").attr("href", $j("a.page-title-action").attr("href") + "?role=customer");';
            echo '$j("input#submit").val("Update Customer");';
            echo '});';
            echo '</script>';
            add_filter('parent_file', array($this,'qode_set_woocommerce_as_current'));
            add_filter('submenu_file', array($this,'qode_set_woocommerce_customers_as_current'));
        }
    }

    // Fix the display of Users page after adding Customer to be Customers page
    public function qode_after_add_customer_custom_js() {
        global $pagenow;
        $user = null;
        if(isset($_GET['id']) and intval($_GET['id'])) {
            $user = get_user_by( 'ID', intval($_GET['id']) );
        }
        if(empty($user)) {
            return;
        }
        if(!in_array('customer', $user->roles)) {
            return;
        }

        if(is_admin() && 'users.php' == $pagenow) {
            wp_redirect(add_query_arg(array('role' => 'customer', 'user_id' => intval($_GET['id'])), 'users.php'));
        }
    }

    // public function qode_fix_users_counts_in_users_php( $views ) {
    //     if(isset($_GET['role']) and $_GET['role'] == 'customer') {
    //         $new_views = array();
    //         $new_views['customer'] = $views['customer'];
    //         $views = $new_views;
    //     } else {
    //         $customers = wp_strip_all_tags($views['customer']);
    //         $customers = str_replace('Customer', '', $customers);
    //         $customers = str_replace('(', '', $customers);
    //         $customers = str_replace(')', '', $customers);
    //         $customers = intval($customers);
    //         $all = wp_strip_all_tags($views['all']);
    //         $all = str_replace('All', '', $all);
    //         $all = str_replace('(', '', $all);
    //         $all = str_replace(')', '', $all);
    //         $all = intval($all);
    //         $new_all = $all - $customers;
    //         unset($views['customer']);
    //         $views['all'] = str_replace($all, $new_all, $views['all']);
    //     }
    //     return $views;
    // }

    // public function qode_admin_custom_menu() {
    //     global $menu, $submenu, $pagenow, $parent_file, $submenu_file;

    //     $user = wp_get_current_user();
    //     $role = (array)$user->roles;
    //     if(count($role) == 1) {
    //         $role = $role[0];
    //         if($role == 'sales_admin') {
    //             foreach ($menu as $key => $menu_array) {
    //                     $found = array_search('menu-posts', $menu_array);
    //                     if($found) {
    //                         unset($menu[$key]);
    //                         break;
    //                     }
    //             } 
    //             foreach ($menu as $key => $menu_array) {
    //                     $found = array_search('menu-posts-portfolio_page', $menu_array);
    //                     if($found) {
    //                         unset($menu[$key]);
    //                         break;
    //                     }
    //             } 
    //             unset($submenu['edit.php']);
    //             unset($submenu['edit.php?post_type=portfolio_page']);
    //         }
    //     }
    //     $parent_menu = 'woocommerce';
    //     $menu_name = 'GL Customers';
    //     $capability = 'manage_woocommerce';
    //     $url = 'users.php?role=customer';

    //     $submenu[$parent_menu][] = array( $menu_name, $capability, $url );
    // }

    /**
     * Change the columns shown in the customer view
     */
    public function modify_customer_user_columns($column_headers) {
        global $pagenow;
        if($pagenow == 'woocommerce') {
            unset($column_headers['name']);
            unset($column_headers['role']);
            unset($column_headers['posts']);
            if(isset($column_headers['wfls_2fa_status'])) {
                unset($column_headers['wfls_2fa_status']);
            }
            if(isset($column_headers['wfls_last_login'])) {
                unset($column_headers['wfls_last_login']);
            }
            unset($column_headers['signup_via']);
        }

        foreach ($this->customer_columns as $column) {
            $column_headers[$column['field']] = $column['label'];
        }
        return $column_headers;
    }
    
    /**
     * CSS to sort the columns better
     */
    public function custom_admin_customer_view_css() {
        $css_heading = '';
        $count = count($this->customer_columns);
        $i = 1;
        foreach($this->customer_columns as $column) {
            $css_heading .= '.column-' . $column['field'] ;
            if($i < $count) {
                $css_heading .= ',';
            }
            $i++;
        }
        echo '<style>' . $css_heading . '{width: 15%}</style>';
    }
    /**
     * Set the values for the custom columns
     */
    public function add_customer_user_columns($value, $column_name, $user_id) {
        foreach($this->customer_columns as $column) {
            if ( $column['field'] == $column_name ) {
                $value = get_user_meta( $user_id, $column['field'], true );
            }
        }
        return $value;
    }
    /**
     * Sets up the columns to be sortable
     */
    public function manage_gl_sortable_user_column( $columns ) {
        foreach($this->customer_columns as $column) {
            $columns[$column['field']] = $column['field'];
        }
        return $columns;
    }
    /**
     * Allow sorting the custom user columns
     */
    public function gl_user_sortable_columns( $query ) {

        if( ! is_admin() )
            return;
    
        $orderby = $query->get( 'orderby');
        // Set up non-numeric sortable columns
        foreach($this->customer_columns as $column) {
            if( $column['field'] == $orderby ) {
                $query->set('meta_key', $column['field'] );
                $query->set('orderby', 'meta_value'); // or meta_value_num for numbers
            }
        }

    }

    public function gl_populate_user_columns() {
        $this->customer_columns[] = array(
            'field' => 'first_name',
            'label' => 'First Name'
        );
        $this->customer_columns[] = array(
            'field' => 'last_name',
            'label' => 'Last Name'
        );
        $this->customer_columns[] = array(
            'field' => 'company',
            'label' => 'Company'
        );
        $this->customer_columns[] = array(
            'field' => 'registration_date',
            'label' => 'Registered'
        );
    }
} // end class

$gl_woocommerce_customer = new Customers();