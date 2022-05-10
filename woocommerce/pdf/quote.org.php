<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$pdf_font = apply_filters('pdf_font_family', '"dejavu sans"');
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <style type="text/css">

        @page { margin: 0.45in 0.45in 0.35in 0.35in; }

        body {
            color: #000;
            font-family: <?php echo $pdf_font ?>;
            margin: 0;
            padding: 0;
        }
        .logo{
            width: 100%;
            float: left;
            max-width: 300px;
        }
        .right{
            float: right;
            width: 60%;
            text-align: right;
        }
        .clear{
            clear: both;
        }
        .admin_info{
            font-size: 12px;
        }

        table{
            border: 0;
        }
        table.quote-table{
            border: 0;
            font-size: 14px;
        }

        .small-title{
            text-align: right;
            font-weight: 600;
            color: #4e4e4e;
            padding-top: 5px;
            padding-right: 5px;
        }
        .admin_info_part_left {
            width: 50%;
            float: left;
        }
        .admin_info_part_right {
            width: 50%;
            float: right;
        }
        .small-info p{
            border-left: 2px solid #a8c6e4;
            padding: 0 0 5px 5px;
            margin: 0; 
        }
        .quote-table td{
            border: 0;
            border-bottom: 1px solid #eee;
        }
        .quote-table .with-border td{
            border-bottom: 2px solid #eee;
        }
        .quote-table .with-border td{
            border-top: 2px solid #eee;
        }
        .quote-table .quote-total td{
            height: 100px;
            vertical-align: middle;
            font-size: 18px;
            border-bottom: 0;
        }
        .quote-table .type-col {
            width: 1in;
        }
        .quote-table .qty-col {
            width: 0.3in;
        }
        .quote-table .part-num-col {
            width: 1in;
        }
        .quote-table .image-col {
            width: 0.7in;
        }
        .quote-table .unit-col {
            width: 1in;
        }
        .quote-table .subtotal-col {
            width: 1.1in;
        }
        .quote-table small{
            font-size: 13px;
        }
        .quote-table .last-col{
            padding-right: .1in;
        }
        .quote-table .last-col.tot{
            font-weight: 600;
        }
        .quote-table .tr-wb{
            border-left: 1px solid #ccc ;
            border-right: 1px solid #ccc ;
        }
        .pdf-button{
            color: #a8c6e4;
            text-decoration: none;
        }
        div.content{ padding-bottom: 0px; border-bottom: 1px; }
		.wc-item-meta {
			margin: 5px 0;
		}
		.wc-item-meta li strong, 
		.wc-item-meta li p {
			display: inline-block;
			margin: 0 !important;
		}
        .shipping-col .shipped_via {
            display: none;
        }
        .footer {
            position: fixed;
            bottom: .05in;
            text-align: center;
            font-size: 70%;
            width: 100%;
            margin-top: 0px;
        }
		.gl-last-page-footer {
			font-size: 13px;
            width: 100%;
            text-align: center;
			position: absolute;
			bottom: -.25in;
		}
        .pagenum:before {
            content: counter(page);
        }
        .part-no-col {
            font-size: 12px;
        }
        .gineico-pdf-footer {
            margin-top: 25px !important;
            font-size: 13px !important;
        }
    </style>
	<?php

	do_action( 'yith_ywraq_quote_template_head' );
	?>
</head>

<body>
<?php
do_action( 'yith_ywraq_quote_template_footer', $order_id );
?>

<?php
do_action( 'yith_ywraq_quote_template_header', $order_id );
?>
<div class="content">
	<?php
	do_action( 'yith_ywraq_quote_template_content', $order_id );
    do_action( 'yith_ywraq_quote_template_after_content', $order_id );
	?>
</div>
<div class="gl-last-page-footer">
    <p>Thank you for the opportunity to quote on your lighting selections. Contact Us <a href="tel:+61-417-950-455" style="text-decoration: none; color: #e2ae68; font-weight: bold;">+61 417 950 455</a> © Gineico Lighting | <a href="https://www.gineicolighting.com.au" target="_blank"style="text-decoration: none; color: #e2ae68; font-weight: bold;">www.gineicolighting.com.au</a></p>
</div>
</body>
</html>
