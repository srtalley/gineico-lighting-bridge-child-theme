<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_YITH_WooCommerce_Wishlist {

  
    public function __construct() {

        // add a custom button
        add_action( 'woocommerce_after_add_to_cart_form', array($this, 'bridge_child_yith_wcwl_add_to_wishlist'), 25 );

        // add_filter ( 'option_yith_wcwl_modal_enable', array($this, 'gl_filter'));
        // add_action('init', array($this, 'gl_test'));
        // add_filter( 'yith_wcwl_add_to_wishlist_params', array($this, 'gl_test'), 10, 2);

        // Ajax for add to products
        add_action( 'wp_ajax_nopriv_gl_add_to_my_products', array($this, 'gl_add_to_my_products') );
        add_action( 'wp_ajax_gl_add_to_my_products', array($this, 'gl_add_to_my_products') );

        // Ajax to see if product id is in my products list
        add_action( 'wp_ajax_nopriv_gl_check_if_product_id_in_my_products', array($this, 'gl_check_if_product_id_in_my_products') );
        add_action( 'wp_ajax_gl_check_if_product_id_in_my_products', array($this, 'gl_check_if_product_id_in_my_products') );

        // Change wishlist text strings
        add_filter( 'gettext', array($this,'gl_change_yith_wishlist_text_strings'), 20, 3 );
        add_filter( 'yith_wcwl_wishlist_manage_name_heading', array($this, 'gl_change_wishlist_manage_name_heading') );

        add_filter( 'yith_wcwl_create_wishlist_title_label', array($this, 'gl_change_wishlist_title_text') );
        add_filter( 'yith_wcwl_create_wishlist_title', array($this, 'gl_change_wishlist_title_text') );
        add_filter( 'yith_wcwl_search_wishlist_title_label', array($this, 'gl_change_wishlist_search_text') );
        add_filter( 'yith_wcwl_search_wishlist_title', array($this, 'gl_change_wishlist_search_text') );
        add_filter( 'yith_wcwl_manage_wishlist_title_label', array($this, 'gl_change_wishlist_manage_text') );
        add_filter( 'yith_wcwl_view_wishlist_title_label', array($this, 'gl_change_wishlist_view_text') );
        add_filter( 'yith_wcwl_view_wishlist_title', array($this, 'gl_change_wishlist_view_text') );
        add_filter( 'yith_wcwl_wishlist_correctly_created_message', array($this, 'gl_change_wishlist_created_text') );
        add_filter( 'yith_wcwl_wishlist_successfully_deleted_message', array($this, 'gl_change_wishlist_deleted_text') );
        add_filter( 'yith_wcwl_create_wishlist_button_label', array($this, 'gl_change_wishlist_create_button_text') );
        add_filter( 'yith_wcwl_create_new_list_text', array($this, 'gl_change_create_new_list_text') );
        add_filter( 'yith_wcwl_new_list_title_text', array($this, 'gl_change_new_list_title_text') );
        add_action( 'yith_wcwl_before_wishlist_title', array($this, 'gl_yith_wcwl_before_wishlist_title') );

        // shortcode to output the my products URL
        add_shortcode( 'gl_my_products_url', array( $this, 'gl_my_products_url_shortcode' ) );

        // redirect for /my-products to the user's products list
        add_action( 'template_redirect', array($this, 'gl_my_products_redirect' ));

       // PDF paper orientation
    //    add_filter( 'yith_wcwl_dompdf_orientation' , array($this, 'gm_ywcwl_change_paper_orientation') );        

        // PDF file url
        add_filter( 'yith_wcwl_wishlist_download_url', array($this, 'gl_ywcwl_pdf_url_string'), 10, 1 );

        // PDF parameters - apply the file name change here
        add_filter( 'yith_wcwl_pdf_parameters' , array($this, 'gl_ywcwl_pdf_parameters'), 10, 1) ;
    }


    // public function gl_filter() {
    //     return 'no';

    //     // https://gineicolighting.client.dustysun.com/product/leva-quadra-fixed/?add_to_wishlist=13468
    // }

    public function gl_test($additional_params, $atts) {
        wl($additional_params);
        // $default_wishlist = \YITH_WCWL_Wishlist_Factory::get_default_wishlist();
        // // wl($default_wishlist);
        // $default_wishlist_url = $default_wishlist ? $default_wishlist->get_url() : YITH_WCWL()->get_wishlist_url();
        // wl($default_wishlist_url);
        // $additional_params['template_part'] = 'button';
        // $additional_params['link_popup_classes'] = 'popup_button button alt';
        // $additional_params['add_to_wishlist_modal'] = 'default';
        // $additional_params['template_part'] = 'popup';
        // $additional_params['link_popup_classes'] = 'popup_button button alt add_to_wishlist single_add_to_wishlist';
        // $additional_params['add_to_wishlist_modal'] = 'yes';
        return $additional_params;
        // wl($additional_params);

    
    }

    /**
     * Get the default wishlist as an object
     */
    private function gl_get_default_wishlist_object() {
        $default_wishlist = \YITH_WCWL_Wishlist_Factory::get_default_wishlist();
        return $default_wishlist;
    }
    /**
     * Get the YITH wishlist default list
     */
    private function gl_get_default_wishlist_id() {
        $default_wishlist = $this->gl_get_default_wishlist_object();
        if($default_wishlist) {
            return $default_wishlist->get_id();
        }
    }
    /**
     * Get the YITH wishlist default url
     */
    private function gl_get_default_wishlist_url() {
        $default_wishlist = $this->gl_get_default_wishlist_object();
        $default_wishlist_url = $default_wishlist ? $default_wishlist->get_url() : YITH_WCWL()->get_wishlist_url();
        return $default_wishlist_url;
    }

    /**
     * See if the product is in the default wishlist
     */
    private function gl_is_product_in_default_wishlist($product_id) {
        $default_wishlist_id = $this->gl_get_default_wishlist_id();
        $exists = YITH_WCWL()->is_product_in_wishlist( $product_id, $default_wishlist_id );
        return $exists;
    }


    /**
     * Get the item id in the default wishlist
     */
    private function gl_get_default_wishlist_item_id($product_id) {
        $items = \YITH_WCWL_Wishlist_Factory::get_wishlist_items( array(
            'product_id' => $product_id,
            'wishlist_id' => $this->gl_get_default_wishlist_id(),
            'limit' => 1,
            'orderby' => 'dateadded',
            'order' => 'DESC'
        ) );
        if( ! $items ){
            return false;
        }

        $item = array_shift( $items );

        return $item->get_id();
    }
    function bridge_child_yith_wcwl_add_to_wishlist() {
        // add_filter ( 'option_yith_wcwl_modal_enable', array($this, 'gl_filter'));
        // add_filter( 'yith_wcwl_add_to_wishlist_params', array($this, 'gl_test'), 10, 2);

        // Check if logged in

        if(is_user_logged_in()) {
            global $product;
            $product_id = $product->get_id();
            echo '<div class="gl-yith-wcwl-wrapper">';
            echo '<div class ="yith-wcwl-add-to-wishlist">';
            // ADD JS TO SWITCH BUTTONS
            $wishlist_status = '';

            if($this->gl_is_product_in_default_wishlist($product_id)) {
                $wishlist_status = 'in_my_products';
            }
                // echo '<div class="yith-wcwl-add-button">
                //     <a href="?add_tod_wishlist=' . $product_id . '" rel="nofollow" data-product_id="' . $product_id . '" class="gl_add_to_products single_add_to_wishlist button alt" data-title="Add to my products">
                //         <i class="yith-wcwl-icon fa fa-heart-o"></i>		<span>Add to my products</span>
                //     </a>
                // </div>';
            // } else {
                // $item_id = $this->gl_get_default_wishlist_item_id($product_id);

            // If it is in the wishlist
            // echo '<a href="?remove_from_wishlist=' . $product_id . '" rel="nofollow" data-item-id="' . $item_id . '" data-product-id="' . $product_id . '" data-original-product-id="' . $product_id . '" class="delete_item   button alt" data-title="Remove from list"><i class="yith-wcwl-icon fa fa-heart"></i>		View My Products 	</a>';
            // echo '<a href="' . $this->gl_get_default_wishlist_url() . '" rel="nofollow" class="view-products button alt" data-title="View My Products"><i class="yith-wcwl-icon fa fa-heart"></i>		View My Products 	</a>';
            // }
            echo '<div class="yith-wcwl-add-button gl-wcwl-add-to-my-products ' . $wishlist_status . '">
                    <a href="?add_tod_wishlist=' . $product_id . '" rel="nofollow" data-product_id="' . $product_id . '" class="gl_add_to_products single_add_to_wishlist button alt" data-title="Add to my products">
                        <i class="yith-wcwl-icon fa fa-heart-o"></i>		<span>Add to my products</span>
                    </a>
                    <a href="' . $this->gl_get_default_wishlist_url() . '" rel="nofollow" class="view-products button alt" data-title="View My Products"><i class="yith-wcwl-icon fa fa-heart"></i>		View My Products 	</a>
                </div>';
                
            echo '<div class="gl-yith-wcwl-add-product-message"></div>';
           
           
            echo '</div> <!-- .yith-wcwl-add-to-wishlist -->';
            // echo '<div class="yith-wcwl-add-to-wishlist add-to-wishlist-13468  wishlist-fragment on-first-load" data-fragment-ref="13468" data-fragment-options="{&quot;base_url&quot;:&quot;&quot;,&quot;in_default_wishlist&quot;:false,&quot;is_single&quot;:true,&quot;show_exists&quot;:false,&quot;product_id&quot;:&quot;13468&quot;,&quot;parent_product_id&quot;:&quot;13468&quot;,&quot;product_type&quot;:&quot;variable&quot;,&quot;show_view&quot;:true,&quot;browse_wishlist_text&quot;:&quot;Browse my projects&quot;,&quot;already_in_wishslist_text&quot;:&quot;The product is already in your project list!&quot;,&quot;product_added_text&quot;:&quot;Product added!&quot;,&quot;heading_icon&quot;:&quot;fa-heart-o&quot;,&quot;available_multi_wishlist&quot;:true,&quot;disable_wishlist&quot;:false,&quot;show_count&quot;:false,&quot;ajax_loading&quot;:false,&quot;loop_position&quot;:&quot;after_add_to_cart&quot;,&quot;product_image&quot;:&quot;&quot;,&quot;label_popup&quot;:&quot;Add to my projects&quot;,&quot;add_to_wishlist_modal&quot;:&quot;default&quot;,&quot;item&quot;:&quot;add_to_wishlist&quot;}">';
			
			
            // echo '<div class="yith-wcwl-add-button">';
            // echo '<a href="?add_to_wishlist=13468" rel="nofollow" data-product-id="13468" data-product-type="variable" data-original-product-id="13468" class="add_to_wishlist single_add_to_wishlist button alt" data-title="Add to my project">';
            // echo '<i class="yith-wcwl-icon fa fa-heart-o"></i>		<span>Add to my proS</span>';
            // echo '</a>';
            // echo '</div>';
			
			// echo '</div>';

            echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );

            echo '</div> <!-- .gl-yith-wcwl-wrapper -->';
        } else {
            echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
        }

    }
    /**
     * Change various text strings
     */
    public function gl_change_yith_wishlist_text_strings($translated_text, $text, $domain) {

        if($domain == 'yith-woocommerce-wishlist') {
            switch ( $translated_text ) {
               
                case 'Choose a wishlist':
                    $translated_text = __( 'Choose your project', 'yith-woocommerce-wishlist');
                    break;
                case 'Remove from wishlist':
                    $translated_text = __( 'Remove from My Products', 'yith-woocommerce-wishlist');
                    break;
                case 'Create a new list': 
                    $translated_text = __( 'Create a new project list', 'yith-woocommerce-wishlist');
                    break;
                case 'Move to another wishlist': 
                    $translated_text = __( 'Move to a different project list', 'yith-woocommerce-wishlist');
                    break;
                case 'This item is already in the <b>%s wishlist</b>.<br/>You can move it in another list:':
                    $translated_text = __( 'This product is currently in the already in the <b>%s project list</b>.<br/>You can move it to another project list below:', 'yith-woocommerce-wishlist');
                    break;
                case '&lsaquo; Back to all wishlists': 
                    $translated_text = __( '&lsaquo; Back to projects', 'yith-woocommerce-wishlist');
                    break;
            }
        } 
        return $translated_text;
    }
    
    /**
     * Change the wishlist name heading to projects
     */
    public function gl_change_wishlist_manage_name_heading() {
        return 'Projects';
    }

    /**
     * Change the create a wishlist link text
     */
    public function gl_change_wishlist_title_text() {
        return 'Create a project';
    }
    /**
     * Change the search a wishlist link text
     */
    public function gl_change_wishlist_search_text() {
        return 'Search projects';
    }
    /**
     * Change the manage wishlist link text
     */
    public function gl_change_wishlist_manage_text() {
        return 'View your projects';
    }
    /**
     * Change the view wishlist link text
     */
    public function gl_change_wishlist_view_text() {
        return 'View your projects';
    }
    /**
     * Change the created wishlist text
     */
    public function gl_change_wishlist_created_text() {
        return 'Project created successfully';
    }
    /**
     * Change the deleted wishlist text
     */
    public function gl_change_wishlist_deleted_text() {
        return 'Project deleted successfully';
    }
    /**
     * Change the create wishlist button text
     */
    public function gl_change_wishlist_create_button_text() {
        return 'Create project';
    }
    /**
     * Change the create a new list link
     */
    public function gl_change_create_new_list_text() {
        return 'Create a new project list';
    }
    /**
     * Change the enter wishlist name text
     */
    public function gl_change_new_list_title_text() {
        return 'Enter project name';
    }
    /**
     * CSS before the wishlist table
     */
    public function gl_yith_wcwl_before_wishlist_title() {
        $default_wishlist_id = $this->gl_get_default_wishlist_id();
        echo '<style>.shop_table.wishlist_table tr[data-wishlist-id="' .$default_wishlist_id . '"] { display: none; }</style>';
    }

    /**
     * Output the my products URL via shortcode
     */
    public function gl_my_products_url_shortcode( $atts, $content = null ) {
        return $this->gl_get_default_wishlist_url();
    }

    /**
     * Redirect /my-products to the individual users products link
     */
    function gl_my_products_redirect() {
        global $wp;
        $current_url =  home_url( $wp->request );
        if($current_url == home_url( '/my-products' )) {
            wp_redirect( $this->gl_get_default_wishlist_url() );
        }
    }

    /** 
     * Change the PDF paper orientation
     */
    public function gm_ywcwl_change_paper_orientation($orientation) {
		return 'landscape';
    }
    
    /**
     * Change the Yith PDF file name
     */
    public function gl_ywcwl_pdf_file_name($pdf_file_name) {
        $pdf_file_name = 'Gineico Lighting - Project ' . $pdf_file_name;
        // $order = wc_get_order($order_id);
        // $customer_firstname = yit_get_prop( $order, '_billing_first_name', true );
        // $customer_lastname  = yit_get_prop( $order, '_billing_last_name', true );
        
        // $user_name_concatenated = $customer_firstname . '_' . $customer_lastname;

		// if($customer_lastname == null || $customer_lastname == '') {
        //     $user_name = yit_get_prop( $order, 'ywraq_customer_name', true );
        //     $user_name_parts = explode(" ", $user_name);
        //     $user_name_concatenated = implode("_", $user_name_parts);
		// }

        // $order_date = yit_get_prop($order, 'date_created', true);
		// $order_date = substr($order_date, 0, 10);
		// $order_date = str_replace('-', '_', $order_date);
        // $pdf_file_name = sanitize_file_name('Gineico Marine Quote-' . $user_name_concatenated . '-' . $order_date . '-' . $order_id . '.pdf');
        
        return $pdf_file_name;
    }

    /** 
     * Add a random string to the end of the URL to break the cache so that the 
     * proper PDF downloads.
     */
    public function gl_ywcwl_pdf_url_string($url) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz'; 
        $randomString = ''; 
    
        for ($i = 0; $i < 6; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $random_string .= $characters[$index]; 
        } 
        return $url . '&ver=' . $random_string;
    }
    public function gl_ywcwl_pdf_parameters($options) {
        add_filter( 'yith_wcwl_wishlist_formatted_title', array($this, 'gl_ywcwl_pdf_file_name'), 10, 1 );
        return $options;
    }
    private function gl_add_product_to_wishlist($product_id) {
        $item = new \YITH_WCWL_Wishlist_Item();
        $item->set_product_id( $product_id );
        $item->set_quantity( '1');
        $item->set_wishlist_id( $this->gl_get_default_wishlist_id() );
        $item->set_user_id( get_current_user_id() );
        $wishlist = $this->gl_get_default_wishlist_object();
        $wishlist->add_item( $item );
        $wishlist->save();

        // see if the above was successful
        if($this->gl_is_product_in_default_wishlist($product_id)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Ajax to handle my products button
     */
    public function gl_add_to_my_products() {

        $status = '';
        $status_msg = '';

        // Get the product or variation id
        $product_id = sanitize_text_field($_POST['product_id']);

        $add_to_default_list = $this->gl_add_product_to_wishlist($product_id);

        if($add_to_default_list) {
            $status_msg = "Product Added";
            $status = 'success';
        } else {
            $status_msg = "Unable to add to list";
            $status = 'fail';
        }
        
        $return_arr = array(
            'status_msg' => $status_msg,
            'status' => $status
        );

        wp_send_json($return_arr);
    }
    /**
     * Ajax to check if a product id is already in the 
     * my products list
     */
    public function gl_check_if_product_id_in_my_products() {
        $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );

        $product_id = sanitize_text_field($_POST['product_id']);
        $product_id_exists = 'no';
        $status_msg = '';
        // see if the product is in the default list (my products)
        if($this->gl_is_product_in_default_wishlist($product_id)) {
            $product_id_exists = 'yes';
            $status_msg = 'Already Added';
        }

        $return_arr = array(
            'status_msg' => $status_msg,
            'product_id_exists' => $product_id_exists
        );

        wp_send_json($return_arr);
    }
} // end class

$gl_yith_woocommerce_wishlist = new GL_YITH_WooCommerce_Wishlist();