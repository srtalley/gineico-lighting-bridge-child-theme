<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Form to Request a quote
 *
 * @package Yithemes
 * @since   2.0.0
 * @author  Yithemes
 */

?>
<div class="yith-ywraq-mail-form-wrapper">
    <h3><?php echo apply_filters( 'ywraq_form_title', __( 'Send the request', 'yith-woocommerce-request-a-quote' ) ) ?></h3>
    <form id="yith-ywraq-default-form" name="yith-ywraq-default-form"
          action="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ) ?>" method="post"
          enctype="multipart/form-data">
        <input type="hidden" id="yith-ywraq-default-form-sent" name="yith-ywraq-default-form-sent" value="1">

	    <?php do_action( 'ywraq_before_content_default_form' ); ?>
	    <?php
            $column_items_count = intval(get_option( 'request_quote_column_items_count', '' ));
            if(empty($column_items_count)) {
                $column_items_count = 4;
            }
            $count = 0;
            $current_is_second_column = false;
            echo '<div class="request-quote-column">';
            foreach ( $fields as $key => $field ) {
                if($count > 0 and $count % $column_items_count == 0 and !$current_is_second_column) {
                    echo '</div>';
                    echo '<div class="request-quote-column">';
                    $current_is_second_column = true;
                }
                if ( isset( $field['enabled'] ) && $field['enabled'] ) {
                    woocommerce_form_field( $key, $field, YITH_YWRAQ_Default_Form()->get_value( $key, $field ) );
                    $count++;
                }
            }
            echo '</div>';
            echo '<div class="request-quote-clear"></div>';
	    ?>

	    <?php if ( ! is_user_logged_in() && 'yes' == $registration_is_enabled ) : ?>
		    <?php do_action( 'ywraq_before_registration_default_form' ); ?>
            <div class="woocommerce-account-fields">
	            <?php if ( 'no' == $force_registration ) : ?>
                    <p class="form-row form-row-wide create-account">
                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                            <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
                                   id="createaccount" <?php checked( true === apply_filters( 'ywraq_create_account_default_checked', false ), true ) ?>
                                   type="checkbox" name="createaccount" value="1"/>
                            <span><?php _e( 'Create an account?', 'yith-woocommerce-request-a-quote' ); ?></span>
                        </label>
                    </p>
	            <?php endif; ?>

			    <?php if ( $account_fields ) : ?>
                    <div class="create-account">
					    <?php foreach ( $account_fields as $key => $field ) : ?>
						    <?php woocommerce_form_field( $key, $field, '' ); ?>
					    <?php endforeach; ?>
                        <div class="clear"></div>
                    </div>
			    <?php endif; ?>
            </div>
		    <?php do_action( 'ywraq_after_registration_default_form' ); ?>
	    <?php endif; ?>

		<?php if ( ywraq_check_recaptcha_options() ) : ?>
            <p class="form-row form-row form-row-wide">
            <div class="g-recaptcha"
                 data-sitekey="<?php echo esc_attr( get_option( 'ywraq_reCAPTCHA_sitekey' ) ) ?>"></div>
            </p>
		<?php endif; ?>

        <p class="form-row form-row-wide">
            <input type="hidden" id="ywraq-mail-wpnonce" name="ywraq_mail_wpnonce"
                   value="<?php echo wp_create_nonce( 'ywraq-default-form-request' ) ?>">
            <input class="button raq-send-request" type="submit"
                   value="<?php echo apply_filters( 'ywraq_form_defaul_submit_label', __( 'Send Your Request', 'yith-woocommerce-request-a-quote' ) ) ?>">
        </p>

		<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
            <input type="hidden" class="lang_param" name="lang" value="<?php echo( ICL_LANGUAGE_CODE ); ?>"/>
		<?php endif ?>

	    <?php do_action( 'ywraq_after_content_default_form' ); ?>
    </form>
</div>
