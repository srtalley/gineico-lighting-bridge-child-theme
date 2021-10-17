<?php
/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */

$border = true;
$order_id          = yit_get_prop( $order, 'id', true );

if( function_exists('icl_get_languages') ) {
    global $sitepress;
	$lang = yit_get_prop( $order, 'wpml_language', true );
    YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}
add_filter('woocommerce_is_attribute_in_product_name','__return_false');

?>

<?php if( ( $after_list = yit_get_prop( $order, '_ywcm_request_response', true ) ) != ''): ?>
    <div class="after-list">
        <p><?php echo apply_filters( 'ywraq_quote_before_list', nl2br($after_list), $order_id ) ?></p>
    </div>
<?php endif; ?>

<?php do_action( 'yith_ywraq_email_before_raq_table', $order ); ?>


<div class="table-wrapper">
    <div class="mark"></div>
    <h5 style="margin: 0 0 10px 0; font-style: italic; font-weight: normal;">Product images are indicative only</h5>
    <table class="quote-table" cellspacing="0" cellpadding="6" style="width: 100%;" border="0">
        <thead>
        <tr>
            <?php if( get_option('ywraq_show_preview') == 'yes'): ?>
                <th scope="col" style="text-align:left; border: 1px solid #777;"><?php _e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
            <?php endif ?>
            <th scope="col" style="text-align:left; border: 1px solid #777;"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #777;"><?php _e( 'Unit Price', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #777;"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #777;"><?php _e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $items = $order->get_items();
        $currency = method_exists( $order,  'get_currency') ? $order->get_currency() :  $order->get_order_currency();
        $colspan = 3;
        if( ! empty( $items ) ):

            foreach( $items as $item ):
              
                if( isset( $item['variation_id']) && $item['variation_id'] ){
                    $_product = wc_get_product( $item['variation_id'] );
                }else{
                    $_product = wc_get_product( $item['product_id'] );
                }

                $title = $_product->get_title();

                if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
                    $title .= ' '.apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
                }

                $subtotal = wc_price( $item['line_total'] , array( 'currency' => $currency) );
                $unit_price = wc_price( $item['line_total']/$item['qty'], array( 'currency' => $currency ) );

                if ( get_option( 'ywraq_show_old_price' ) == 'yes' ) {
                    $subtotal = ( $item['line_subtotal'] != $item['line_total'] ) ? '<small><del>' . wc_price( $item['line_subtotal'], array( 'currency' => $currency) ) . '</del></small> ' . wc_price( $item['line_total'], array( 'currency' => $currency) ) : wc_price( $item['line_subtotal'] , array( 'currency' => $currency));
                    $unit_price = ( $item['line_subtotal'] != $item['line_total'] ) ? '<small><del>' . wc_price( $item['line_subtotal']/$item['qty'], array( 'currency' => $currency) ) . '</del></small> ' . wc_price( $item['line_total']/$item['qty'] ) : wc_price( $item['line_subtotal']/$item['qty'] , array( 'currency' => $currency));
                }

                //$meta = yith_ywraq_get_product_meta_from_order_item( $item['item_meta'], false );
	            $im = false;
	            if ( version_compare( WC()->version, '2.7.0', '<' ) ) {
	                $im = new WC_Order_Item_Meta( $item );
	            }

                ?>
                <tr>
                    <?php if( get_option('ywraq_show_preview') == 'yes'): ?>
                        <td scope="col" style="text-align:center; border-left: 1px solid #777; border-color: #777;">
                            <?php
                            $image_id = $_product->get_image_id();
                            if ( $image_id ) {
	                            $thumbnail_id  = $image_id;
	                            $thumbnail_url = apply_filters( 'ywraq_pdf_product_thumbnail', get_attached_file( $thumbnail_id ), $thumbnail_id );
                            } else {
	                            $thumbnail_url = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '';
                            }
                            $thumbnail = sprintf( '<img src="%s" style="max-width:100px; max-height:87px;"/>', $thumbnail_url );

                            $colspan = 4;
                            if ( ! $_product->is_visible() ) {
	                            echo $thumbnail;
                            } else {
	                            printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                            }
                            ?>
                        </td>
                    <?php endif ?>
                    <td scope="col" style="text-align:left; border-left: 1px solid #777; border-color: #777;">
                    <?php //echo $title
                    //BEGIN GL CUSTOM
                    //app.launchURL("http://www.mycompany.com/pdfDocument.pdf", true);
							echo '<a style="text-decoration: none; color: #e2ae68; font-weight: bold;" target="_blank" href="' . esc_url( $_product->get_permalink() ) . '">' . esc_html( $title ) . '</a>';
                    // END GL CUSTOM ?>
                       <small><?php

                            //BEGIN GL CUSTOM	
							echo '<div class="product-description">';
							$product_id  = '';
							$variation_description = '';
							if($_product->is_type('variable') || $_product->is_type('variation')) {
								$product_id = $_product->get_parent_id();
								$variation_description_raw = strip_tags($_product->get_variation_description());
								if($variation_description_raw != '' && $variation_description_raw != null) {
									$variation_description = '<ul class="wc-item-meta"><li><p><strong class="wc-item-meta-label" style="vertical-align: top;">Description:&nbsp;</strong>' . $variation_description_raw . '</p></li></ul>';
								}

							} else if($_product->is_type('simple')) {
								$product_id = $_product->get_id();
							}
							if($product_id != '') {
								$product = wc_get_product($product_id);
								$product_short_description = $product->get_short_description();

									echo strip_tags( substr($product->get_short_description(), 0 , 200)) . '&hellip; <a style="text-decoration: none; color: #e2ae68;" target="_blank" href="' . esc_url( $_product->get_permalink() ) . '">Read More</a>';
								
							}
							echo '</div>';
                            echo $variation_description;
                            //END GL CUSTOM

						   if ( $im ) {
		                       $im->display();
	                       } else {
                            wc_display_item_meta( $item );
	                       }
	                       ?></small>
                           
                           
                           </td>
                    <td scope="col" style="text-align:center; border-left: 1px solid #777; border-color: #777;"><?php echo $unit_price ?></td>
                    <td scope="col" style="text-align:center; border-left: 1px solid #777; border-color: #777;"><?php echo $item['qty'] ?></td>

                    <td scope="col" class="last-col" style="text-align:right; border-left: 1px solid #777; border-right: 1px solid #777; border-color: #777;"><?php echo apply_filters('ywraq_quote_subtotal_item', ywraq_formatted_line_total( $order, $item ), $item['line_total'], $_product); ?></td>
                </tr>

            <?php
            endforeach; ?>

            <?php
            foreach ( $order->get_order_item_totals() as $key => $total ) {
                ?>
                    <tr>
                        <th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;"><?php echo $total['label']; ?></th>
                        <td scope="col" class="last-col" style="text-align:right; border-left: 1px solid #777; border-right: 1px solid #777; border-color: #777;"><?php echo $total['value']; ?></td>
                    </tr>
                <?php    } ?>
        <?php endif; ?>


        </tbody>
    </table>
</div>
<?php if( get_option( 'ywraq_pdf_link' ) == 'yes'): ?>
<div>
    <table>
        <tr>
            <?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ): ?>
            <td><a href="<?php echo esc_url( add_query_arg( array( 'request_quote' => $order_id, 'status' => 'accepted', 'raq_nonce' => ywraq_get_token( 'accept-request-quote', $order_id, yit_get_prop( $order, 'ywraq_customer_email', true ) ) ), YITH_Request_Quote()->get_raq_page_url() ) ) ?>" class="pdf-button"><?php ywraq_get_label('accept', true) ?></a></td>
            <?php endif;
            echo ( get_option( 'ywraq_show_accept_link' ) != 'no' && get_option( 'ywraq_show_reject_link' ) != 'no' ) ? '<td><span style="color: #666666">|</span></td>' : '';
            if ( get_option( 'ywraq_show_reject_link' ) != 'no' ): ?>
            <td><a href="<?php echo esc_url( add_query_arg( array( 'request_quote' => $order_id, 'status' => 'rejected', 'raq_nonce' => ywraq_get_token( 'reject-request-quote', $order_id, yit_get_prop( $order, 'ywraq_customer_email', true ) ) ), YITH_Request_Quote()->get_raq_page_url() ) ) ?>" class="pdf-button"><?php ywraq_get_label('reject', true) ?></a></td>
            <?php endif ?>
        </tr>
    </table>
</div>
<?php endif ?>
  
<?php do_action( 'yith_ywraq_email_after_raq_table', $order ); ?>

<?php if ( ( $after_list = yit_get_prop( $order, '_ywraq_request_response_after', true ) ) != '' ): ?>
    <div class="after-list">
        <p><?php echo apply_filters( 'ywraq_quote_after_list', nl2br( $after_list ), $order_id ) ?></p>
    </div>
<?php endif; ?>
