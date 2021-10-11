<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_YITH_WooCommerce_Wishlist_Page_Template {

  
    public function __construct() {

        if(class_exists('YITH_WCWL')) {
            add_action( 'yith_wcwl_table_after_product_thumbnail', array($this, 'gl_yith_wcwl_table_after_product_thumbnail'), 10, 2 );
            
            add_action( 'gl_yith_wcwl_table_before_product_varition', array($this, 'gl_yith_wcwl_table_before_product_varition'), 10, 2 );

            add_action( 'gl_yith_wcwl_table_after_product_name', array($this, 'gl_yith_wcwl_table_after_product_name'), 10, 2 );
            
            add_action( 'yith_wcwl_table_product_after_move_to_another_wishlist', array($this, 'gl_yith_wcwl_table_product_after_move_to_another_wishlist'), 10, 2 );
            add_action( 'gl_yith_wcwl_table_mobile_after_move_to_another_wishlist', array($this, 'gl_yith_wcwl_table_product_after_move_to_another_wishlist'), 10, 2 );

        } // end if class exists
    }

    /**
     * Add the date added after the product image
     */
    public function gl_yith_wcwl_table_after_product_thumbnail( $item, $wishlist ) {
        if ( $item->get_date_added() ) :
            // translators: date added label: 1 date added.
            echo '<span class="dateadded">' . esc_html( sprintf( __( 'Added on: %s', 'yith-woocommerce-wishlist' ), $item->get_date_added_formatted() ) ) . '</span>';
        endif;

    }
  
    /** 
     * Hook into custom GL template location above the product
     * variation listing
     */
    public function gl_yith_wcwl_table_before_product_varition($item, $wishlist ) {
        echo '<div class="product-description">';
        $product_id  = '';
        $variation_description = '';
        $product = wc_get_product($item['product_id']);
        $main_product = $product;
        if ( $product->is_type( 'variation' ) ) {

            $product_id = $product->get_parent_id();
            $main_product = wc_get_product($product_id);
            $variation_description_raw = strip_tags($product->get_description());
            if($variation_description_raw != '' && $variation_description_raw != null) {
                $variation_description = '<p class="variation-description"><strong>DESCRIPTION:&nbsp;</strong>' . $variation_description_raw . '</p>';
            }
        } else {
            $product_id = $product->get_id();
        }

        $product_short_description = $main_product->get_short_description();

        echo strip_tags( substr($product_short_description, 0 , 200)) . '&hellip;';
        echo '</div>';
        echo $variation_description;
    }
    /**
     * Product info. Have to add this before the product name because 
     * bridge removes the after_product_name hook.
     */
    public function gl_yith_wcwl_table_after_product_name( $item, $wishlist ) {
        ?>
        <div class="gl-wishlist-qty"><label>QTY <input type="number" min="1" step="1" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][quantity]" value="<?php echo esc_attr( $item->get_quantity() ); ?>"/></label></div>
        <?php
        $product = wc_get_product($item['product_id']);

        if($product->is_type('variable')) {

        ?>
        <div class="yith-ywraq-add-to-quote gl-wcwl-quote-select-options-wrapper">
            <div class="yith-ywraq-add-button show" style="display:block" data-product_id="<?php $product->get_id();?>">
            <a href="#gl-wcwl-list-select-options-<?php echo $product->get_id();?>" class="gl-wcwl-list-select-options" data-product_id="<?php $product->get_id();?>" data-update_wcwl_button="true">Add Product Options</a>


            </div>
            <div id="gl-wcwl-list-select-options-<?php echo $product->get_id();?>" ><div class="gl-wcwl-quote-select-option-variation"></div>
                <div class="gl-wcwl-select-option-variation-message"></div>
            <div class="gl-wcwl-quote-select-option-variation-loader"></div>

        
        </div>							
        </div> <!-- .gl-wcwl-quote-select-options-wrapper -->
    <?php
        }
    }

    public function gl_yith_wcwl_table_product_after_move_to_another_wishlist( $item, $wishlist ) {
        echo '<a href="#gl_copy_to_another_wishlist" class="gl-open-popup-copy-to-another-wishlist-button" data-product_id="<?php $product->get_id();?>">Copy to another list &rsaquo;</a>';

        $product = wc_get_product($item['product_id']);

        if($product->is_type('variable')) {
            ?>
            <div class="yith-ywraq-add-to-quote gl-wcwl-quote-select-options-wrapper">
                <div class="yith-ywraq-add-button show" style="display:block" data-product_id="<?php $product->get_id();?>">
                    <a href="#gl-wcwl-quote-select-options-<?php echo $product->get_id();?>" class="gl-wcwl-quote-select-options button" data-product_id="<?php $product->get_id();?>" data-wraq_button="true">Request a Quote	</a>
                    <p>* product options required</p>

                </div>
                <div id="gl-wcwl-quote-select-options-<?php echo $product->get_id();?>" ><div class="gl-wcwl-quote-select-option-variation"></div>
                <div class="gl-wcwl-quote-select-option-variation-loader"></div>
                <!-- <div class="gl-wcwl-quote-select-option-variation-ywraq-buttons"></div>-->
            
            </div>							
            </div> <!-- .gl-wcwl-quote-select-options-wrapper -->
        <?php
        } else {

            if( function_exists( 'YITH_Request_Quote_Premium' ) ) {
                add_filter('yith_ywraq-btn_other_pages', '__return_true');
                add_filter('yith_ywraq_show_button_in_loop_product_type', function(){
                    $types = array(
                        'simple',
                        'subscription',
                        'external',
                        'yith-composite',
                        'variation',
                        'variable'
                    );
                    return $types;
                });
            YITH_Request_Quote_Premium()->add_button_shop();
            }
        } // end if

    }

} // end class

$gl_yith_woocommerce_wishlist_page_template = new GL_YITH_WooCommerce_Wishlist_Page_Template();