<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_YITH_WooCommerce_Wishlist {

    var $wishlist_params;
  
    public function __construct() {

        if(class_exists('YITH_WCWL')) {
                
            add_shortcode( 'add_to_cart_form', array($this,'gl_add_to_cart_form_shortcode') );

            // create the default list if it has been deleted
            add_action( 'woocommerce_init', array($this, 'gl_recreate_default_list') );

            // add a the my favourites and my favourites buttons to the 
            // single product pages

            
            add_action( 'woocommerce_single_product_summary', array($this, 'gl_add_yith_wcwl_to_single_product'), 38 );

            // add_action( 'woocommerce_after_add_to_cart_form', array($this, 'gl_add_yith_wcwl_to_single_product'), 25 );

            add_action( 'bridge_qode_action_woocommerce_after_product_image', array($this, 'gl_add_yith_wcwl_to_shop'), 11);

            add_action( 'woocommerce_before_shop_loop_item', array($this, 'gl_change_loop_wishlist_button') );

            // Ajax for add to favourites
            add_action( 'wp_ajax_nopriv_gl_add_to_my_favourites', array($this, 'gl_add_to_my_favourites') );
            add_action( 'wp_ajax_gl_add_to_my_favourites', array($this, 'gl_add_to_my_favourites') );

            // Ajax to show variation form for products to select options
            add_action( 'wp_ajax_nopriv_gl_select_variation_options', array($this, 'gl_select_variation_options') );
            add_action( 'wp_ajax_gl_select_variation_options', array($this, 'gl_select_variation_options') );

            // Ajax to see if product id is in my favourites list
            add_action( 'wp_ajax_nopriv_gl_check_if_product_id_in_my_favourites', array($this, 'gl_check_if_product_id_in_my_favourites') );
            add_action( 'wp_ajax_gl_check_if_product_id_in_my_favourites', array($this, 'gl_check_if_product_id_in_my_favourites') );

            // Ajax for copy to another list
            add_action( 'wp_ajax_nopriv_gl_copy_to_another_list', array($this, 'gl_copy_to_another_list') );
            add_action( 'wp_ajax_gl_copy_to_another_list', array($this, 'gl_copy_to_another_list') );

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

            // shortcode to output the my favourites URL
            add_shortcode( 'gl_my_favourites_url', array( $this, 'gl_my_favourites_url_shortcode' ) );

            // redirect for /my-favourites to the user's favourites list
            add_action( 'template_redirect', array($this, 'gl_my_favourites_redirect' ));

        // PDF paper orientation
        //    add_filter( 'yith_wcwl_dompdf_orientation' , array($this, 'gm_ywcwl_change_paper_orientation') );        

            // PDF file url
            add_filter( 'yith_wcwl_wishlist_download_url', array($this, 'gl_ywcwl_pdf_url_string'), 10, 1 );

            // PDF parameters - apply the file name change here
            add_filter( 'yith_wcwl_pdf_parameters' , array($this, 'gl_ywcwl_pdf_parameters'), 10, 1) ;

            // add to the wishlist footer 
            add_action( 'yith_wcwl_after_wishlist_form', array($this, 'gl_copy_to_another_list_template'), 10 );
            // add a filter to get the wishlist shortcode parameters
            // so we can use it later
            add_filter( 'yith_wcwl_wishlist_params', array($this, 'gl_get_yith_wcwl_shortcode_params'), 10, 2);
        } // end if class exists
    }

    /**
     * Recreate a default wishlist if it is deleted
     */
    public function gl_recreate_default_list() {
        if(is_user_logged_in()) {
            $default_wishlist = \YITH_WCWL_Wishlist_Factory::get_default_wishlist();
            // see if the wishlist is properly named
            if(is_object($default_wishlist)) {
                $default_wishlist_name = $default_wishlist->get_formatted_name();
            } else {
                $default_wishlist_name = '';
            }
            if($default_wishlist_name != get_option( 'yith_wcwl_wishlist_title')) {
            
                if(is_object($default_wishlist)) {
                    // remove default flag 
                    global $wpdb;
                    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_wcwl_wishlists} SET is_default = %d WHERE ID = {$default_wishlist->get_id()}", 0 ) );
                }
                $args = array(
                    'wishlist_name' => get_option( 'yith_wcwl_wishlist_title'),
                    'is_default' => true,
                );
                $default_wishlist = YITH_WCWL()->add_wishlist($args);
            }
        } 
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
        if(is_object($default_wishlist)) {
            $default_wishlist_url = $default_wishlist ? $default_wishlist->get_url() : YITH_WCWL()->get_wishlist_url();
            return $default_wishlist_url;
        }
    }

    /**
     * See if the product is in the default wishlist
     */
    private function gl_is_product_in_default_wishlist($product_id) {
        $default_wishlist_id = $this->gl_get_default_wishlist_id();
        if(is_numeric($default_wishlist_id )) {
            $exists = YITH_WCWL()->is_product_in_wishlist( $product_id, $default_wishlist_id );
            return $exists;
        }
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
    /**
     * Add the my favourites and my projects buttons to the single product pages
     */
    public function gl_add_yith_wcwl_to_single_product() {
        // Check if logged in

        if(is_user_logged_in()) {
            global $product;
            $product_id = $product->get_id();
            echo '<div class="gl-yith-wcwl-wrapper">';
            echo '<div class ="yith-wcwl-add-to-wishlist">';
            // ADD JS TO SWITCH BUTTONS
            $wishlist_status = '';
            $product_message;
            if($this->gl_is_product_in_default_wishlist($product_id)) {
                $wishlist_status = 'in_my_favourites';
                $product_message = 'Already Added';
            }
              
            echo '<div class="yith-wcwl-add-button gl-wcwl-add-to-my-favourites ' . $wishlist_status . '">
                    <a href="?add_tod_wishlist=' . $product_id . '" rel="nofollow" data-product_id="' . $product_id . '" class="gl_add_to_favourites single_add_to_wishlist button alt" data-title="Add to my favourites">
                        <i class="yith-wcwl-icon fa fa-heart-o"></i>		<span>Add to my favourites</span>
                    </a>
                    <a href="' . $this->gl_get_default_wishlist_url() . '" rel="nofollow" class="view-favourites button alt" data-title="View My favourites"><i class="yith-wcwl-icon fa fa-heart"></i>		View My favourites 	</a>
                </div>';
            
            echo '<div class="gl-yith-wcwl-add-product-message">' . $product_message . '</div>';
           
            echo '</div> <!-- .yith-wcwl-add-to-wishlist -->';
           
            echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );

            echo '</div> <!-- .gl-yith-wcwl-wrapper -->';
        } else {
            echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
        }

    }
    public function gl_add_yith_wcwl_to_shop() {
        echo '<div class="gl-yith-wcwl-wrapper">';
        echo '<div class ="yith-wcwl-add-to-wishlist">';
        global $product;
            $product_id = $product->get_id();

            // ADD JS TO SWITCH BUTTONS
            $wishlist_status = '';
            $product_message = '';
            if($this->gl_is_product_in_default_wishlist($product_id)) {
                $wishlist_status = 'in_my_favourites';
                $product_message = 'Already Added';
            }
              
            echo '<div class="yith-wcwl-add-button gl-wcwl-add-to-my-favourites ' . $wishlist_status . '">
                    <a href="?add_tod_wishlist=' . $product_id . '" rel="nofollow" data-product_id="' . $product_id . '" class="gl_add_to_favourites single_add_to_wishlist button alt" data-title="Add to my favourites">
                        <i class="yith-wcwl-icon fa fa-heart-o"></i><span class="help-text-wrapper"><span class="help-text">Add to My favourites</span></span>
                    </a>
                    <a href="' . $this->gl_get_default_wishlist_url() . '" rel="nofollow" class="view-favourites button alt" data-title="View My favourites"><i class="yith-wcwl-icon fa fa-heart"></i><span class="help-text-wrapper"><span class="help-text">View My favourites</span></span></a>
                </div>';
            // echo '<div class="gl-wcwl-add-to-my-favourites-tooltip">Add to My favourites</div>';
            echo '<div class="gl-yith-wcwl-add-product-message">' . $product_message . '</div>';
        echo '</div> <!-- .yith-wcwl-add-to-wishlist -->';
           
        echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
        // echo '<div class="yith-wcwl-add-to-wishlist-tooltip">Add to My Project</div>';

        echo '</div> <!-- .gl-yith-wcwl-wrapper -->';
    }
    /**
     * Change the my projects wishlist button in the shop
     */
    public function gl_change_loop_wishlist_button() {
        add_filter( 'yith_wcwl_button_label', array($this, 'gl_change_yith_wcwl_add_to_wishlist_title') );
    }
    public function gl_change_yith_wcwl_add_to_wishlist_title() {
        return '<span class="gl-save-project-icon"></span><span class="help-text-wrapper"><span class="help-text">Add to My Project</span></span>';
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
                    $translated_text = __( 'Remove from My favourites', 'yith-woocommerce-wishlist');
                    break;
                case 'Create a new list': 
                    $translated_text = __( 'Create a new project list', 'yith-woocommerce-wishlist');
                    break;
                case 'Move to another wishlist': 
                    $translated_text = __( 'Select a new project list below:', 'yith-woocommerce-wishlist');
                    break;
                case 'This item is already in the <b>%s wishlist</b>.<br/>You can move it in another list:':
                    $translated_text = __( '', 'yith-woocommerce-wishlist');
                    break;
                case '&lsaquo; Back to all wishlists': 
                    $translated_text = __( '&lsaquo; Back to projects', 'yith-woocommerce-wishlist');
                    break;
                case 'No products added to the wishlist':
                    $translated_text = __( 'No products are in the list', 'yith-woocommerce-wishlist');
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
    public function gl_yith_wcwl_before_wishlist_title($wishlist) {
        $default_wishlist_id = $this->gl_get_default_wishlist_id();
        echo '<style>.shop_table.wishlist_table tr[data-wishlist-id="' . $default_wishlist_id . '"] { display: none; }</style>';
        // hide project links on the default list; prevent renaming list
        if(is_object($wishlist)) {

            if($wishlist->get_id() == $default_wishlist_id) {
                echo "<script> jQuery(function($) { $('document').ready(function() { $(document).off('click', '.wishlist-title-with-form h2'); $(document).off('click', '.show-title-form'); }); });</script>";
                echo '<style>.btn.button.show-title-form, .back-to-all-wishlists, .wishlist-page-links { display: none; } .wishlist-title.wishlist-title-with-form h2:hover {background:inherit;cursor:inherit;}</style>';
            }
        }

    }

    /**
     * Output the my favourites URL via shortcode
     */
    public function gl_my_favourites_url_shortcode( $atts, $content = null ) {
        return $this->gl_get_default_wishlist_url();
    }

    /**
     * Redirect /my-favourites to the individual users favourites link
     */
    function gl_my_favourites_redirect() {
        global $wp;
        $current_url =  home_url( $wp->request );
        if($current_url == home_url( '/my-favourites' )) {
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

        $current_date = date('d-m-Y', time());
        $pdf_file_name = 'Gineico Lighting - Project ' . $pdf_file_name . ' - ' . $current_date ;
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
     * Ajax to handle my favourites button
     */
    public function gl_add_to_my_favourites() {

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
     * my favourites list
     */
    public function gl_check_if_product_id_in_my_favourites() {
        $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );

        $product_id = sanitize_text_field($_POST['product_id']);
        $product_id_exists = 'no';
        $status_msg = '';
        // see if the product is in the default list (my favourites)
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

    /**
     * Show the variation selection options via Ajax
     * NOTE: The wp-util scripts must be called so make sure that is a
     * dependency on the JS file containing the JS that calls this Ajax
     */
    public function gl_select_variation_options() {
        $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );

        $product_id = sanitize_text_field($_POST['product_id']);

        $html = '';

        // First we must manually add the variation js scripts or 
        // this won't work in Ajax
        $html .= "<script type='text/javascript' id='wc-add-to-cart-variation-js-extra'>
        /* <![CDATA[ */";
        $html .= 'var wc_add_to_cart_variation_params = {"wc_ajax_url":"\/?wc-ajax=%%endpoint%%","i18n_no_matching_variations_text":"Sorry, no products matched your selection. Please choose a different combination.","i18n_make_a_selection_text":"Please select some product options before adding this product to your cart.","i18n_unavailable_text":"Sorry, this product is unavailable. Please choose a different combination."};
        /* ]]> */
        </script>';

        $html .= "<script type='text/javascript' src='" . plugins_url( 'assets/js/frontend/add-to-cart-variation.js', WC_PLUGIN_FILE) . "' id='gl-wc-add-to-cart-variation-js'></script>";

        // Next get the variation template which adds some more JS
        ob_start();
        wc_get_template( 'single-product/add-to-cart/variation.php' );
        $html .= ob_get_clean();


        $html .= $this->gl_add_to_cart_form_shortcode(array('id' => $product_id));
        
       
        $return_arr = array(

            'html' => $html
        );

        wp_send_json($return_arr);
    }

    /**
     * Shortcode to display an add to cart form; can also
     * be called by other functions
     */
    public function gl_add_to_cart_form_shortcode( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
			return '';
		}

		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);

		if ( isset( $atts['sku'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => sanitize_text_field( $atts['sku'] ),
				'compare' => '=',
			);

			$args['post_type'] = array( 'product', 'product_variation' );
		}

		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}

		$single_product = new \WP_Query( $args );

		$preselected_id = '0';

		// Check if sku is a variation.
		if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {
			$variation = new \WC_Product_Variation( $single_product->post->ID );
			$attributes = $variation->get_attributes();
			// Set preselected id to be used by JS to provide context.
			$preselected_id = $single_product->post->ID;

			// Get the parent product object.
			$args = array(
				'posts_per_page'      => 1,
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => 1,
				'p'                   => $single_product->post->post_parent,
			);

			$single_product = new \WP_Query( $args );
		?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );

					<?php foreach ( $attributes as $attr => $value ) { ?>
						$variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
					<?php } ?>
				});
			</script>
		<?php

		} 

		// For "is_single" to always make load comments_template() for reviews.
		$single_product->is_single = true;

		ob_start();

		global $wp_query;

		// Backup query object so following loops think this is a product page.
		$previous_wp_query = $wp_query;
		// @codingStandardsIgnoreStart
		$wp_query          = $single_product;
		// @codingStandardsIgnoreEnd
		wp_enqueue_script( 'wc-single-product' );

		while ( $single_product->have_posts() ) {
			$single_product->the_post();
           
			?>
			<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php 
                woocommerce_template_single_add_to_cart(); 
                yith_ywraq_render_button();
                // do_shortcode('[yith_ywraq_button_quote]');
                  ?>
			</div>
			<?php
		}

		// Restore $previous_wp_query and reset post data.
		// @codingStandardsIgnoreStart
		$wp_query = $previous_wp_query;
		// @codingStandardsIgnoreEnd
		wp_reset_postdata();

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
    }


    // COPIED
    /**
		 * Move an item to another wishlist on an ajax call
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function move_to_another_wishlist() {
			$origin_wishlist_token = isset( $_POST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_token'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$destination_wishlist_token = isset( $_POST['destination_wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['destination_wishlist_token'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$fragments = isset( $_REQUEST['fragments'] ) ? $_REQUEST['fragments'] : false; // phpcs:ignore WordPress.Security
			$moved = false;
			$message = '';

			if ( $destination_wishlist_token && $origin_wishlist_token && $item_id ) {
				if ( 'new' == $destination_wishlist_token ) {
					try {
						$destination_wishlist = YITH_WCWL()->add_wishlist();
					} catch ( Exception $e ) {
						$destination_wishlist = false;
					}
				}

				$origin_wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $origin_wishlist_token );
				$destination_wishlist = isset( $destination_wishlist ) ? $destination_wishlist : YITH_WCWL_Wishlist_Factory::get_wishlist( $destination_wishlist_token );

				if ( $origin_wishlist && $destination_wishlist && $origin_wishlist->current_user_can( 'remove_from_wishlist' ) && $destination_wishlist->current_user_can( 'add_to_wishlist' ) ) {
					$item = $origin_wishlist->get_product( $item_id );

					if ( $item ) {
						if ( $destination_item = $destination_wishlist->get_product( $item_id ) ) {
							$destination_item->set_date_added( current_time( 'mysql' ) );

							$destination_item->save();
							$item->delete();
						} else {
							$item->set_wishlist_id( $destination_wishlist->get_id() );
							$item->set_date_added( current_time( 'mysql' ) );

							$item->save();
						}

						$moved = true;
						wp_cache_delete( 'wishlist-items-' . $origin_wishlist->get_id(), 'wishlists' );
						wp_cache_delete( 'wishlist-items-' . $destination_wishlist->get_id(), 'wishlists' );

					}
				}
			}

			$wishlists = YITH_WCWL_Wishlist_Factory::get_wishlists();
			$wishlists_to_prompt = array();

			foreach ( $wishlists as $wishlist ) {
				$wishlists_to_prompt[] = array(
					'id'                       => $wishlist->get_id(),
					'wishlist_name'            => $wishlist->get_formatted_name(),
					'default'                  => $wishlist->is_default(),
					'add_to_this_wishlist_url' => isset( $item ) ? add_query_arg(
						array(
							'add_to_wishlist' => $item->get_product_id(),
							'wishlist_id' => $wishlist->get_id(),
						)
					) : '',
				);
			}

			if ( $moved ) {
				// translators: 1. Destination wishlist name.
				$message = apply_filters( 'yith_wcwl_moved_element_message', sprintf( __( 'Element correctly moved to %s', 'yith-woocommerce-wishlist' ), $destination_wishlist->get_name() ) );
			}

			$return = array(
				'result' => $moved,
				'fragments' => YITH_WCWL_Ajax_Handler::refresh_fragments( $fragments ),
				'user_wishlists' => $wishlists_to_prompt,
				'message' => $message,
			);

			wp_send_json( $return );
		}

        public function gl_copy_to_another_list() {
            $nonce_check = check_ajax_referer( 'gl_mods_init_nonce', 'nonce' );

			$origin_wishlist_token = isset( $_POST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_token'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$destination_wishlist_token = isset( $_POST['destination_wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['destination_wishlist_token'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$fragments = isset( $_REQUEST['fragments'] ) ? $_REQUEST['fragments'] : false; // phpcs:ignore WordPress.Security
			$message = '';

            if ( $destination_wishlist_token && $origin_wishlist_token && $item_id ) {

                $origin_wishlist = \YITH_WCWL_Wishlist_Factory::get_wishlist( $origin_wishlist_token );

				$destination_wishlist = isset( $destination_wishlist ) ? $destination_wishlist : \YITH_WCWL_Wishlist_Factory::get_wishlist( $destination_wishlist_token );
                wl($destination_wishlist->get_url());
                
                if ( $destination_wishlist && $destination_wishlist->current_user_can( 'add_to_wishlist' ) ) {
                    wl('hi');
					$item = $origin_wishlist->get_product( $item_id );
               
                    // if ( $item ) {

                        // if ( $destination_item = $destination_wishlist->get_product( $item_id ) ) {
                        //     // see if the item is already in that list and update the time
                        //     $destination_item->set_date_added( current_time( 'mysql' ) );
                        //     $destination_item->save();
                        // } else {

                            // $unko = $destination_wishlist->add_item($item);
                            $add_to_new_list_status = $destination_wishlist->add_product($item_id);
                            wl($unko);
                            if($add_to_new_list_status) {
                                $message = '<p>Item was copied to the <strong>' . $destination_wishlist->get_name() . '</strong> list. <strong><a href="' . $destination_wishlist->get_url() . '">View &raquo;</a></strong></p>';
                            } else {
                                $message = '<p>Item was already in the <strong>' . $destination_wishlist->get_name() . '</strong> list. <strong><a href="' . $destination_wishlist->get_url() . '">View &raquo;</a></strong></p>';
                            }
                            $destination_wishlist->save();
                            // $item->set_wishlist_id( $destination_wishlist->get_id() );
                            // $item->set_date_added( current_time( 'mysql' ) );
                            // $item->save();
                        // }

                        // wp_cache_delete( 'wishlist-items-' . $origin_wishlist->get_id(), 'wishlists' );
                        wp_cache_delete( 'wishlist-items-' . $destination_wishlist->get_id(), 'wishlists' );

                    // }


                }
            }




            // wl($_POST);
            // $status = $_POST;
            $return_arr = array(

                'message' => $message
            );
    
            wp_send_json($return_arr);
        }
        /** 
         * Get the YITH Wishlist parameters that are used by the shortcode
         * so we can use the same parameters. There may be a better way 
         * to get this directly from YITH, but this works at the moment.
         */
        public function gl_get_yith_wcwl_shortcode_params($additional_params, $atts) {
            $this->wishlist_params = $additional_params;
            return $additional_params;
        }
        /**
         * Set up our custom template used for the copy to another
         * wishlist functionality
         */
        public function gl_copy_to_another_list_template($wishlist) {
            yith_wcwl_get_template( 'wishlist-popup-copy-gl.php', $this->wishlist_params);
        }
} // end class

$gl_yith_woocommerce_wishlist = new GL_YITH_WooCommerce_Wishlist();