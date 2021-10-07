<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* GL CUSTOMIZATIONS */ ?>
<?php /* translators: %s: Customer username */ ?>
<!-- <p><?php //printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p> -->
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<!-- <p><?php //printf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'woocommerce' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p> -->
<?php //if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
	<?php /* translators: %s: Auto generated password */ ?>
	<!-- <p><?php //printf( esc_html__( 'Your password has been automatically generated: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p> -->
<?php //endif; ?>

<p><strong>Welcome to Gineico Lighting's online projects schedule and quoting system where you will be able to select products from our website and add them to your <a href="<?php echo esc_url( site_url('/my-favourites/')  ); ?>" target="_blank">MY FAVOURITES</a> list, your <a href="<?php echo esc_url( site_url('/my-projects/manage/')  ); ?>" target="_blank">MY PROJECTS</a> jobs lists or add them directly to your <a href="<?php echo esc_url( site_url('/request-quote/')  ); ?>" target="_blank">MY QUOTES</a> for pricing.</strong></p>

<p>Within our system, located from the main menu you will find -</p>

<p>
	<ul>
		<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' )  ); ?>" target="_blank">MY ACCOUNT</a> to login</li>
		<li><a href="<?php echo esc_url( site_url('/my-favourites/')  ); ?>" target="_blank">MY FAVOURITES</a> a list of your favourite fittings.</li>
		<li><a href="<?php echo esc_url( site_url('/my-projects/manage/')  ); ?>" target="_blank">MY PROJECTS</a> a list of your current project schedules with features to download PDF copies for reference or to share. Once final fittings have been decided you will be able to select individual fittings for a quote.</li>
		<li><a href="<?php echo esc_url( site_url('/request-quote/')  ); ?>" target="_blank">MY REQUEST QUOTES</a> a list of your requested quotes</li>
		<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'quotes/'); ?>" target="_blank">MY QUOTATIONS</a> a list of quotes with prices from Gineico Lighting</li>
	</ul>
</p>

<p><strong>Your account has been setup:</strong></p>

<p>
	<ul>
		<li><strong>Username:</strong> <?php echo esc_html( $user_login ); ?></li>
		<li><strong>Password:</strong> <?php echo esc_html( $user_pass ); ?></li>
	</ul>
</p>

<p>Please log into your account at <?php echo make_clickable( esc_url( site_url('/login/') ) ); ?> and proceed to add products to your lists or for quoting.</p>

<p>To access all of your details you will find <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' )  ); ?>" target="_blank">MY ACCOUNT</a> in the main menu.</p>

<p>You can change your password at <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'edit-account/' ); ?>" target="_blank"><?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'edit-account/' ); ?></p>

<p>If you need to speak to us directly please call us on <a href="tel:+61-417-950-455" target="_blank">+61 417 950 455</a></p>

<p>We look forward to speaking to you soon.</p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
