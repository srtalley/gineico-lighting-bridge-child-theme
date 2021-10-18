<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_YITH_WooCommerce_Wishlist_Admin{

  
    public function __construct() {

        if(class_exists('YITH_WCWL')) {
            add_filter( 'yith_wcwl_wishlist_column', array($this, 'gl_yith_wcwl_wishlist_column'), 10, 1);
            add_filter( 'yith_wcwl_column_default', array($this, 'gl_yith_wcwl_column_default'), 10, 3);
            add_action( 'admin_init', array($this, 'gl_populate_user_columns') );

        } // end if class exists
    }

    /** 
     * Set the custom columns
     */
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
            'field' => 'email',
            'label' => 'Email'
        );
        $this->customer_columns[] = array(
            'field' => 'company',
            'label' => 'Company'
        );
    }
    public function gl_yith_wcwl_wishlist_column($columns) {
        foreach($this->customer_columns as $column) {
            $columns[$column['field']] = $column['label'];
        }
        return $columns;
    }
    public function gl_yith_wcwl_column_default($item, $wishlist, $column_name) {

        $user_id = $wishlist->get_user_id();

        foreach($this->customer_columns as $column) {
            if($column_name == $column['field']) {
                if($column['field'] == 'email') {
                    $user_info = get_userdata($user_id);
                    $value = $user_info->user_email;
                } else {
                    $value = get_user_meta( $user_id, $column['field'], true );

                }
                return $value;
            }

        }
    }
} // end class

$gl_yith_woocommerce_wishlist_admin = new GL_YITH_WooCommerce_Wishlist_Admin();