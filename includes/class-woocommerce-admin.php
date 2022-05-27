<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_WooCommerceAdmin {

    public function __construct() {
        add_action( 'current_screen', array($this,'gl_woocommerce_product_admin'), 10, 1 );

        add_action( 'woocommerce_before_order_itemmeta', array($this, 'gl_show_quote_description'), 10, 3 );

        add_filter('woocommerce_product_variation_get_sku', array($this, 'gl_test'), 10, 2 );

    }

    public function gl_test($value, $object) {
        return $value;
    }
    /**
    * Move the product metaboxes to the left in the admin area.
    */
    public function gl_woocommerce_product_admin($current_screen) {
        if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

            if($current_screen->id == 'product') {

                // prevent loading user custom order
                add_filter( 'get_user_option_meta-box-order_product', '__return_empty_string' );

                // change the order
                add_action( 'add_meta_boxes', array($this, 'gl_change_product_metaboxes'), 99 );

                // add theCSS
                add_action('admin_head', array($this, 'gl_yith_admin_css'), 1000);

            }
        } // end if 
    } // end function gl_woocommerce_product_admin

    /**
     * Move the position of the product metaboxes
     */
    public function gl_change_product_metaboxes() {
        global $wp_meta_boxes;
        // Set up the 'normal' location with 'high' priority.
        if ( empty( $wp_meta_boxes['product']['normal'] ) ) {
            $wp_meta_boxes['product']['normal'] = [];
        }
        if ( empty( $wp_meta_boxes['product']['normal']['high'] ) ) {
            $wp_meta_boxes['product']['normal']['high'] = [];
        }

        // Move the post excerpt
        $mainarea_excerpt_metabox = $wp_meta_boxes['product']['normal']['default']['postexcerpt'];
        unset($wp_meta_boxes['product']['normal']['default']['postexcerpt']);
        $wp_meta_boxes['product']['normal']['high']['postexcerpt'] = $mainarea_excerpt_metabox;
        
        $mainarea_woo_product_data_metabox = $wp_meta_boxes['product']['normal']['high']['woocommerce-product-data'];
        unset($wp_meta_boxes['product']['normal']['high']['woocommerce-product-data']);
        $wp_meta_boxes['product']['normal']['high']['woocommerce-product-data'] = $mainarea_woo_product_data_metabox;
        

        // sidebar items to search for
        $sidebar_product_metabox_search = array('product_catdiv', 'brandsdiv', 'designersdiv', 'colour_temperaturediv', 'ip_ratingdiv', 'beam_anglediv', 'control_protocoldiv', 'maximum_depthdiv', 'cridiv', 'formatdiv', 'surfacediv');
        $sidebar_product_metaboxes = array();
        // grab the sidebar product boxes

        foreach($sidebar_product_metabox_search as $metabox_key) {
            $sidebar_product_metaboxes[$metabox_key] = $wp_meta_boxes['product']['side']['core'][$metabox_key];
            unset($wp_meta_boxes['product']['side']['core'][$metabox_key]);
        }

        foreach($sidebar_product_metaboxes as $key => $metabox) {
            $wp_meta_boxes['product']['normal']['high'][$key] = $metabox;
        }

        // Move the product tags
        $sidebar_tags_metabox = $wp_meta_boxes['product']['side']['core']['tagsdiv-product_tag'];
        unset($wp_meta_boxes['product']['side']['core']['tagsdiv-product_tag']);
        // $wp_meta_boxes['product']['side']['low']['tagsdiv-product_tag'] = $sidebar_tags_metabox;
        $wp_meta_boxes['product']['normal']['high']['tagsdiv-product_tag'] = $sidebar_tags_metabox;

        // Move the YITH Request a Quote metabox
        if(isset($wp_meta_boxes['product']['normal']['high']['yith-ywraq-metabox'])) {
            $mainarea_ywraq_metabox = $wp_meta_boxes['product']['normal']['high']['yith-ywraq-metabox'];
            unset($wp_meta_boxes['product']['normal']['high']['yith-ywraq-metabox']);
            $wp_meta_boxes['product']['normal']['high']['yith-ywraq-metabox'] = $mainarea_ywraq_metabox;
    
        }

        // Move the product PDF files
        $mainarea_pdffiles_metabox = $wp_meta_boxes['product']['normal']['high']['acf-group_5bbe75339e188'];
        unset($wp_meta_boxes['product']['normal']['high']['acf-group_5bbe75339e188']);
        $wp_meta_boxes['product']['normal']['high']['acf-group_5bbe75339e188'] = $mainarea_pdffiles_metabox;

        // Move the Yoast boxes
        $mainarea_yoast_metabox = $wp_meta_boxes['product']['normal']['high']['wpseo_meta'];
        unset($wp_meta_boxes['product']['normal']['high']['wpseo_meta']);
        $wp_meta_boxes['product']['normal']['low']['wpseo_meta'] = $mainarea_yoast_metabox;

        $sidebar_yoast_metabox = $wp_meta_boxes['product']['side']['low']['yoast_internal_linking'];
        unset($wp_meta_boxes['product']['side']['low']['yoast_internal_linking']);
        $wp_meta_boxes['product']['normal']['low']['yoast_internal_linking'] = $sidebar_yoast_metabox;
      
    }
    
    /**
     * CSS for product metaboxes
     */
    public function gl_yith_admin_css() {
        ?>
        <style type="text/css">
            #normal-sortables {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                margin: 0 -10px;
            }
            #normal-sortables .postbox {
                flex: 1 1 40%;
                margin-left: 10px;
                margin-right: 10px;
            }
            #product_catdiv,
            #brandsdiv,
            #designersdiv {
                flex-basis: 30% !important;
            }
            #normal-sortables .categorydiv div.tabs-panel {
                max-height: none;
                position: relative;
                /* display: flex; */
                /* flex-direction: row; */
            }
            #normal-sortables #product_catdiv .categorydiv div.tabs-panel,
            #normal-sortables #brandsdiv .categorydiv div.tabs-panel,
            #normal-sortables #designersdiv .categorydiv div.tabs-panel {
                max-height: 300px;
            } 

            #normal-sortables ul.categorychecklist {
                /* display: flex;
                flex-direction: row;
                flex-wrap: wrap; */
                column-count: 4;
                column-width: 100px;
            }
            #normal-sortables #product_catdiv ul.categorychecklist,
            #normal-sortables #brandsdiv ul.categorychecklist,
            #normal-sortables #designersdiv ul.categorychecklist {
                display: block;
                column-count: 1;
            }
            #normal-sortables #ip_ratingdiv ul.categorychecklist {
                column-count: 7; 
                column-width: 50px;
            }
            #normal-sortables #maximum_depthdiv ul.categorychecklist {
                column-count: 2; 
                column-width: auto;
            }
            
            #normal-sortables #cridiv ul.categorychecklist {
                column-count: 8; 
                column-width: 50px;
            }
            
            #normal-sortables #surfacediv ul.categorychecklist {
                column-count: 6; 
                column-width: 80px;
            }
            #normal-sortables #control_protocoldiv ul.categorychecklist {
                column-count: 2; 
                column-width: 150px;
            }
            
            #normal-sortables #beam_anglediv ul.categorychecklist {
                column-count: 4; 
                column-width: 90px;
            }
            #normal-sortables ul.categorychecklist li {
                /* position: relative; */
            }

            #normal-sortables .wpseo-non-primary-term,
            #normal-sortables .wpseo-primary-term {
                /* position: relative; */
            }
            #normal-sortables .wpseo-non-primary-term .wpseo-make-primary-term,
            #normal-sortables .wpseo-primary-term .wpseo-is-primary-term {
                display: none !important;
                /* position: absolute;
                opacity: 0;
                z-index: -1;
                display: block;
                padding: 5px 10px;
                background-color: #fff;
                border-radius: 5px;
                transition: all 0.6s ease-in;
                box-shadow: 1px 1px 7px 5px rgb(0 0 0 / 23%); */
            }
            #normal-sortables .wpseo-non-primary-term:hover > .wpseo-make-primary-term,
            #normal-sortables .wpseo-primary-term:hover > .wpseo-is-primary-term {
                /* opacity: 1;
                z-index: 10; */
            }

            #normal-sortables ul.categorychecklist li {
                /* margin-right: 10px;
                margin-bottom: 5px; */
            }

            #normal-sortables #product_catdiv ul.categorychecklist li,
            #normal-sortables #brandsdiv ul.categorychecklist li,
            #normal-sortables #designersdiv ul.categorychecklist li {
                /* margin-right: 0;
                margin-bottom: 0; */
            }


            #woocommerce-product-data, 
            #acf-group_5bbe75339e188,
            #postexcerpt{
                flex-basis: 100% !important;
            }
            #wpseo_meta,
            #yoast_internal_linkingyoast_internal_linking {
                flex-basis: 50% !important;
            }
            .handle-order-higher, .handle-order-lower {
                display: none;
            }
            #yith-ywraq-metabox .yith-plugin-ui.metaboxes-tab .the-metabox {
                margin-left: 10px;
                margin-right: 10px;
            }
            #yith-ywraq-metabox .yith-plugin-ui.metaboxes-tab label {
                margin-left: 0;
                flex: 1 1 100%;
                margin-bottom: 20px;
            }
            #yith-ywraq-metabox #_ywraq_hide_quote_button-container {
                display: flex; 
                flex-direction: row;
                flex-wrap: wrap;
            }
            #yith-ywraq-metabox #_ywraq_hide_quote_button-container .yith-plugin-fw-field-wrapper.yith-plugin-fw-checkbox-field-wrapper {
                flex: 0 1 30px;
            }
            #yith-ywraq-metabox #_ywraq_hide_quote_button-container .clear {
                display: none;
            }
            #yith-ywraq-metabox #_ywraq_hide_quote_button-container .description {
                flex: 1 1 60%;
                min-width: auto;
                max-width: 100%;
                margin-top: 0;
            }
            #acf-group_5bbe75339e188 .inside.acf-fields {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: flex-end;
            }
            #acf-group_5bbe75339e188 .inside.acf-fields .acf-field {
                flex: 1 1 40%;
                border-top: none;
            }


            @media (max-width: 767px) {
                #normal-sortables .postbox,
                #acf-group_5bbe75339e188 .inside.acf-fields .acf-field {
                    flex-basis: 100%;
                }
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                setTimeout(function() {
                    // disable dragging and dropping
                    $('.meta-box-sortables').sortable({
                        disabled: true
                    });
                    $('.postbox .hndle').css('cursor', 'auto');

                    // open closed metaboxes
                    $('.postbox .hndle').unbind('click.postboxes');
                    $('.postbox .handlediv').remove();
                    $('.postbox').removeClass('closed');
                }, 5000);
            });
        </script>
        <?php
    }

    /**
     * Show the quote description in the order area
     */
    public function gl_show_quote_description($item_id, $item, $product) {
        if(is_object($product)) {

            $is_variation = false;
            $product->get_sku();
            // first see if this line item already has a custom description
            $quote_description_custom_meta = wc_get_order_item_meta($item_id, '_gl_quote_description_custom', true);
            $quote_description = get_post_meta($product->get_id(), 'quote_description', true);

            if($product->get_type() == 'variation') {
                $is_variation = true;
                $quote_description = get_post_meta($product->get_id(), 'quote_description', true);
                if($quote_description == '') {
                    // try to get the parent desc
                    $parent_id = $product->get_parent_id();
                    $quote_description = get_post_meta($parent_id, 'quote_description', true);
                }
            } 
            // if($quote_description != '') {
                // echo '<div class="gl-quote-description"><strong>Quote Description: </strong>' . $quote_description . '<div class="edit"><table class="gl-quote-description-edit"><tr><td><textarea name="gl-quote-description[' . $item_id . ']" disabled>' . $quote_description . '</textarea></td><td><a href="#" class=name="gl-quote-description-edit-link" data-item_id="' . $item_id . '">Edit</a><a href="#" class=name="gl-quote-description-cancel-edit-link" data-item_id="' . $item_id . '">Cancel</a> <label><input type="checkbox" name="gl-quote-description-update-product[' . $item_id . ']">Update Description for All</label></td></tr></table></div></div>';
                // echo '<div class="gl-quote-description"><strong>Quote Description: </strong>' . $quote_description . '<span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-edit-link" data-item_id="' . $item_id . '">Edit</a><a href="#" class="gl-quote-description-cancel-edit-link hide-link" data-item_id="' . $item_id . '">Cancel</a></span><div class="gl-quote-description-edit"><label class="gl-quote-description-edit-label" for="gl-quote-description[' . $item_id . ']">Enter New Quote Description:</label><textarea name="gl-quote-description[' . $item_id . ']">' . $quote_description . '</textarea> <label><input type="checkbox" name="gl-quote-description-update-product[' . $item_id . ']">&nbsp;Update Description for All</label></div></div>';

                // echo '<div class="gl-quote-description"><span class="gl-quote-description-label">Quote Description: </span><span class="gl-quote-description-text">' . $quote_description . '</span><span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-edit-link" data-item_id="' . $item_id . '">Edit</a></span><div class="gl-quote-description-edit"><label class="gl-quote-description-edit-label" for="gl-quote-description[' . $item_id . ']">Enter New Quote Description:</label><span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-cancel-edit-link hide-link" data-item_id="' . $item_id . '">Cancel</a></span><textarea class="gl-quote-description-textarea" name="gl-quote-description[' . $item_id . ']">' . $quote_description . '</textarea> <label><input type="checkbox" name="gl-quote-description-update-product[' . $item_id . ']">&nbsp;Update Description for All</label></div></div>';
                ?>
                <div class="gl-quote-description">
                    <?php if($quote_description_custom_meta != ''): ?>
                        <span class="gl-quote-description-label">Custom Quote Description: </span>
                        <span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-edit-link" data-item_id="<?php echo $item_id ; ?>">Edit</a></span>
                        <span class="gl-quote-description-text"><?php echo wpautop($quote_description_custom_meta) ; ?></span>

                        <? // set it for the text area 
                        $quote_description = $quote_description_custom_meta;
                        ?>
                    <?php else: ?>
                        <span class="gl-quote-description-label">Quote Description: </span>
                        <span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-edit-link" data-item_id="<?php echo $item_id ; ?>">Edit</a></span>
                        <span class="gl-quote-description-text"><?php echo wpautop($quote_description ); ?></span>
                    <?php endif; ?>

                    <div class="gl-quote-description-edit">
                        <label class="gl-quote-description-edit-label" for="gl-quote-description[<?php echo $item_id ; ?>]">Enter New Quote Description:</label><span class="edit gl-quote-description-edit-links"><a href="#" class="gl-quote-description-cancel-edit-link hide-link" data-item_id="<?php echo $item_id ; ?>">Cancel</a></span>
                        <textarea class="gl-quote-description-textarea" name="gl-quote-description[<?php echo $item_id ; ?>]"><?php echo $quote_description ; ?></textarea>
                        <input type="hidden" class="gl-quote-description-is-custom" name="gl-quote-description-is-custom[<?php echo $item_id ; ?>]" value="no">
                        <?php if($is_variation): ?>
                            <div><label><input type="checkbox" name="gl-quote-description-update-product-variation[<?php echo $item_id ; ?>]">&nbsp;Update description for this variation</label></div>

                            <div><label><input type="checkbox" name="gl-quote-description-update-product-parent[<?php echo $item_id ; ?>]">&nbsp;Update description for parent product</label></div>
                        <?php else: ?>
                            <div><label><input type="checkbox" name="gl-quote-description-update-product-parent[<?php echo $item_id ; ?>]">&nbsp;Update description for product</label></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            // }
        }
    }



} // end class

$gl_woocommerce_admin = new GL_WooCommerceAdmin();