<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
//GL
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$separator = ', ';
?>
<div class="product_meta_container">
	<div class="product_meta">
		<?php do_action( 'woocommerce_product_meta_start' ); ?>

		<?php
			if(function_exists('bridge_child_get_custom_taxonomies_list')) {
				echo '<!-- Product Brands -->';
				$taxonomy = 'brands';
				$brands = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				if(!empty($brands)) {
					echo '<span class="product-brands">' . _n('Brand:', 'Brands:', count($brands), 'woocommerce') . ' ';
				    echo implode($separator, $brands);
				    echo '</span>';
				}

				echo '<!-- Product Designers -->';
				$taxonomy = 'designers';
				$designers = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				if(!empty($designers)) {
					echo '<span class="product-designers">' . _n('Designer:', 'Designers:', count($designers), 'woocommerce') . ' ';
				    echo implode($separator, $designers);
				    echo '</span>';
				}
			}

			if(function_exists('get_field')) {
				echo '<!-- Product Attached PDF Files -->';
				$product_details = get_field('product_details', $product->get_id());
				$installation_instructions = get_field('installation_instructions', $product->get_id());
				$photometry = get_field('photometry', $product->get_id());
				$dwg_file = get_field('dwg_file', $product->get_id());
				$ldt_ies_file = get_field('ldt_ies_file', $product->get_id());
				if(!empty($product_details) or !empty($installation_instructions) or !empty($photometry) or !empty($dwg_file) or !empty($dxf_file) or !empty($ldt_ies_file)) {
					// prepare product details link
					if(!empty($product_details)) {
						$product_details_alt       = $product_details['alt'];
						$product_details_url       = $product_details['url'];
						$product_details_link_text = get_field('product_details_link_text', $product->get_id());
						$product_details_link_obj  = get_field_object('product_details_link_text', $product->get_id());
						$product_details_title     = $product_details_link_obj['default_value'];
						if(!empty($product_details_link_text)) {
							$product_details_title = $product_details_link_text;
						}
					}
					// prepare installation instructions link
					if(!empty($installation_instructions)) {
						$installation_instructions_alt       = $installation_instructions['alt'];
						$installation_instructions_url       = $installation_instructions['url'];
						$installation_instructions_link_text = get_field('installation_instructions_link_text', $product->get_id());
						$installation_instructions_link_obj  = get_field_object('installation_instructions_link_text', $product->get_id());
						$installation_instructions_title     = $installation_instructions_link_obj['default_value'];
						if(!empty($installation_instructions_link_text)) {
							$installation_instructions_title = $installation_instructions_link_text;
						}
					}
					// prepare photometry link
					if(!empty($photometry)) {
						$photometry_alt       = $photometry['alt'];
						$photometry_url       = $photometry['url'];
						$photometry_link_text = get_field('photometry_link_text', $product->get_id());
						$photometry_link_obj  = get_field_object('photometry_link_text', $product->get_id());
						$photometry_title     = $photometry_link_obj['default_value'];
						if(!empty($photometry_link_text)) {
							$photometry_title = $photometry_link_text;
						}
					}
					// prepare dwg link
					if(!empty($dwg_file)) {
						$dwg_file_alt       = $dwg_file['alt'];
						$dwg_file_url       = $dwg_file['url'];
						$dwg_file_link_text = get_field('dwg_file_link_text', $product->get_id());
						$dwg_file_link_obj  = get_field_object('dwg_file_link_text', $product->get_id());
						$dwg_file_title     = $dwg_file_link_obj['default_value'];
						if(!empty($dwg_file_link_text)) {
							$dwg_file_title = $dwg_file_link_text;
						}
					}
					// prepare ldt_ies link
					if(!empty($ldt_ies_file)) {
						$ldt_ies_file_alt       = $ldt_ies_file['alt'];
						$ldt_ies_file_url       = $ldt_ies_file['url'];
						$ldt_ies_file_link_text = get_field('ldt_ies_file_link_text', $product->get_id());
						$ldt_ies_file_link_obj  = get_field_object('ldt_ies_file_link_text', $product->get_id());

						$ldt_ies_file_title     = $ldt_ies_file_link_obj['default_value'];
						if(!empty($ldt_ies_file_link_text)) {
							$ldt_ies_file_title = $ldt_ies_file_link_text;
						}
					}
					echo '<div class="product-attached-pdf">';
					echo '<div class="product-attached-pdf-label">Downloads: </div>';
					echo '<div class="product-attached-pdf-files">';
					if(!empty($product_details)) {
						echo '<p><a alt="' . $product_details_alt . '" href="' . $product_details_url . '" target="_blank">' . $product_details_title . '</a></p>';
					}
					if(!empty($installation_instructions)) {
						echo '<p><a alt="' . $installation_instructions_alt . '" href="' . $installation_instructions_url . '" target="_blank">' . $installation_instructions_title . '</a></p>';
					}
					if(!empty($photometry)) {
						echo '<p><a alt="' . $photometry_alt . '" href="' . $photometry_url . '" target="_blank">' . $photometry_title . '</a></p>';
					}
					if(!empty($dwg_file)) {
						echo '<p><a alt="' . $dwg_file_alt . '" href="' . $dwg_file_url . '" target="_blank">' . $dwg_file_title . '</a></p>';
					}
					if(!empty($ldt_ies_file)) {
						echo '<p><a alt="' . $ldt_ies_file_alt . '" href="' . $ldt_ies_file_url . '" target="_blank">' . $ldt_ies_file_title . '</a></p>';
					}
					echo '</div>';
					echo '</div>';
				}
			}
		?>

		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

			<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

		<?php endif; ?>

		<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

		<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

		<?php do_action( 'woocommerce_product_meta_end' ); ?>
	</div>
	<div class="product_meta">
		<?php
			$categories = get_the_terms ( $product->get_id(), 'product_cat' );
			$area_args = array(
				'separator' => ' ',
				'inclusive' => false,
			);
			$areas = array();
			foreach ($categories as $cat_key => $cat) {
				$temp  = get_ancestors($cat->term_id, 'product_cat', 'taxonomy');
				$areas = array_merge($areas, $temp);
				// echo get_term_parents_list($cat->term_id, 'product_cat', $area_args);
			}
			if(!empty($areas)) {
				$areas = array_unique($areas);
				echo '<span class="product-area">' . esc_html__( 'AREA:', 'woocommerce' ) . ' ';
				foreach ($areas as $area_term_id) {
					$area_term = get_term($area_term_id, 'product_cat');
					echo '<a href="' . esc_url(get_term_link($area_term->term_id, 'product_cat')) . '">' . $area_term->name . '</a> ';
				}
				echo '</span>';
			}

			if(function_exists('bridge_child_generate_product_meta')) {
				// echo '<!-- Product Surface -->';
				// $taxonomy = 'surface';
				// $surface = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				// if(!empty($surface)) {
				// 	echo '<span class="product-surface">' . __('Surface:', 'woocommerce') . ' ';
				//     echo implode($separator, $surface);
				//     echo '</span>';
				// }

				// echo '<!-- Product Colour Temperature -->';
				// $taxonomy = 'colour_temperature';
				// $colour_temperature = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				// if(!empty($colour_temperature)) {
				// 	echo '<span class="product-colour-temperature">' . __('Colour Temperature:', 'woocommerce') . ' ';
				//     echo implode($separator, $colour_temperature);
				//     echo '</span>';
				// }

				// echo '<!-- Product IP Rating -->';
				// $taxonomy = 'ip_rating';
				// $ip_rating = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				// if(!empty($ip_rating)) {
				// 	echo '<span class="product-ip-rating">' . __('IP Rating:', 'woocommerce') . ' ';
				//     echo implode($separator, $ip_rating);
				//     echo '</span>';
				// }

				// echo '<!-- Product Maximum Depth -->';
				// $taxonomy = 'maximum_depth';
				// $maximum_depth = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				// if(!empty($maximum_depth)) {
				// 	echo '<span class="product-maximum-depth">' . __('Maximum Depth:', 'woocommerce') . ' ';
				//     echo implode($separator, $maximum_depth);
				//     echo '</span>';
				// }

				$product_meta = array(
					array(
						'taxonomy' => 'surface',
						'class'    => 'product-surface',
						'label'    => __('Surface:', 'woocommerce'),
						'comment'  => '<!-- Product Surface -->',
					),
					array(
						'taxonomy' => 'colour_temperature',
						'class'    => 'product-colour-temperature',
						'label'    => __('Colour Temperature:', 'woocommerce'),
						'comment'  => '<!-- Product Colour Temperature -->',
					),
					array(
						'taxonomy' => 'ip_rating',
						'class'    => 'product-ip-rating',
						'label'    => __('IP Rating:', 'woocommerce'),
						'comment'  => '<!-- Product IP Rating -->',
					),
					array(
						'taxonomy' => 'maximum_depth',
						'class'    => 'product-maximum-depth',
						'label'    => __('Maximum Depth:', 'woocommerce'),
						'comment'  => '<!-- Product Maximum Depth -->',
					),
					array(
						'taxonomy' => 'beam_angle',
						'class'    => 'product-beam-angle',
						'label'    => __('Beam Angle:', 'woocommerce'),
						'comment'  => '<!-- Product Beam Angle -->',
					),
					array(
						'taxonomy' => 'format',
						'class'    => 'product-format',
						'label'    => __('Format:', 'woocommerce'),
						'comment'  => '<!-- Product Format -->',
					),
					array(
						'taxonomy' => 'control_protocol',
						'class'    => 'control-protocol',
						'label'    => __('Control Protocol:', 'woocommerce'),
						'comment'  => '<!-- Product Control Protocol -->',
					),
					array(
						'taxonomy' => 'cri',
						'class'    => 'cri',
						'label'    => __('CRI:', 'woocommerce'),
						'comment'  => '<!-- Product CRI -->',
					),
				);

				bridge_child_generate_product_meta($product_meta, $product, $separator);
			}
		?>
	</div>
</div>

<?php
if(function_exists('magictoolbox_WooCommerce_MagicZoom_init')) {
	wc_get_template_part('single-product/product-images-lightbox-links');
}
?>