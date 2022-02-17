<?php

// custom child theme includes
include_once('includes/qode-breadcrumbs.php');
include_once('includes/class-portfolio.php');

include_once('includes/class-woocommerce.php');
include_once('includes/class-woocommerce-admin.php');

// include_once('includes/class-woocommerce-customers.php');
include_once('includes/class-woocommerce-customers-list.php');
include_once('includes/class-woocommerce-customers-table.php');
include_once('includes/class-woocommerce-customers-wishlist-table.php');

include_once('includes/class-yith-woocommerce-quotes.php');
include_once('includes/class-yith-woocommerce-wishlist.php');
// include_once('includes/class-yith-woocommerce-wishlist-admin.php');
include_once('includes/class-yith-woocommerce-wishlist-page-template.php');

define('QODE_CHILD_ROOT', get_stylesheet_directory_uri());
define( 'QODE_CHILD__FILE__', __FILE__ );

// Remove the recaptcha from the wp-admin login form
// remove_action('login_form',array('LoginNocaptcha', 'nocaptcha_form'));
// remove_filter('authenticate', array('LoginNocaptcha', 'authenticate'), 30, 3);

// enqueue the child theme stylesheet

function bridge_child_wp_enqueue_scripts() {
	wp_enqueue_style('select2', QODE_CHILD_ROOT . '/css/select2.min.css', array(), '1.0');
	// wp_register_style('childstyle', get_stylesheet_directory_uri() . '/style.css', array(), '1.0');
	// wp_enqueue_style('childstyle');
	wp_enqueue_style( 'bridge-childstyle', get_stylesheet_directory_uri() . '/style.css', '', wp_get_theme()->get('Version'), 'all' );

	wp_enqueue_script('qode_child', QODE_CHILD_ROOT . '/js/qode_child.js',array(),wp_get_theme()->get('Version'),true);

	wp_enqueue_script('gl-mods', QODE_CHILD_ROOT . '/js/gl-mods.js',array('jquery', 'wp-util', 'jquery-blockui'),wp_get_theme()->get('Version'),true);
	wp_localize_script( 'gl-mods', 'gl_mods_init', array(
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'ajaxnonce' => wp_create_nonce( 'gl_mods_init_nonce' )
    ) );

	if(is_page('login')) {
		wp_enqueue_script('gl-login', QODE_CHILD_ROOT . '/js/gl-login.js',array('jquery'),wp_get_theme()->get('Version'),true);
	}

	$current_post_posttype = get_post_type();
	if( $current_post_posttype== 'portfolio_page') {
		remove_shortcode('social_share');
		add_shortcode('social_share', 'social_share_list');
	}
}
add_action('wp_enqueue_scripts', 'bridge_child_wp_enqueue_scripts', 11);
// remove wp version number from scripts and styles
function remove_css_js_version( $src ) {

	global $wp_version;

    if( strpos( $src, '?ver=' . $wp_version ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'remove_css_js_version', 9999 );


/* Social Share shortcode */
if (!function_exists('social_share_list')) {
    function social_share_list($atts, $content = null) {
        global $bridge_qode_options;
        if(isset($bridge_qode_options['twitter_via']) && !empty($bridge_qode_options['twitter_via'])) {
            $twitter_via = " via " . $bridge_qode_options['twitter_via'] . " ";
        } else {
            $twitter_via = 	"";
        }
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $html = "";
        if(isset($bridge_qode_options['enable_social_share']) && $bridge_qode_options['enable_social_share'] == "yes") {
            $post_type = get_post_type();

            if(isset($bridge_qode_options["post_types_names_$post_type"])) {
                if($bridge_qode_options["post_types_names_$post_type"] == $post_type) {
                    $html .= '<div class="social_share_list_holder">';
                    $html .= "<span>".__('Share on: ', 'qode')."</span>";
                    $html .= '<ul>';

                    $is_mobile = (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
                        '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                        '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );

                    if(isset($bridge_qode_options['enable_facebook_share']) &&  $bridge_qode_options['enable_facebook_share'] == "yes") {
                        $html .= '<li class="facebook_share">';

                        // if mobile, use different link to sharer.php service
                        if($is_mobile) {
                            $html .= '<a title="'.__("Share on Facebook","qode").'" href="javascript:void(0)" onclick="window.open(\'http://m.facebook.com/sharer.php?u=' . urlencode(get_permalink());
                        }
                        else {
                            $html .= '<a title="'.__("Share on Facebook","qode").'" href="javascript:void(0)" onclick="window.open(\'http://www.facebook.com/sharer.php?u=' . urlencode(get_permalink());
                        }

                        $html .='\', \'sharer\', \'toolbar=0,status=0,width=620,height=280\');">';
                        if(!empty($bridge_qode_options['facebook_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options["facebook_icon"] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-facebook"></i>';
                        }
                        $html .= "</a>";
                        $html .= "</li>";
                    }

                    if($bridge_qode_options['enable_twitter_share'] == "yes") {
                        $html .= '<li class="twitter_share">';

                        // if mobile use different link to update status service
                        if($is_mobile) {
                            $html .= '<a href="#" title="'.__("Share on Twitter", 'qode').'" onclick="popUp=window.open(\'http://twitter.com/intent/tweet?text=' . urlencode(the_excerpt_max_charlength(mb_strlen(get_permalink())) . $twitter_via) . get_permalink() . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;">';
                        }
                        else {
                            $html .= '<a href="#" title="'.__("Share on Twitter", 'qode').'" onclick="popUp=window.open(\'http://twitter.com/home?status=' . urlencode(the_excerpt_max_charlength(mb_strlen(get_permalink())) . $twitter_via) . get_permalink() . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false;">';
                        }


                        if(!empty($bridge_qode_options['twitter_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options["twitter_icon"] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-twitter"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }
                    if($bridge_qode_options['enable_google_plus'] == "yes") {
                        $html .= '<li  class="google_share">';
                        $html .= '<a href="#" title="'.__("Share on Google+","qode").'" onclick="popUp=window.open(\'https://plus.google.com/share?url=' . urlencode(get_permalink()) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
                        if(!empty($bridge_qode_options['google_plus_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options['google_plus_icon'] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-google-plus"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }
                    if(isset($bridge_qode_options['enable_linkedin']) && $bridge_qode_options['enable_linkedin'] == "yes") {
                        $html .= '<li  class="linkedin_share">';
                        $html .= '<a href="#" class="'.__("Share on LinkedIn","qode").'" onclick="popUp=window.open(\'http://linkedin.com/shareArticle?mini=true&amp;url=' . urlencode(get_permalink()). '&amp;title=' . urlencode(get_the_title()) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
                        if(!empty($bridge_qode_options['linkedin_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options['linkedin_icon'] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-linkedin"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }
                    if(isset($bridge_qode_options['enable_tumblr']) && $bridge_qode_options['enable_tumblr'] == "yes") {
                        $html .= '<li  class="tumblr_share">';
                        $html .= '<a href="#" title="'.__("Share on Tumblr","qode").'" onclick="popUp=window.open(\'http://www.tumblr.com/share/link?url=' . urlencode(get_permalink()). '&amp;name=' . urlencode(get_the_title()) .'&amp;description='.urlencode(get_the_excerpt()) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
                        if(!empty($bridge_qode_options['tumblr_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options['tumblr_icon'] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-tumblr"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }
                    if(isset($bridge_qode_options['enable_pinterest']) && $bridge_qode_options['enable_pinterest'] == "yes") {
                        $html .= '<li  class="pinterest_share">';
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                        $html .= '<a href="#" title="'.__("Share on Pinterest","qode").'" onclick="popUp=window.open(\'http://pinterest.com/pin/create/button/?url=' . urlencode(get_permalink()). '&amp;description=' . qode_addslashes(get_the_title()) .'&amp;media='.urlencode($image[0]) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
                        if(!empty($bridge_qode_options['pinterest_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options['pinterest_icon'] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-pinterest"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }
                    if(isset($bridge_qode_options['enable_vk']) && $bridge_qode_options['enable_vk'] == "yes") {
                        $html .= '<li  class="vk_share">';
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                        $html .= '<a href="#" title="'.__("Share on VK","qode").'" onclick="popUp=window.open(\'http://vkontakte.ru/share.php?url=' . urlencode(get_permalink()). '&amp;title=' . urlencode(get_the_title()) .'&amp;description=' . urlencode(get_the_excerpt()) .'&amp;image='.urlencode($image[0]) . '\', \'popupwindow\', \'scrollbars=yes,width=800,height=400\');popUp.focus();return false">';
                        if(!empty($bridge_qode_options['vk_icon'])) {
                            $html .= '<img itemprop="image" src="' . $bridge_qode_options['vk_icon'] . '" alt="" />';
                        } else {
                            $html .= '<i class="fa fa-vk"></i>';
                        }

                        $html .= "</a>";
                        $html .= "</li>";
                    }

                    $html .= '</ul>'; //close ul
                    $html .= '</div>'; //close div.social_share_list_holder
                }
            }
        }
        return $html;
    }
}

if (!function_exists('the_excerpt_max_charlength')) {
	/**
	 * Function that sets character length for social share shortcode
	 * @param $charlength string original text
	 * @return string shortened text
	 */
	function the_excerpt_max_charlength($charlength) {
		global $bridge_qode_options;
		if(isset($bridge_qode_options['twitter_via']) && !empty($bridge_qode_options['twitter_via'])) {
			$via = " via " . $bridge_qode_options['twitter_via'] . " ";
		} else {
			$via = 	"";
		}
		$excerpt = get_the_excerpt();
		$charlength = 140 - (mb_strlen($via) + $charlength);

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength);
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut );
			} else {
				return $subex;
			}
		} else {
			return $excerpt;
		}
	}
}

if(!function_exists('qode_addslashes')) {
	/**
	 * Function that checks if magic quotes are turned on (for older versions of php) and returns escaped string
	 * @param $str string string to be escaped
	 * @return string escaped string
	 */
	function qode_addslashes($str) {
		
		$str = addslashes($str);
		
		return $str;
	}
}


if(!function_exists('bridge_child_get_custom_taxonomies_list')) {
	function bridge_child_get_custom_taxonomies_list($post_id, $taxonomy) {
		$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		$separator = '|';
		if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
		    $term_ids = implode( ',' , $post_terms );
		    $terms = wp_list_categories( array(
		        'title_li' => '',
		        'style'    => 'none',
		        'echo'     => false,
		        'taxonomy' => $taxonomy,
		        'include'  => $term_ids
		    ) );
			$terms = rtrim(trim(str_replace('<br />', $separator, $terms)), $separator);
		    $terms = explode($separator, $terms);
		    $terms = array_map('trim', $terms);
		    return $terms;
		}
		return null;
	}
}

if(!function_exists('bridge_child_generate_product_meta')) {
	function bridge_child_generate_product_meta($meta, $product, $separator = ', ') {
		if(function_exists('bridge_child_get_custom_taxonomies_list')) {
			foreach ($meta as $meta_row) {
				$taxonomy = isset($meta_row['taxonomy']) ? $meta_row['taxonomy'] : '';
				$class    = isset($meta_row['class'])    ? $meta_row['class']    : '';
				$label    = isset($meta_row['label'])    ? $meta_row['label']    : '';
				$comment  = isset($meta_row['comment'])  ? $meta_row['comment']  : '';

				$items = bridge_child_get_custom_taxonomies_list($product->get_id(), $taxonomy);
				if(!empty($items)) {
					echo $comment;
					echo '<span class="' . $class . '">' . $label . ' ';
				    echo implode($separator, $items);
				    echo '</span>';
				}
			}
		}
	}
}

// function to change the category title tag from h2 to h6
if(!function_exists('bridge_child_woocommerce_template_loop_category_title')) {
	remove_action('woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10);
	add_action('woocommerce_shop_loop_subcategory_title', 'bridge_child_woocommerce_template_loop_category_title', 10);
	/**
	 * Show the subcategory title in the product loop.
	 *
	 * @param object $category Category object.
	 */
	function bridge_child_woocommerce_template_loop_category_title($category) {
		?>
		<h6 class="woocommerce-loop-category__title">
			<?php
			echo esc_html($category->name);

			if ($category->count > 0) {
				echo apply_filters('woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html($category->count) . ')</mark>', $category); // WPCS: XSS ok.
			}
			?>
		</h6>
		<?php
	}
}

if(!function_exists('bridge_child_custom_taxonomy_columns')) {
	function bridge_child_custom_taxonomy_columns($columns) {
		$new_columns;
		if(function_exists('get_field')) {
			foreach ($columns as $key => $value) {
				$new_columns[$key] = $value;
				if($key == 'cb') {
					$new_columns['term_image'] = __('Image', 'gineicolighting');
				}
			}
		}
		return $new_columns;
	}
	add_filter('manage_edit-brands_columns' , 'bridge_child_custom_taxonomy_columns');
	add_filter('manage_edit-designers_columns' , 'bridge_child_custom_taxonomy_columns');
}

if(!function_exists('bridge_child_ywraq_change_paper_orientation')) {
	function bridge_child_ywraq_change_paper_orientation($orientation) {
		return 'landscape';
	}
	add_filter('ywraq_change_paper_orientation' , 'bridge_child_ywraq_change_paper_orientation');
}

if(!function_exists('bridge_child_custom_taxonomy_columns_content')) {
	function bridge_child_custom_taxonomy_columns_content($content, $column_name, $term_id) {
	    if(function_exists('get_field')) {
		    if('term_image' == $column_name) {
		    	$term = get_term($term_id);
		        $image = get_field('thumbnail', $term);
		        if(!empty($image)) {
		        	$content = wp_get_attachment_image($image['id'], array('40', '40'));//'<img src="">';
		        } else {
	                $content = wc_placeholder_img(array('40', '40'));
		        }
		    }
		}
		return $content;
	}
	add_filter('manage_brands_custom_column', 'bridge_child_custom_taxonomy_columns_content', 10, 3);
	add_filter('manage_designers_custom_column', 'bridge_child_custom_taxonomy_columns_content', 10, 3);
}

if ( ! function_exists( 'bridge_child_woocommerce_subcategory_thumbnail' ) ) {
	function bridge_child_woocommerce_subcategory_thumbnail( $category ) {
		$small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
		$dimensions           = wc_get_image_size( $small_thumbnail_size );
		if(function_exists('get_field')) {
			$image = get_field('thumbnail', $category);
			$thumbnail_id = $image['id'];
		} else {
			$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
		}

		if ( $thumbnail_id ) {
			$image        = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
			$image        = $image[0];
			$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false;
			$image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false;
		} else {
			$image        = wc_placeholder_img_src();
			$image_srcset = false;
			$image_sizes  = false;
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds.
			// Ref: https://core.trac.wordpress.org/ticket/23605.
			$image = str_replace( ' ', '%20', $image );

			// Add responsive image markup if available.
			if ( $image_srcset && $image_sizes ) {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
			} else {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
			}
		}
	}
}

if ( ! function_exists( 'bridge_child_product_custom_taxonomies' ) ) {
	function bridge_child_product_custom_taxonomies( $atts ) {
		$product_custom_taxonomies_array = array(
			'brands',
			'designers'
		);

		if ( isset( $atts['number'] ) ) {
			$atts['limit'] = $atts['number'];
		}

		$atts = shortcode_atts( array(
			'taxonomy'   => '',
			'limit'      => '-1',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '5',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
		), $atts, 'product_custom_taxonomies' );

		if(empty($atts['taxonomy']) or !in_array($atts['taxonomy'], $product_custom_taxonomies_array)) {
			return '';
		}

		$ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
		$hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
		);

		$product_taxonomies = get_terms( $atts['taxonomy'], $args );

		if ( '' !== $atts['parent'] ) {
			$product_taxonomies = wp_list_filter( $product_taxonomies, array(
				'parent' => $atts['parent'],
			) );
		}

		if ( $hide_empty ) {
			foreach ( $product_taxonomies as $key => $taxonomy ) {
				if ( 0 === $taxonomy->count ) {
					unset( $product_taxonomies[ $key ] );
				}
			}
		}

		$atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );
		if ( $atts['limit'] ) {
			$product_taxonomies = array_slice( $product_taxonomies, 0, $atts['limit'] );
		}

		$columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );
		wc_set_loop_prop( 'is_shortcode', true );

		ob_start();

		if ( $product_taxonomies ) {

			woocommerce_product_loop_start();

			foreach ( $product_taxonomies as $taxonomy ) {
				wc_get_template( 'content-product_custom_tax.php', array(
					'taxonomy' => $taxonomy,
				) );
			}

			woocommerce_product_loop_end();
		}

		woocommerce_reset_loop();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		add_shortcode('product_custom_taxonomies', 'bridge_child_product_custom_taxonomies');
	}
}

// if(!function_exists('bridge_child_yith_wcwl_add_to_wishlist')) {
// 	function bridge_child_yith_wcwl_add_to_wishlist() {
// 		echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
// 	}
// 	add_action( 'woocommerce_single_product_summary', 'bridge_child_yith_wcwl_add_to_wishlist', 25 );
// }

if(!function_exists('bridge_child_qode_title_text')) {
	function bridge_child_qode_title_text($title) {
		global $term, $taxonomy;
		$term_name = $term;
		$term_obj = get_term_by( 'slug', $term, $taxonomy, 'OBJECT' );
		if( !empty( $term_obj ) ) {
			$term_name = $term_obj->name;
		}
		if(is_tax()) {
			return $term_name;
		}
		return $title;
	}
	add_filter( 'qode_title_text', 'bridge_child_qode_title_text' );
}

/**
 * Allow HTML in term (category, tag) descriptions
 */
foreach ( array( 'pre_term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_filter_kses' );
}
 
foreach ( array( 'term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_kses_data' );
}

if(!function_exists('bridge_child_qode_woocommerce_show_page_title')) {
	function bridge_child_qode_woocommerce_show_page_title($show_page_title) {
		return false;
	}
	add_filter( 'woocommerce_show_page_title', 'bridge_child_qode_woocommerce_show_page_title' );
}

if(!function_exists('custom_woocommerce_content')) {
	function custom_woocommerce_content() {
		global $wp_query, $woocommerce, $taxonomy, $term;
		if( is_shop() and !is_search() ) {
			$shop_id = get_option('woocommerce_shop_page_id');
			$shop    = get_post($shop_id);
			echo apply_filters( 'the_content', $shop->post_content );
		} elseif(in_array($taxonomy, array('brands', 'designers'))) {
			// $term_obj = get_term_by( 'slug', $term, $taxonomy, 'OBJECT' );
			// if(!empty($term_obj->description)) {
			// 	echo '<div class="products-custom-tax-desc">';
			// 	echo apply_filters( 'the_content', $term_obj->description );
			// 	echo '</div>';
			// }
			woocommerce_content();
		} else {
			woocommerce_content();
		}
	}
}

if(!function_exists('bridge_child_is_woocommerce_page')) {
	function bridge_child_is_woocommerce_listing_page() {
		global $taxonomy;
		$taxonomies = get_object_taxonomies( 'product', 'object' );
		// var_dump($taxonomies[$taxonomy]);
		// if ( isset( $taxonomies[$taxonomy] ) ) {
		// }
		if(is_shop() or is_product_category() or in_array($taxonomy, array('brands', 'designers'))) {
			return true;
		}
		return false;
	}
}

if(!function_exists('qode_clean_body_classes')) {
	function qode_clean_body_classes($classes) {
		if (($key = array_search('qode-theme-bridge', $classes)) !== false) {
		    unset($classes[$key]);
		}
		return $classes;
	}
	add_filter('body_class','qode_clean_body_classes',20);
}

/**
 * Save user info
 */
if(function_exists('update_field')) {
	if(!function_exists('qode_save_user_custom_fields')) {
		function qode_save_user_custom_fields( $user_id ) {
		    if(isset($_POST['account_elect'])) {
		    	update_field('elect', sanitize_text_field($_POST['account_elect']), 'user_' . $user_id);
		    }
		    if(isset($_POST['account_first_name'])) {
		    	update_field('first_name', sanitize_text_field($_POST['account_first_name']), 'user_' . $user_id);
		    }
		    if(isset($_POST['account_last_name'])) {
		    	update_field('last_name', sanitize_text_field($_POST['account_last_name']), 'user_' . $user_id);
		    }
		    if(isset($_POST['account_company'])) {
		    	update_field('company', sanitize_text_field($_POST['account_company']), 'user_' . $user_id);
				update_user_meta( $user_id, 'billing_company', sanitize_text_field($_POST['account_company']) );
		    }
		    if(isset($_POST['account_phone_number'])) {
		    	update_field('phone_number', sanitize_text_field($_POST['account_phone_number']), 'user_' . $user_id);
				update_user_meta( $user_id, 'billing_phone', sanitize_text_field($_POST['account_phone_number']));
		    }
		    if(isset($_POST['account_state'])) {
		    	update_field('state', sanitize_text_field($_POST['account_state']), 'user_' . $user_id);

				$state_choices = qode_get_select_field_choices('acf_user-additional-information', 'state');
				foreach ($state_choices as $key => $value) {
					if($_POST['account_state'] == $key)
					update_user_meta( $user_id, 'billing_state', $value );
				}
		    }
		}
	}

	if(!function_exists('qode_action_woocommerce_save_account_details')) {
		function qode_action_woocommerce_save_account_details( $user_id ) {
			qode_save_user_custom_fields( $user_id );
		}
		add_action( 'woocommerce_save_account_details', 'qode_action_woocommerce_save_account_details' );
	}

	if(!function_exists('qode_action_woocommerce_created_customer')) {
		function qode_action_woocommerce_created_customer( $customer_id, $new_customer_data, $password_generated ) {
			qode_save_user_custom_fields( $customer_id );

			if(has_action('mailchimp_sync_subscribe_user') and isset($_POST['account_subscribe']) and $_POST['account_subscribe'] == 'yes') {
				do_action('mailchimp_sync_subscribe_user', $customer_id);
			}
		}
		add_action( 'woocommerce_created_customer', 'qode_action_woocommerce_created_customer', 10, 3 );
	}
}

if(!class_exists('QODE_Add_Settings_Fields')) {
	// Class for adding a new field to the options-general.php page
	class QODE_Add_Settings_Fields {
		// Class constructor
		public function __construct() {
			add_action( 'admin_init' , array( $this , 'register_fields' ) );
		}
		// Add new fields to wp-admin/options-general.php page
		public function register_fields() {
			register_setting( 'general', 'projects_listing_page', 'esc_attr' );
			register_setting( 'general', 'request_quote_column_items_count', 'esc_attr' );
			add_settings_field(
				'projects_listing_page',
				'<label for="projects_listing_page">Projects Listing Page</label>',
				array( $this, 'projects_listing_page_html' ),
				'general'
			);
			add_settings_field(
				'request_quote_column_items_count',
				'<label for="request_quote_column_items_count">Request Quote Form Column Items Count</label>',
				array( $this, 'request_quote_column_items_count_html' ),
				'general'
			);
		}
		// HTML for extra settings
		public function projects_listing_page_html() {
			$value = get_option( 'projects_listing_page', '' );
			?>
			<select name="projects_listing_page"> 
				<option value=""><?php echo esc_attr( __( 'Select page', 'gineicolighting' ) ); ?></option> 
				<?php 
					$pages = get_pages(); 
					foreach ( $pages as $page ) {
						$option = '<option value="' . $page->ID . '"' . selected( $value, $page->ID ) . '>';
						$option .= wp_trim_words( $page->post_title, 7, '...' );
						$option .= '</option>';
						echo $option;
					}
				?>
			</select>
			<?php
		}

		public function request_quote_column_items_count_html() {
			$value = get_option( 'request_quote_column_items_count', '' );
			?>
			<input type="text" name="request_quote_column_items_count" value="<?php echo esc_attr( $value ); ?>"> 
			<?php
		}
	}
	new QODE_Add_Settings_Fields();
}

if(!function_exists('qode_woocommerce_upsells_total')) {
	function qode_woocommerce_upsells_total( $limit ) {
	    return 8;
	}
	add_filter( 'woocommerce_upsells_total', 'qode_woocommerce_upsells_total' );
}

if(!function_exists('qode_woocommerce_upsells_orderby')) {
	function qode_woocommerce_upsells_orderby( $orderby ) {
	    return 'RAND';
	}
	add_filter( 'woocommerce_upsells_orderby', 'qode_woocommerce_upsells_orderby' );
}

if(!function_exists('qode_get_select_field_choices')) {
	function qode_get_select_field_choices( $group_slug, $field_name ) {
		global $wpdb;
		$field_key  = null;
		$acf_group  = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name LIKE %s" , $group_slug ) );
		if(!empty($acf_group)) {
			$acf_group_id = $acf_group->ID;
			$field_name_query = '%' . sprintf( 's:4:"name";s:%d:"%s";', strlen($field_name), $field_name ) . '%';
			$acf_field  = $wpdb->get_row( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_value LIKE %s AND post_id = %d" , $field_name_query, $acf_group_id ) );
			if(!empty($acf_field)) {
				$field_key = $acf_field->meta_key;
			}
		}
		if(!empty($field_key)) {
			$field = get_field_object($field_key);
			if(!empty($field['choices'])) {
				return $field['choices'];
			}
		}
		return array();
	}
}

if(!function_exists('qode_ywraq_get_default_form_field_first_name')) {
	function qode_ywraq_get_default_form_field_first_name( $value ) {
		$user_id = get_current_user_id();
	    $value = get_user_meta( $user_id, 'first_name', true );
	    return $value;
	}
	add_filter( 'ywraq_get_default_form_field_first_name', 'qode_ywraq_get_default_form_field_first_name' );
}

if(!function_exists('qode_ywraq_get_default_form_field_last_name')) {
	function qode_ywraq_get_default_form_field_last_name( $value ) {
		$user_id = get_current_user_id();
	    $value = get_user_meta( $user_id, 'last_name', true );
	    return $value;
	}
	add_filter( 'ywraq_get_default_form_field_last_name', 'qode_ywraq_get_default_form_field_last_name' );
}

if(!function_exists('qode_ywraq_get_default_form_field_company_name')) {
	function qode_ywraq_get_default_form_field_company_name( $value ) {
		$user_id = get_current_user_id();
		// get the woocommerce fields first
		$value       = get_user_meta( $user_id, 'billing_company', true );
		// get from the ACF account fields if the above are empty
		if($value == '') {
			$value       = get_user_meta( $user_id, 'company', true );
		}
	    return $value;
	}
	add_filter( 'ywraq_get_default_form_field_company_name', 'qode_ywraq_get_default_form_field_company_name' );
}

if(!function_exists('qode_ywraq_get_default_form_field_phone_number')) {
	function qode_ywraq_get_default_form_field_phone_number( $value ) {
		$user_id = get_current_user_id();
		// get the woocommerce fields first
		$value  = get_user_meta( $user_id, 'billing_phone', true );
		// get from the ACF account fields if the above are empty
		if($value == '') {
			$value  = get_user_meta( $user_id, 'phone_number', true );
		}
	    return $value;
	}
	add_filter( 'ywraq_get_default_form_field_phone_number', 'qode_ywraq_get_default_form_field_phone_number' );
}

if(!function_exists('qode_ywraq_get_default_form_field_email')) {
	function qode_ywraq_get_default_form_field_email( $value ) {
		$current_user = wp_get_current_user();
	    $value = $current_user->user_email;
	    return $value;
	}
	add_filter( 'ywraq_get_default_form_field_email', 'qode_ywraq_get_default_form_field_email' );
}

/**
 * Add the resend quote action to order actions select box on edit order page
 * Only added for Pending Quote orders
 *
 * @param array $actions order actions array to display
 * @return array - updated actions
 */
if(!function_exists('qode_wc_add_action_to_order_actions_box')) {
	function qode_wc_add_action_to_order_actions_box($actions) {
	    global $theorder;
	    $order_data = $theorder->get_data();
	    if($order_data['status'] != 'ywraq-pending') {
	        return $actions;
	    }
	    $actions['wc_resend_quote_email_action'] = __('Resend Quote Email', 'gineicolighting');
	    return $actions;
	}
	add_action('woocommerce_order_actions', 'qode_wc_add_action_to_order_actions_box');
}

/**
 * Add an order note when quote resend
 *
 * @param \WC_Order $order
 */
if(!function_exists('qode_wc_resend_quote_email_handler')) {
	function qode_wc_resend_quote_email_handler($order) {
	    $message = sprintf(__('Quote details email resent by %s.', 'gineicolighting'), wp_get_current_user()->display_name);
	    $order->add_order_note($message);

		$mailer = WC()->mailer();
		$mails = $mailer->get_emails();
		if(!empty($mails)) {
		    foreach($mails as $mail) {
		        if($mail->id == 'ywraq_send_quote') {
		        	$mail->trigger($order->get_id());
		        }
		    }
		}
	}
	add_action('woocommerce_order_action_wc_resend_quote_email_action', 'qode_wc_resend_quote_email_handler' );
}

if ( ! function_exists( 'bridge_child_product_advanced_search' ) ) {
	function bridge_child_product_advanced_search( $atts ) {
		// get filter selected values
		$gineico_filter = isset($_GET['gineico-filter']) ? true : false;
		$search_text = isset($_GET['gineico-s']) ? esc_attr($_GET['gineico-s']) : '';
		$search_area = isset($_GET['area']) ? $_GET['area'] : array();
		$search_area = array_map('esc_attr', $search_area);
		$search_brand = isset($_GET['brand']) ? $_GET['brand'] : array();
		$search_brand = array_map('esc_attr', $search_brand);
		$search_ip_rating = isset($_GET['ip_rating']) ? esc_attr($_GET['ip_rating']) : '';
		$search_surface = isset($_GET['surface']) ? esc_attr($_GET['surface']) : '';
		$search_colour = isset($_GET['colour']) ? $_GET['colour'] : array();
		$search_colour = array_map('esc_attr', $search_colour);
		$search_colour_temperature = isset($_GET['colour_temperature']) ? $_GET['colour_temperature'] : array();
		$search_colour_temperature = array_map('esc_attr', $search_colour_temperature);
		$search_cri = isset($_GET['cri']) ? $_GET['cri'] : array();
		$search_cri = array_map('esc_attr', $search_cri);
		$search_format = isset($_GET['format']) ? esc_attr($_GET['format']) : '';
		$search_maximum_depth = isset($_GET['maximum_depth']) ? esc_attr($_GET['maximum_depth']) : '';
		$search_beam_angle = isset($_GET['beam_angle']) ? $_GET['beam_angle'] : array();
		$search_beam_angle = array_map('esc_attr', $search_beam_angle);
		$search_control_protocol = isset($_GET['control_protocol']) ? $_GET['control_protocol'] : array();
		$search_control_protocol = array_map('esc_attr', $search_control_protocol);

		if($gineico_filter) {
			$tax_query  = array();
			$meta_query = array();
			$per_page = 20;
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args = array(
				's' => $search_text,
				'post_type' => 'product',
				'posts_per_page' => $per_page,
				'paged' => $paged
				);
			if(isset($search_area) and !empty($search_area)) {
				$tax_query[] = array(
									'taxonomy' => 'product_cat',
									'field' => 'slug',
									'terms' => $search_area,
								);
			}
			if(isset($search_brand) and !empty($search_brand)) {
				$tax_query[] = array(
									'taxonomy' => 'brands',
									'field' => 'slug',
									'terms' => $search_brand,
								);
			}
			if(isset($search_ip_rating) and !empty($search_ip_rating)) {
				$tax_query[] = array(
									'taxonomy' => 'ip_rating',
									'field' => 'slug',
									'terms' => $search_ip_rating,
								);
			}
			if(isset($search_surface) and !empty($search_surface)) {
				$tax_query[] = array(
									'taxonomy' => 'surface',
									'field' => 'slug',
									'terms' => $search_surface,
								);
			}
			if(isset($search_colour) and !empty($search_colour)) {
				foreach ($search_colour as $colour_value) {
					$meta_query[] = array(
										'key'     => '_product_attributes',
										'value'   => '"' . $colour_value . '"',
										'compare' => 'LIKE',
									);
					$meta_query[] = array(
										'key'     => '_product_attributes',
										'value'   => '| ' . $colour_value . '"',
										'compare' => 'LIKE',
									);
					$meta_query[] = array(
										'key'     => '_product_attributes',
										'value'   => '"' . $colour_value . ' |',
										'compare' => 'LIKE',
									);
					$meta_query[] = array(
										'key'     => '_product_attributes',
										'value'   => '| ' . $colour_value . ' |',
										'compare' => 'LIKE',
									);
				}
			}
			if(isset($search_colour_temperature) and !empty($search_colour_temperature)) {
				$tax_query[] = array(
									'taxonomy' => 'colour_temperature',
									'field' => 'slug',
									'terms' => $search_colour_temperature,
								);
			}
			if(isset($search_cri) and !empty($search_cri)) {
				$tax_query[] = array(
									'taxonomy' => 'cri',
									'field' => 'slug',
									'terms' => $search_cri,
								);
			}
			if(isset($search_format) and !empty($search_format)) {
				$tax_query[] = array(
									'taxonomy' => 'format',
									'field' => 'slug',
									'terms' => $search_format,
								);
			}
			if(isset($search_maximum_depth) and !empty($search_maximum_depth)) {
				$tax_query[] = array(
									'taxonomy' => 'maximum_depth',
									'field' => 'slug',
									'terms' => $search_maximum_depth,
								);
			}
			if(isset($search_beam_angle) and !empty($search_beam_angle)) {
				$tax_query[] = array(
									'taxonomy' => 'beam_angle',
									'field' => 'slug',
									'terms' => $search_beam_angle,
								);
			}
			if(isset($search_control_protocol) and !empty($search_control_protocol)) {
				$tax_query[] = array(
									'taxonomy' => 'control_protocol',
									'field' => 'slug',
									'terms' => $search_control_protocol,
								);
			}
			if(count($tax_query) > 1) {
				$tax_query['relation'] = 'AND';
			}/* elseif(count($tax_query) <= 0 and empty($search_text)) {
				$tax_query[] = array(
									'taxonomy' => 'product_cat',
									'field' => 'slug',
									'terms' => '',
								);
			}*/
			if(count($meta_query) > 1) {
				$meta_query['relation'] = 'OR';
			}
			$args['tax_query']  = $tax_query;
			$args['meta_query'] = $meta_query;
			// print_r($args);
			$loop = new WP_Query($args);
			// echo $loop->request;
			$total_products = $loop->found_posts;
			wc_set_loop_prop('is_paginated', true);
			wc_set_loop_prop('total', $total_products);
			wc_set_loop_prop('per_page', $per_page);
			wc_set_loop_prop('total_pages', ceil($total_products / $per_page));
			wc_set_loop_prop('current_page', $paged);
		}
		
		wc_get_template_part('gineico-advanced-filter');

		if($gineico_filter) {
			?>
			<div id="search-results" class="woocommerce columns-4">
				<?php woocommerce_result_count(); ?>
				<ul class="products">
					<?php
						if ( $loop->have_posts() ) {
							while ( $loop->have_posts() ) : $loop->the_post();
								wc_get_template_part( 'content', 'product' );
							endwhile;
						} else {
							echo '<p class="woocommerce-info">' . __('No products were found matching your selection.', 'gineicolighting') . '</p>';
						}
						wp_reset_postdata();
					?>
				</ul><!--/.products-->
			</div>
			<?php
		}
		do_action( 'woocommerce_after_shop_loop' );
	}
	add_shortcode('product_advanced_search', 'bridge_child_product_advanced_search');
}

function bridge_child_query_clauses($pieces, $query) {
    global $wpdb;
	$gineico_filter = isset($_GET['gineico-filter']) ? true : false;
	$search_text    = isset($query->query_vars['s']) ? $query->query_vars['s'] : false;
    if($query->query_vars['post_type'] == 'product' and $gineico_filter and isset($search_text) and !empty($search_text)) {
		$search_ids = array();
		$terms = explode(',', $query->query_vars['s']);
		foreach ($terms as $term) {
	        $sku_to_parent_id = $wpdb->get_col($wpdb->prepare("SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->postmeta} pm on p.ID = pm.post_id and pm.meta_key='_sku' and pm.meta_value LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean($term)));
	        $sku_to_id        = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value LIKE '%%%s%%';", wc_clean($term)));
	        $search_ids       = array_merge($search_ids, $sku_to_id, $sku_to_parent_id);
		}
		$search_ids = array_filter(array_map('absint', $search_ids));
		if(!empty($search_ids) and strpos($pieces['where'], "{$wpdb->posts}.post_title LIKE") !== false) {
			$pieces['where'] = str_replace("{$wpdb->posts}.post_title LIKE", "{$wpdb->posts}.ID IN (" . implode(',', $search_ids) . ")) OR ({$wpdb->posts}.post_title LIKE", $pieces['where']);
		}
    }
    return $pieces;
}
add_filter('posts_clauses', 'bridge_child_query_clauses', 10, 2);


if ( ! function_exists( 'bridge_child_product_searchform' ) ) {
	/**
	 * bridge_child_product_searchform
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */
	function bridge_child_product_searchform($form) {
	    $form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/advanced-search/'  ) ) . '">
			<div>
				<label class="screen-reader-text" for="gineico-s">' . __( 'Search for:', 'gineicolighting' ) . '</label>
				<input type="text" value="' . get_search_query() . '" name="gineico-s" id="gineico-s" placeholder="' . __( 'Search Products (Name / SKU)', 'gineicolighting' ) . '" />
                <a href="#" class="search-options">
                    <span aria-hidden="true" class="qode_icon_font_elegant icon_adjust-horiz"></span>
                </a>
				<input type="submit" id="searchsubmit" value="&#xf002" />
				<input type="hidden" name="gineico-filter" value="1">
			</div>
		</form>';
	    return $form;
	}

	function remove_filter_get_product_search_form() {
		remove_filter('get_product_search_form' , 'woo_qode_product_searchform');
	}
	add_action('init','remove_filter_get_product_search_form');
	add_filter('get_product_search_form' , 'bridge_child_product_searchform');
}

if ( ! function_exists( 'bridge_child_wp_footer' ) ) {
	function bridge_child_wp_footer() {
		ob_start();
		if(function_exists('wc_get_template_part')) {
			wc_get_template_part('gineico-advanced-filter');
			$gineico_advanced_filter = ob_get_contents();
			ob_end_clean();
			echo '<div id="float-gineico-advanced-filter">';
			echo '<div class="float-gineico-advanced-filter-container">';
			echo '<span class="float-gineico-advanced-filter-close qode_icon_font_elegant icon_close"></span>';
			echo $gineico_advanced_filter;
			echo '</div>';
			echo '</div>';
		}
	}
	add_action( 'wp_footer', 'bridge_child_wp_footer' );
}

if ( ! function_exists( 'bridge_child_woocommerce_email_footer' ) ) {
	function bridge_child_woocommerce_email_footer($email) {
		switch ($email->id) {
			case 'customer_new_account':
			case 'customer_reset_password':
				?>
				<p>&nbsp;</p>
				<p style="font-style: italic; font-size: 12px;">
					To make the most of our website and your schedule creations -
					<ul>
						<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Make sure to give each project a name to differentiate your lists.</li>
						<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Schedules can be made private or public and can be changed at any time by clicking the “manage” button at the bottom of your schedule list.</li>
						<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Schedules can be printed as a PDF to take to meetings.</li>
						<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">You can add and delete products within your schedule at any time.</li>
						<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">You can request a price estimate on products from within your schedule by simply clicking the button.</li>
					</ul>
				</p>
				<?php
				break;
			
			default:
				echo '<p>&nbsp;</p>';
				bridge_child_woocommerce_quote_email_pdf_footer($email);
				break;
		}
	}
	add_action( 'woocommerce_email_footer', 'bridge_child_woocommerce_email_footer' );
}

if ( ! function_exists( 'bridge_child_woocommerce_quote_email_pdf_footer' ) ) {
	function bridge_child_woocommerce_quote_email_pdf_footer($order_id) {
		?>
		<div class="gineico-pdf-footer" style="margin-top: 100px; font-style: italic; font-size: 12px;">
		<p style="font-style: italic; font-size: 12px;">
			PLEASE TAKE NOTE OF ALL THE CONDITIONS OF THIS QUOTE AS STATED BELOW, BEFORE PLACING AN ORDER</p>
			<ol>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Until fully Paid, goods remain the sole property of Gineico Queensland Pty Ltd.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Unless otherwise specified, indicated costs are unit costs.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Prices are quoted not including G.S.T</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Prices are quoted for goods ex our store. Delivery charges will apply if goods are required to be on-forwarded.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Unless otherwise stated, standard manufacturing lead time is aproximately 4-5 weeks from confirmation of order (not including holiday closures). The goods are then ready for collection from the manufacturer in Italy.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Standard prices are based on sea freight from Italy to Australia. Transit time for sea freight is aproximately 6-7 weeks from date goods are ready / collected from the manufacturers warehouse in Italy (see above).</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Express air freight option is available at additional cost. This reduces transit time to aproximately 7 working days from date goods are ready / collected from the manufacturers warehouse in Italy (see above).</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Terms of sale: 50% deposit with written order. Balance in full prior to consignment.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Balance of payment and collection of goods, to take place within 7 calendar days from date when goods become available from our warehouse.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Failure to pay and collect goods by the stated time may incur storage costs or the forfeit of the deposit and goods.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Payments made by cheque require clearance of funds prior to goods being released.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">This offer is valid for 30 calendar days from date of issue.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Quantities indicated above are to be checked by purchaser prior to ordering. Reduction of the indicated quantities will be cause for revision of quoted prices.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Restocking fee of 50% applies to all items returned. Items can only be returned with prior written consent by gineico QLD Pty Ltd. Goods to be returned in "as new condition" at client's expense.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Custom or non standard / stock hardware cannot be returned.</li>
				<li style="font-style: italic; margin-bottom: 5px; font-size: 12px;">Clients should take care to download the product specific data sheets, or to request technical information in writing, to ensure all items ordered are in every way compatible for each specific intended application.</li>
			</ol>
		</div>
		<?php
	}
	add_action( 'yith_ywraq_quote_template_after_content', 'bridge_child_woocommerce_quote_email_pdf_footer' );
	add_action( 'woocommerce_order_details_after_order_table', 'bridge_child_woocommerce_quote_email_pdf_footer' );
}


if ( ! function_exists( 'bridge_child_woocommerce_email_footer_quote_conditions_plain' ) ) {
	function bridge_child_woocommerce_email_footer_quote_conditions_plain($email) {
		switch ($email->id) {
			case 'customer_new_account':
			case 'customer_reset_password':
				echo sprintf( __( 'To make the most of our website and your schedule creations -', 'woocommerce' ) ) . "\n\n";
				echo sprintf( __( '- Make sure to give each project a name to differentiate your lists.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '- Schedules can be made private or public and can be changed at any time by clicking the “manage” button at the bottom of your schedule list.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '- Schedules can be printed as a PDF to take to meetings.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '- You can add and delete products within your schedule at any time.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '- You can request a price estimate on products from within your schedule by simply clicking the button.', 'woocommerce' ) ) . "\n\n";
				break;
			
			default:
				echo sprintf( __( 'PLEASE TAKE NOTE OF ALL THE CONDITIONS OF THIS QUOTE AS STATED BELOW, BEFORE PLACING AN ORDER', 'woocommerce' ) ) . "\n\n";
				echo sprintf( __( '1. Until fully Paid, goods remain the sole property of Gineico Queensland Pty Ltd.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '2. Unless otherwise specified, indicated costs are unit costs.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '3. Prices are quoted not including G.S.T', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '4. Prices are quoted for goods ex our store. Delivery charges will apply if goods are required to be on-forwarded.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '5. Unless otherwise stated, standard manufacturing lead time is aproximately 4-5 weeks from confirmation of order (not including holiday closures). The goods are then ready for collection from the manufacturer in Italy.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '6. Standard prices are based on sea freight from Italy to Australia. Transit time for sea freight is aproximately 6-7 weeks from date goods are ready / collected from the manufacturers warehouse in Italy (see above).', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '7. Express air freight option is available at additional cost. This reduces transit time to aproximately 7 working days from date goods are ready / collected from the manufacturers warehouse in Italy (see above).', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '8. Terms of sale: 50% deposit with written order. Balance in full prior to consignment.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '9. Balance of payment and collection of goods, to take place within 7 calendar days from date when goods become available from our warehouse.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '10. Failure to pay and collect goods by the stated time may incur storage costs or the forfeit of the deposit and goods.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '11. Payments made by cheque require clearance of funds prior to goods being released.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '12. This offer is valid for 30 calendar days from date of issue.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '13. Quantities indicated above are to be checked by purchaser prior to ordering. Reduction of the indicated quantities will be cause for revision of quoted prices.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '14. Restocking fee of 50% applies to all items returned. Items can only be returned with prior written consent by gineico QLD Pty Ltd. Goods to be returned in "as new condition" at client\'s expense.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '15. Custom or non standard / stock hardware cannot be returned.', 'woocommerce' ) ) . "\n";
				echo sprintf( __( '16. Clients should take care to download the product specific data sheets, or to request technical information in writing, to ensure all items ordered are in every way compatible for each specific intended application.', 'woocommerce' ) ) . "\n\n";
				break;
		}

		echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
	}
	add_action( 'woocommerce_email_footer_quote_conditions_plain', 'bridge_child_woocommerce_email_footer_quote_conditions_plain' );
}

// if ( ! function_exists( 'bridge_child_ywraq_pdf_file_name' ) ) {
// 	function bridge_child_ywraq_pdf_file_name($pdf_file_name, $order_id) {
// 		$order = wc_get_order($order_id);
// 		$customer_lastname   = yit_get_prop( $order, '_billing_last_name', true );

// 		if($customer_lastname == null || $customer_lastname == '') {
// 			$user_name = yit_get_prop( $order, 'ywraq_customer_name', true );
// 			$user_name_parts = explode(" ", $user_name);
// 			$customer_lastname = array_pop($user_name_parts);
// 		}
// 		// $temp_order_date_modified = yit_get_prop($order, '_temp_order_date_modified', true);
// 		// $order_date_modified = get_the_modified_date('U', $order_id);
// 		$order_date = yit_get_prop($order, 'date_created', true);
// 		$order_date = substr($order_date, 0, 10);
// 		$order_date = str_replace('-', '_', $order_date);
// 		$pdf_file_name = 'Quote-' . $order_id . '-' . $order_date;
// 		$YITH_Request_Quote = YITH_Request_Quote_Premium();
// 		$path = $YITH_Request_Quote->create_storing_folder($order_id);
// 		$file = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '.pdf';
// 		// yit_save_prop($order, '_temp_order_date_modified', $order_date_modified);
// 		if(file_exists($file)) {
// 			$new_pdf_file_name = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '-' . $customer_lastname . '.pdf';
// 			rename($file, $new_pdf_file_name);
// 			return $pdf_file_name . '-' . $customer_lastname . '.pdf';
// 		}
// 		// if(intval($temp_order_date_modified) != intval($order_date_modified)) {
// 		// 	$file = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '-' . $temp_order_date_modified . '-'. $customer_lastname . '.pdf';
// 		// 	if(file_exists($file)) {
// 		// 		$new_pdf_file_name = YITH_YWRAQ_DOCUMENT_SAVE_DIR . $path . $pdf_file_name . '-' . $order_date_modified . '-'. $customer_lastname . '.pdf';
// 		// 		rename($file, $new_pdf_file_name);
// 		// 		return $pdf_file_name . '-' . $order_date_modified . '-'. $customer_lastname . '.pdf';
// 		// 	}
// 		// }
// 		return $pdf_file_name . '-' . $customer_lastname . '.pdf';
// 	}
// 	add_filter( 'ywraq_pdf_file_name', 'bridge_child_ywraq_pdf_file_name', 10, 2 );
// }

if ( ! function_exists( 'bridge_child_woocommerce_get_order_item_totals' ) ) {
	function bridge_child_woocommerce_get_order_item_totals($total_rows, $obj, $tax_display) {
		if(isset($total_rows['order_total'])) {
			$total_rows['order_total']['label'] = __( 'Total Ex GST:', 'woocommerce' );
		}
		return $total_rows;
	}
	add_filter( 'woocommerce_get_order_item_totals', 'bridge_child_woocommerce_get_order_item_totals', 10, 3 );
}

if ( ! function_exists( 'bridge_child_wc_display_item_meta' ) ) {
	/**
	 * Display item meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Order_Item $item Order Item.
	 * @param  array         $args Arguments.
	 * @return string|void
	 */
	function bridge_child_wc_display_item_meta( $item, $args = array() ) {
		$strings = array();
		$html    = '';
		$args    = wp_parse_args( $args, array(
			'before'    => '<ul class="wc-item-meta"><li>',
			'after'     => '</li></ul>',
			'separator' => '</li><li>',
			'echo'      => true,
			'autop'     => false,
		) );

		foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
			$value     = $args['autop'] ? wp_kses_post( $meta->value ) : wp_kses_post( make_clickable( trim( $meta->value ) ) );
			$strings[] = '<strong class="wc-item-meta-label">' . wp_kses_post( $meta->display_key ) . ':</strong> ' . $value;
			// $strings[] = $meta->display_key . ': ' . trim($meta->value);
			// var_dump($meta);
		}

		if ( $strings ) {
			$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}

		$html = apply_filters( 'woocommerce_display_item_meta', $html, $item, $args );

		if ( $args['echo'] ) {
			echo $html; // WPCS: XSS ok.
		} else {
			return $html;
		}
	}
}

$colours = array(
	'White',
	'Black',
	'Grey',
	'Red',
	'Orange',
	'Purple',
	'Yellow',
	'Green',
	'Blue',
	'Cement',
	'Moka',
	'Gold',
	'Brass',
	'Bronze',
	'Copper',
	'Nickel',
	'Cor-ten',
	'Silver',
	'Chrome',
	'Stainless Steel',
	'Graphite',
);
if ( ! function_exists( 'bridge_child_get_products_colours' ) ) {
	function bridge_child_get_products_colours() {
		global $colours;
		// global $wpdb;
		// $colours = array();
		// $query_var_values = array();
		// $query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_product_attributes' AND meta_value LIKE '%colour%'";
		// $results = $wpdb->get_results($wpdb->prepare($query, $query_var_values));
		// foreach ($results as $key => $product_attributes) {
		// 	$product_attributes = maybe_unserialize($product_attributes->meta_value);
		// 	if(isset($product_attributes['colour']) and isset($product_attributes['colour']['value'])) {
		// 		$product_attributes = explode('|', $product_attributes['colour']['value']);
		// 		$product_attributes = array_map('trim', $product_attributes);
		// 	} else {
		// 		continue;
		// 	}
		// 	$colours = array_unique(array_merge($colours, $product_attributes));
		// }
			// 'White',
			// 'Black',
			// 'Gold',
			// 'Moka',
			// 'Graphite',
			// 'Chrome',
			// 'Stainless Steel',
			// 'Bronze',
			// 'Copper',
			// 'Burnished Brass',
			// 'Cor-ten',
			// 'Grey',
			// 'Cement',
			// 'Red',
			// 'Nickel',
		return $colours;
	}
}

if (!function_exists('bridge_child_get_meta_sql')) {
	function bridge_child_get_meta_sql($array) {
		$search_colour = isset($_GET['colour']) ? $_GET['colour'] : array();
		$search_colour = array_map('esc_attr', $search_colour);
		if(isset($search_colour) and !empty($search_colour)) {
			foreach ($array as $key => $value) {
				foreach ($search_colour as $colour_value) {
					if(strpos($value, $colour_value)) {
						$array[$key] = str_replace($colour_value, '%' . $colour_value . '%', $value);
					}
				}
			}
		}
	    return $array; 
	}; 
	add_filter('get_meta_sql', 'bridge_child_get_meta_sql', 10, 1);
}

/* Add Download PDF button to single product pages */
// add_action( 'woocommerce_single_product_summary', 'dl_after_add_to_cart_download_pdf',39 );
 
// function dl_after_add_to_cart_download_pdf(){

// 	global $product;

// 	if(function_exists('get_field')) {
// 		echo '<!-- Product Attached PDF Files -->';
// 		$product_details = get_field('product_details', $product->get_id());
// 		$installation_instructions = get_field('installation_instructions', $product->get_id());
// 		$photometry = get_field('photometry', $product->get_id());
// 		$dwg_file   = get_field('dwg_file', $product->get_id());
// 		if(!empty($product_details) or !empty($installation_instructions) or !empty($photometry) or !empty($dwg_file)) {
// 			// prepare product details link
// 			$product_details_alt       = $product_details['alt'];
// 			$product_details_url       = $product_details['url'];
// 			$product_details_link_text = get_field('product_details_link_text', $product->get_id());
// 			$product_details_link_obj  = get_field_object('product_details_link_text', $product->get_id());
// 			$product_details_title     = $product_details_link_obj['default_value'];
// 			if(!empty($product_details_link_text)) {
// 				$product_details_title = $product_details_link_text;
// 			}
// 			echo '<div class="product-attached-pdf-buttons" style="margin: 0 0 20px;">';

// 			if(!empty($product_details)) {
// 				echo '<p><a class="button" alt="' . $product_details_alt . '" href="' . $product_details_url . '">Download: ' . $product_details_title . '</a></p>';
// 			}
// 			echo '</div>';
// 		}
// 	}
// }


/**
 * Remove Price from structured schema data
 */
	 

 /**
 * Remove partial product structured data.
 */
// add_filter( 'woocommerce_structured_data_product_offer', 'dst_remove_partial_product_structured_data', 10, 2 );

// function dst_remove_partial_product_structured_data( $markup_offer, $product ) {
// 	$markup_offer = array(
// 		'availability'  => 'https://schema.org/' . ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ),
// 		'url'           => get_permalink( $product->get_id() ),
// 		'seller'        => array(
// 			'@type' => 'Organization',
// 			'name'  => get_bloginfo( 'name' ),
// 			'url'   => home_url(),
// 		),
// 	);

// 	return $markup_offer;
// }

/*
 * Add the product image to the emails
 */

add_filter( 'woocommerce_email_order_items_args', 'dst_email_order_items_args', 10, 1 );
 
function dst_email_order_items_args( $args ) {
 
	$args['show_image'] = true;
	$args['image_size'] = array( 70, 70 );
 
    return $args;
 
}



function wl ( $log ) {
	if ( is_array( $log ) || is_object( $log ) ) {
	error_log( print_r( $log, true ) );
	} else {
	error_log( $log );
	}
}

/**
* Logging function to a file path specified
*/
function wf( $contents, $filename = '' ) {

	if($filename == '') {
		$filename = 'myfile_'.date('m-d-Y_hia');
	}
	$uploads = wp_upload_dir();
	$upload_path = $uploads['path'];
	$file = $upload_path . '/' . $filename;
	// Open the file to get existing content
	$current = file_get_contents($file);
	// Append a new person to the file
	$current .= $contents;
	// Write the contents back to the file
	file_put_contents($file, $current);
}

    /**
     * Modify WooCommerce breadcrumb delimiters
     */
    function gm_woocommerce_set_breadcrumbs( $defaults ) {
        // Change the breadcrumb delimeter from '/' to '>'
        $defaults['delimiter'] = ' &gt; ';
        return $defaults;
    }