<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; line-height: 1.3em; color: #232323;">
   <tbody>
      <tr style="font-family: Arial, sans-serif; line-height: 1.3em;">
         <td width="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; padding: 0 !important;">
            <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; line-height: 1.3em; color: #232323;">
               <tbody>
                  <tr height="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; height: 50%;">
                     <td style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; padding: 0 !important;">&nbsp;</td>
                  </tr>
                  <tr height="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; height: 50%;">
                     <td class="header_content_h2_border" style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; border-top: 2px solid #282828; padding: 0 !important;"></td>
                  </tr>
               </tbody>
            </table>
         </td>
         <td width="1%" class="header_content_h2" style="line-height: 1.3em; font-family: Arial,sans-serif; font-weight: bold; font-style: none; font-size: 14px; color: #232323; text-decoration: none; text-transform: uppercase; margin: 0; padding: 0px 5px; white-space: nowrap; padding-right: 6px; padding-left: 6px;"> Order&nbsp;Details </td>
         <td width="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; padding: 0 !important;">
            <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; line-height: 1.3em; color: #232323;">
               <tbody>
                  <tr height="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; height: 50%;">
                     <td style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; padding: 0 !important;">&nbsp;</td>
                  </tr>
                  <tr height="50%" style="font-family: Arial, sans-serif; line-height: 1.3em; height: 50%;">
                     <td class="header_content_h2_border" style="font-family: Arial, sans-serif; line-height: 1.3em; font-size: 1px; border-top: 2px solid #282828; padding: 0 !important;"></td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>

<?php 
$text_align = is_rtl() ? 'right' : 'left';

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2>
	<?php
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	// echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	?>
</h2>
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; line-height: 1.3em; color: #232323; margin-bottom: 15px;">
   <tbody>
      <tr style="font-family: Arial, sans-serif; line-height: 1.3em;">
         <td class="order-table-heading" style="font-family: Arial, sans-serif; line-height: 1.3em; padding: 0 0 6px; text-align: left;"> <span class="highlight" style="color: #242e4a; text-decoration: none; font-style: none;"> Order Number: </span> <?php echo $order->get_id(); ?> </td>
         <td class="order-table-heading" style="font-family: Arial, sans-serif; line-height: 1.3em; padding: 0 0 6px; text-align: right;"> <span class="highlight" style="color: #242e4a; text-decoration: none; font-style: none;"> Order Date: </span>  <?php echo $order->get_date_created()->format ('F d, Y'); ?> </td>
      </tr>
   </tbody>
</table>
<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>

				<?php 
				$text_align_original = $text_align;
				$text_align = 'right'; ?>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<?php $text_align = $text_align_original; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$order,
				array(
					'show_sku'      => $sent_to_admin,
					'show_image'    => false,
					'image_size'    => array( 32, 32 ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
				)
			);
			?>
		</tbody>
		<tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();
			if ( $item_totals ) {

				$bottom_table_array = array();
				foreach ( $item_totals as $key => $total ) {
					
					ob_start();
					if($key == 'shipping') {
						$selected_shipping_methods = array();
						foreach( $order->get_shipping_methods() as $shipping_method ) {
							if($shipping_method->get_total() == 0) {
								$amount = '&#8211;';
							} else {
								$amount = wc_price( $shipping_method->get_total(), get_woocommerce_currency_symbol() );
							}
							$selected_shipping_methods[] = array(
								'name' => $shipping_method->get_name(),
								'amount' => $amount
							);
						}
						$shipping_method_count = count($selected_shipping_methods);

							$j = 1;
							foreach($selected_shipping_methods as $this_shipping_method) {
								?>
								
								<?php
								if($j == 1) {
									?>
									<tr>
										<th class="td" scope="col" colspan="3" style="text-align: left; border: 1px solid #e5e5e5; border-left: none;">Freight:</th>
									</tr>

									<?php
									}
										// end if
							?>
								<tr>
									<td class="td" scope="col" colspan="2" style="text-align:left; padding-left: 40px;"><?php echo $this_shipping_method['name']; ?></td>
									<td class="td" scope="col" style="text-align: right;"><?php echo  $this_shipping_method['amount']; ?></td>
								</tr>
							<?php
								$j++;
								} // end foreach
							?>
	
						<?php 
						
					} else {
	
						?>
						<tr>
							<th class="td" scope="col" colspan="2" style="text-align:left;"><?php echo $total['label']; ?></th>
							<td class="td" scope="col" style="text-align: right;"><?php echo $total['value']; ?></td>
						</tr>
						<?php 
					}
				  
					$bottom_table_array[$key] = ob_get_clean();

				} 
				echo $bottom_table_array['cart_subtotal'];
				echo $bottom_table_array['shipping'];
				echo $bottom_table_array['order_total'];
	
				$order_gst = round((floatval($order->get_total()) * .1), 2);
				$order_total_with_gst = floatval($order_gst) + floatval($order->get_total());
				?>
				<tr>
					<th class="td" scope="col" colspan="2" style="text-align: left;">GST:</th>
					<td class="td" scope="col" style="text-align: right;"<?php echo wc_price( $order_gst, get_woocommerce_currency_symbol()); ?></td>
				</tr>
	
				<tr>
					<th class="td" scope="col" colspan="2" style="text-align: left;">TOTAL:</th>
					<td class="td" scope="col" style="text-align: right;"><?php echo wc_price( $order_total_with_gst, get_woocommerce_currency_symbol()); ?></td>
				</tr>
				<?php
			}
			if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			}
			?>
		</tfoot>
	</table>
</div>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
