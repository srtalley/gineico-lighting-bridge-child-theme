<?php
/**
 * Wishlist page template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist              \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items        array Array of items to show for current page
 * @var $page_title            string Page title
 * @var $show_price            bool Whether to show price column
 * @var $show_dateadded        bool Whether to show item date of addition
 * @var $show_stock_status     bool Whether to show product stock status
 * @var $show_price_variations bool Whether to show price variation over time
 * @var $show_variation        bool Whether to show variation attributes when possible
 * @var $show_quantity         bool Whether to show input quantity or not
 * @var $css_url               string Url to css file
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

// copied from the Request a Quote template
$pdf_font = apply_filters( 'pdf_font_family', '"dejavu sans"' );
$logo_url = get_option( 'ywraq_pdf_logo' );

$logo_attachment_id = apply_filters( 'yith_pdf_logo_id', get_option( 'ywraq_pdf_logo-yith-attachment-id' ) );


if ( ! $logo_attachment_id && $logo_url ) {
	$logo_attachment_id = attachment_url_to_postid( $logo_url );
}

$logo = $logo_attachment_id ? get_attached_file( $logo_attachment_id ) : $logo_url;


$image_type        = wp_check_filetype( $logo );
$mime_type         = array( 'image/jpeg', 'image/png' );
$logo              = apply_filters( 'ywraq_pdf_logo', ( isset( $image_type['type'] ) && in_array( $image_type['type'], $mime_type, true ) ) ? $logo : '' );
$show_quantity = false;
$show_dateadded = false;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title><?php echo esc_html( $page_title ); ?></title>

	<link rel="stylesheet" href="<?php echo esc_url( $css_url ); ?>"/>
	<style type="text/css">
        body {
            color: #000;
            font-family: <?php echo $pdf_font ?> !important;
        }
		a, p a {
			color: #e2ae68;
			text-decoration: none;
		}
        .logo{
            width: 100%;
            float: left;
            max-width: 300px;
        }
        .clear{
            clear: both;
        }
        .admin_info{
            font-size: 12px;
        }
		.wishlist-title h2 {
			margin-bottom: 0;
			text-align: left;
    		font-size: 18px;
			margin: 15px 5px 0;
		}
        table{
            border: 0;
			width: 10.75in !important;
			border-collapse: collapse;
		}

        table.shop_table {
            border: 0;
            font-size: 14px;
        }
		table.wishlist_table tr th {
			color: #fff !important;
			border-bottom: 1px solid #818181;
			padding: 0;
			font-size: 5px;
			line-height: 5px;
		}
		/** SRT */
        .small-title p {
            margin-bottom: 0;
        }
        .admin_info_part_left {
            width: 50%;
            float: left;
        }
        .admin_info_part_right {
            width: 50%;
            float: right;
			text-align: right;
        }
        .small-title p {
			margin: 0 0 5px;
        }
		.small-title .title {
			font-size: 16px;
			font-weight: bold;
		}
		table.wishlist_table th {
			/* border-top: 1px solid #818181 */
		}
		table.wishlist_table tr td {
			border-top: 1px solid #818181;
		}
		table.wishlist_table tr:last-child td {
			border-bottom: 1px solid #818181;
		}
		/* table.shop_table tr th:first-child, */
		table.shop_table tr td:first-child {
			border-left: 1px solid #818181;
		}

		/** SRT */
		/* table.shop_table tr th:last-child, */
		table.shop_table tr td:last-child {
			border-right: 1px solid #818181;
        }
		table.shop_table .product-thumbnail {
			padding: 10px;
			width: 80px;
		}
		table.shop_table .product-thumbnail a {
			display: block !important;
			max-width: 80px !important;
		}
		table.shop_table .product-name {
			padding: 5px;
		}
		table.shop_table .product-name a {
			font-weight: 700;
			font-size: 14px;
		}
		table.shop_table .product-name .product-description,
        table.shop_table .product-name .product-description a {
			font-size: 14px;
			font-weight: 400;
			line-height: 16px;
		}
		.variation-description {
			margin: 5px 0 0;
		}
		dl.variation {
			margin: 0;
		}
		dl.variation dd {
			margin: 0;
		}
		dl.variation dd::after {
			content: '\A';
			white-space: pre-line;
		}
		dl.variation dd:last-of-type::after {
			content: '';
		}
		dl.variation dd, dl.variation dt {
			display: inline;
		}
		dl.variation dd, dl.variation dt {
			vertical-align: top;
		}
		dl.variation dt {
			font-weight: bolder;
			margin: 0 6px 0 0;
		}

		/* This style footer appears on every page*/
        .gl-ywcwl-footer  {
			/* page-break-after: always; */
			font-size: 14px;
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: .25in;
            margin-top: 0px;
        }
		/* This style footer appears only on the last page*/
		.gl-last-page-footer  {
			font-size: 14px;
            width: 100%;
            text-align: center;
			position: absolute;
			bottom: -.25in;
		}
      
    </style>
</head>

<body>
<!-- <div class="heading">
	<div id="logo">
		<h1><?php //echo esc_html( get_option( 'blogname' ) ); ?></h1>
	</div>
	<div id="tagline"><?php //echo esc_html( get_option( 'blogdescription' ) ); ?></div>
</div> -->


<div class="logo">
	<img src="<?php echo esc_url( $logo ); ?>" style="max-width: 300px; max-height: 80px;">
</div>
<div class="admin_info right">
	<div class="admin_info_part_left">
	</div>
	<div class="admin_info_part_right">
		<div class="small-title">
			<p class="title">Gineico Lighting</p>
			<p><a href="https://www.gineicolighting.com.au" target="_blank">www.gineicolighting.com.au</a></p>
			<p><a href="mailto:showroom@gineico.com" target="_blank">showroom@gineico.com</a></p>
			<p><a href="tel:+61-417-950-455">+61 417 950 455</a></p>
		</div>
	</div>
</div>
<div class="clear"></div>
<!-- <div class="gl-ywcwl-footer">
	<p>Contact Us <a href="tel:+61-417-950-455">+61 417 950 455</a> © Gineico Lighting | <a href="https://www.gineicolighting.com.au" target="_blank">www.gineicolighting.com.au</a></p>
</div> -->

<!-- TITLE -->
<?php
do_action( 'yith_wcwl_pdf_before_wishlist_title', $wishlist );
if ( ! empty( $page_title ) ) :
	?>
	<div class="wishlist-title">
		<?php 
		if($page_title == 'My Favourites') {
			echo '<h2>My Favourites</h2>';
		} else {
			echo '<h2>My Project &#8211; ' . $page_title . '</h2>';
		}
		?>
		<?php //echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>PROJECT: ' . esc_html( $page_title ) . '</h2>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
<?php
endif;

do_action( 'yith_wcwl_pdf_before_wishlist', $wishlist );
?>

<!-- WISHLIST TABLE -->
<table class="shop_table cart wishlist_table">

	<?php $column_count = 2; ?>

	<thead>
	<tr>

		<th class="product-thumbnail"></th>

		<th class="product-name">
			<span class="nobr">
				<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_name_heading', __( 'Product Name', 'yith-woocommerce-wishlist' ) ) ); ?>
			</span>
		</th>

		<?php
		if ( $show_price || $show_price_variations ) :
			$column_count ++;
		?>
			<th class="product-price">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_price_heading', __( 'Unit Price', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_quantity ) :
			$column_count ++;
		?>
			<th class="product-quantity">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_quantity_heading', __( 'Quantity', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_stock_status ) :
			$column_count ++;
		?>
			<th class="product-stock-status">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_stock_heading', __( 'Stock status', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_dateadded ) :
			$column_count ++;
		?>
			<th class="product-add-to-cart"></th>
		<?php endif; ?>
	</tr>
	</thead>

	<tbody>
	<?php
	if ( count( $wishlist_items ) > 0 ) :
		foreach ( $wishlist_items as $item ) :
			/**
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product      = $item->get_product();
			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;

			if ( $product && $product->exists() ) :
				?>
				<tr id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">

					<td class="product-thumbnail">
						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
							<?php echo YITH_WCWL_Frontend()->get_product_image_with_path( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</td>

					<td class="product-name">
						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?></a>

						<?php
						echo '<div class="product-description">';
						$product_id  = '';
						$variation_description = '';
						$main_product = $product;
						if ( $show_variation && $product->is_type( 'variation' ) ) {
							/**
							 * @var $product \WC_Product_Variation
							 */

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

						echo strip_tags( substr($product_short_description, 0 , 200)) . '&hellip; <a style="text-decoration: none; color: #e2ae68;" target="_blank" href="' . esc_url( $product->get_permalink() ) . '">Read More</a>';
						echo '</div>';
						echo $variation_description;

						echo wc_get_formatted_variation( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						echo '<div class="gl-ywcyl-qty">QTY: ' . esc_html( $item->get_quantity() ) . '</div>';

						echo '<div class="gl-ywcyl-dateadded"><span class="dateadded">' . esc_html( sprintf( __( 'Added on: %s', 'yith-woocommerce-wishlist' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) ) . '</span></div>';
						?>

						<?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>
					</td>

					<?php if ( $show_price || $show_price_variations ) : ?>
						<td class="product-price">
							<?php
							if ( $show_price ) {
								echo $item->get_formatted_product_price(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							if ( $show_price_variations ) {
								echo $item->get_price_variation(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</td>
					<?php endif ?>

					<?php if ( $show_quantity ) : ?>
						<td class="product-quantity">
							<?php echo esc_html( $item->get_quantity() ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $show_stock_status ) : ?>
						<td class="product-stock-status">
							<?php echo $stock_status === 'out-of-stock' ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of stock', 'yith-woocommerce-wishlist' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'yith-woocommerce-wishlist' ) . '</span>'; ?>
						</td>
					<?php endif ?>

					<?php if ( $show_dateadded ): ?>
						<td class="product-add-to-cart">
							<!-- Date added -->
							<?php
							if ( $show_dateadded && isset( $item['dateadded'] ) ):
								echo '<span class="dateadded">' . esc_html( sprintf( __( 'Added on: %s', 'yith-woocommerce-wishlist' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) ) . '</span>';
							endif;
							?>
						</td>
					<?php endif; ?>
				</tr>
			<?php
			endif;
		endforeach;
	else: ?>
		<tr>
			<td colspan="<?php echo esc_attr( $column_count ) ?>" class="wishlist-empty"><?php echo esc_html( apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products added to the wishlist', 'yith-woocommerce-wishlist' ) ) ); ?></td>
		</tr>
	<?php endif; ?>
	</tbody>

</table>

<?php do_action( 'yith_wcwl_pdf_after_wishlist', $wishlist ); ?>
<div class="gl-last-page-footer">
	<p>Contact Us <a href="tel:+61-417-950-455">+61 417 950 455</a> © Gineico Lighting | <a href="https://www.gineicolighting.com.au" target="_blank">www.gineicolighting.com.au</a></p>
</div>
</body>
</html>
