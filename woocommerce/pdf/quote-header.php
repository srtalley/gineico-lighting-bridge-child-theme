<?php
/**
 * Request Quote PDF Header
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 */

/**
 * @var $order    WC_Order
 * @var $raq_data array
 */

if ( function_exists( 'icl_get_languages' ) ) {
	global $sitepress;
	$lang = yit_get_prop( $order, 'wpml_language', true );
	if ( function_exists( 'wc_switch_to_site_locale' ) ) {
		wc_switch_to_site_locale();
	}
	YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}

$order_id = yit_get_prop( $order, 'id', true );
$logo_url = get_option( 'ywraq_pdf_logo' );

$logo_attachment_id = apply_filters( 'yith_pdf_logo_id', get_option( 'ywraq_pdf_logo-yith-attachment-id' ) );
if ( ! $logo_attachment_id && $logo_url ) {
	$logo_attachment_id = attachment_url_to_postid( $logo_url );
}

$logo = $logo_attachment_id ? get_attached_file( $logo_attachment_id ) : $logo_url;

$image_type = wp_check_filetype( $logo );
$mime_type  = array( 'image/jpeg', 'image/png' );
$logo       = apply_filters( 'ywraq_pdf_logo', ( isset( $image_type['type'] ) && in_array( $image_type['type'], $mime_type ) ) ? $logo : '' );

$user_name         = yit_get_prop( $order, 'ywraq_customer_name', true );
$user_email        = yit_get_prop( $order, 'ywraq_customer_email', true );
$formatted_address = $order->get_formatted_billing_address();

$billing_phone   = yit_get_prop( $order, 'ywraq_billing_phone', true );
$billing_name    = yit_get_prop( $order, '_billing_first_name', true );
$billing_surname = yit_get_prop( $order, '_billing_last_name', true );
$billing_phone   = yit_get_prop( $order, 'ywraq_billing_phone', true );
$billing_phone   = empty( $billing_phone ) ? yit_get_prop( $order, '_billing_phone', true ) : $billing_phone;
$billing_vat     = yit_get_prop( $order, 'ywraq_billing_vat', true );

/* GL CUSTOM */
$title_order_date   = date_i18n('d/m/Y', strtotime(yit_get_prop($order, 'date_created', true)));
/* END GL CUSTOM */

$exdata          = yit_get_prop( $order, '_ywcm_request_expire', true );
$expiration_data = '';

if ( function_exists( 'wc_format_datetime' ) ) {
	$order_date = wc_format_datetime( $order->get_date_created() );
	if ( ! empty( $exdata ) ) {
		$exdata          = new WC_DateTime( $exdata, new DateTimeZone( 'UTC' ) );
		$expiration_data = wc_format_datetime( $exdata );
	}
} else {
	$date_format     = isset( $raq_data['lang'] ) ? ywraq_get_date_format( $raq_data['lang'] ) : wc_date_format();
	$order_date      = date_i18n( $date_format, strtotime( yit_get_prop( $order, 'date_created', true ) ) );
	$expiration_data = empty( $exdata ) ? '' : date_i18n( $date_format, strtotime( $exdata ) );
}

?>
<div class="logo">
	<img src="<?php echo esc_url( $logo ); ?>" style="max-width: 300px; max-height: 80px;">
</div>
<div class="admin_info right">
	<div class="admin_info_part_left">
		<table>
			<tr>
				<td valign="top" class="small-title"><?php echo __( 'From', 'yith-woocommerce-request-a-quote' ) ?></td>
				<td valign="top" class="small-info">
					<p><?php echo apply_filters( 'ywraq_pdf_info', nl2br( get_option( 'ywraq_pdf_info' ) ), $order ) ?></p>
				</td>
			</tr>
		</table>
	</div>
	<div class="admin_info_part_right">
		<table>
			<tr>
				<td valign="top" class="small-title"><?php echo __( 'Customer', 'yith-woocommerce-request-a-quote' ) ?></td>
				<td valign="top" class="small-info">
	                <p>
						<?php if(isset($additional_email_fields['Company Name']) && !empty($additional_email_fields['Company Name'])) {
	                        echo esc_attr($additional_email_fields['Company Name']) . '<br>';
						} ?>
						
						<?php if ( empty( $billing_name ) && empty( $billing_surname ) ): ?>
	                        <strong><?php echo $user_name ?></strong>
	                        <br>
						<?php endif; ?>

						<?php
						echo $formatted_address .'<br>';
						echo $user_email . '<br>';

						if ( $billing_phone != '' ) {
							echo $billing_phone . '<br>';
						}

						if ( $billing_vat != '' ) {
							echo $billing_vat . '<br>';
						} ?>
						
						<?php if(isset($additional_email_fields['Phone Number']) && !empty($additional_email_fields['Phone Number'])) {
	                        echo esc_attr($additional_email_fields['Phone Number']) . '<br>';
						} ?>
	                </p>
				</td>
			</tr>
			<?php if ( $expiration_data != '' ): ?>
				<tr>
					<td valign="top" class="small-title"><?php echo __( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></td>
					<td valign="top" class="small-info">
						<p><strong><?php echo $expiration_data ?></strong></p>
					</td>
				</tr>
			<?php endif ?>
		</table>
	</div>
</div>
<div class="clear"></div>
<div class="quote-title" style="margin: 30px 0 0;">
	<h4 style="margin: 0 0 5px 0;"><?php printf( __( 'Quote #%s', 'yith-woocommerce-request-a-quote' ), apply_filters( 'ywraq_quote_number', $order_id ) ) ?> - <?php echo $title_order_date; ?></h4>
	<?php if((isset($additional_email_fields['Project Name']) && !empty($additional_email_fields['Project Name'])) or (isset($additional_email_fields['Project Address']) && !empty($additional_email_fields['Project Address']))) {
        echo '<h5 style="margin: 0;">';
        if(!empty($additional_email_fields['Project Name'])) {
	        echo 'Project Name: <span style="font-weight: normal">' . esc_attr($additional_email_fields['Project Name']) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if(!empty($additional_email_fields['Project Address'])) {
	        echo 'Project Address: <span style="font-weight: normal">' . esc_attr($additional_email_fields['Project Address']) . '</span>';
        }
        echo '</h5>';
	} ?>
	<?php if(isset($additional_email_fields['Required Delivery Date']) && !empty($additional_email_fields['Required Delivery Date'])) {
        echo '<h5 style="margin: 0;">Required Delivery Date: ';
        echo '<span style="font-weight: normal">' . esc_attr($additional_email_fields['Required Delivery Date']) . '</span> ';
        echo '</h5>';
	} ?>
</div>