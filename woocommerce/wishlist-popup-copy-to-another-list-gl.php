<?php
/**
 * Wishlist move popup
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist					  \YITH_WCWL_Wishlist Wishlist
 * @var $move_to_another_wishlist_url string Url to use as action for wishlist form
 * @var $users_wishlists              \YITH_WCWL_Wishlist[] User wishlists
 * @var $wishlist_token               string Wishlist token
 * @var $heading_icon                 string Heading icon HTML tag
 */
if ( ! defined( 'YITH_WCWL' ) || !is_object($wishlist) ) {
	exit;
} // Exit if accessed directly
?>

<div id="gl_copy_to_another_wishlist">
	<form action="<?php echo esc_attr( $gl_copy_to_another_wishlist_url ); ?>" method="post" class="gl-copy-to-another-wishlist-popup">
		<div class="yith-wcwl-popup-content">

            <h3><?php esc_html_e( 'Copy to another list', 'yith-woocommerce-wishlist' ); ?></h3>

			<p class="form-row">
				<?php printf( __( 'This item is already in the <b>%s</b> list.', 'yith-woocommerce-wishlist' ), $wishlist->get_formatted_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>

			<p class="form-row form-row-wide">
				<select name="new_wishlist_id" class="change-to-wishlist">
					<?php
					foreach ( $users_wishlists as $wl ):
						if ( $wl->get_token() === $wishlist_token ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $wl->get_id() ); ?>">
							<?php echo esc_html( sprintf( '%s - %s', $wl->get_formatted_name(), $wl->get_formatted_privacy() ) ); ?>
						</option>
					<?php
					endforeach;
					?>
				</select>
			</p>
		</div>
		<div class="yith-wcwl-popup-footer">
			<button class="gl-copy-to-another-wishlist-button gl-copy-to-another-wishlist-button-popup wishlist-submit button alt">
				Copy
			</button>
		</div>
	</form>
</div>
