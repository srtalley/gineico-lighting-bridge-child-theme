<?php

global $product;

$post_thumbnail_id = intval($product->get_image_id());

$attachment_ids = array_merge(array($post_thumbnail_id), $product->get_gallery_image_ids());

if ( $attachment_ids && has_post_thumbnail() ) {
	foreach ( $attachment_ids as $attachment_id ) {
		// echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id  ), $attachment_id );
		echo '<a id="product-image-' . $attachment_id . '" class="product-image-light-box" href="' . wp_get_attachment_url( $attachment_id ) . '" data-rel="prettyPhoto[woo_single_pretty_photo]" rel="prettyPhoto[woo_single_pretty_photo]"></a>';
	}
}
