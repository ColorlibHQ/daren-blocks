<?php
/**
 * The newsletter sign-up in the footer.
 *
 * The template draws an email field and an arrow button under "Newsletter" and
 * sends them nowhere. A form that looks like it works and does not is worse
 * than no form, so this one works: it validates the address and passes it on.
 *
 * A theme cannot be a mailing list, so by default it emails the site owner —
 * "someone asked to join" — which is honest and needs nothing installed. The
 * moment a list exists, one filter hands the address to it instead:
 *
 *     add_filter( 'daren_newsletter_handlers', function ( $handled, $email ) {
 *         my_list_subscribe( $email );   // Mailchimp, Sendy, MailPoet …
 *         return true;                   // and skip the theme's email
 *     }, 10, 2 );
 *
 * A shortcode for the same reason as the contact form: the footer is a pattern
 * that the Site Editor may copy into the database, where PHP never runs.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;

const DAREN_NEWSLETTER_ACTION = 'daren_newsletter';

/**
 * The sign-up form: one field, one button.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function daren_newsletter_form( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'placeholder' => __( 'Enter email address', 'daren' ),
			'button'      => __( 'Subscribe', 'daren' ),
		),
		$atts,
		'daren_newsletter_form'
	);

	$here = daren_current_url( 'daren-newsletter' );
	$id   = 'daren-newsletter-email';

	$out  = '<form class="daren-subscribe" method="post" action="' . esc_url( $here ) . '#daren-newsletter">';
	$out .= '<div id="daren-newsletter" class="daren-form__anchor"></div>';
	$out .= daren_form_notice(
		'daren-newsletter',
		array(
			'sent'    => array( 'ok', __( 'Thank you — you are on the list.', 'daren' ) ),
			'email'   => array( 'error', __( 'That email address does not look right.', 'daren' ) ),
			'failed'  => array( 'error', __( 'Sorry, that did not go through. Please try again later.', 'daren' ) ),
			'expired' => array( 'error', __( 'The form had expired. Please try again.', 'daren' ) ),
		)
	);
	$out .= wp_nonce_field( DAREN_NEWSLETTER_ACTION, 'daren_newsletter_nonce', true, false );
	$out .= '<input type="hidden" name="action" value="' . esc_attr( DAREN_NEWSLETTER_ACTION ) . '">';
	$out .= '<input type="hidden" name="daren_redirect" value="' . esc_url( $here ) . '">';

	$out .= '<p class="daren-form__trap" aria-hidden="true">';
	$out .= '<label for="daren-newsletter-website">' . esc_html__( 'Leave this field empty', 'daren' ) . '</label>';
	$out .= '<input id="daren-newsletter-website" type="text" name="daren_website" tabindex="-1" autocomplete="off">';
	$out .= '</p>';

	$out .= '<div class="daren-subscribe__row">';
	$out .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html__( 'Email address', 'daren' ) . '</label>';
	$out .= '<input id="' . esc_attr( $id ) . '" class="daren-field__control" type="email" name="daren_email" required autocomplete="email" placeholder="' . esc_attr( $atts['placeholder'] ) . '">';
	$out .= '<button type="submit" class="daren-subscribe__button wp-element-button"><span class="screen-reader-text">' . esc_html( $atts['button'] ) . '</span></button>';
	$out .= '</div>';

	$out .= '</form>';

	return $out;
}
add_shortcode( 'daren_newsletter_form', 'daren_newsletter_form' );

/**
 * Handle a sign-up.
 */
function daren_handle_newsletter() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked immediately below.
	if ( ! isset( $_POST['action'] ) || DAREN_NEWSLETTER_ACTION !== $_POST['action'] ) {
		return;
	}

	$redirect = daren_posted_redirect( 'daren-newsletter' );

	$nonce = isset( $_POST['daren_newsletter_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['daren_newsletter_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, DAREN_NEWSLETTER_ACTION ) ) {
		daren_form_redirect( $redirect, 'daren-newsletter', 'expired', 'daren-newsletter' );
	}

	if ( ! empty( $_POST['daren_website'] ) ) {
		daren_form_redirect( $redirect, 'daren-newsletter', 'sent', 'daren-newsletter' );
	}

	$email = isset( $_POST['daren_email'] ) ? sanitize_email( wp_unslash( $_POST['daren_email'] ) ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		daren_form_redirect( $redirect, 'daren-newsletter', 'email', 'daren-newsletter' );
	}

	/**
	 * Filters whether the sign-up has been handled.
	 *
	 * Return true once the address is on a list, and the theme will not email
	 * the site owner about it.
	 *
	 * @param bool   $handled Whether something has dealt with the sign-up.
	 * @param string $email   The sanitised address.
	 */
	$handled = apply_filters( 'daren_newsletter_handlers', false, $email );

	if ( ! $handled ) {
		$to = apply_filters( 'daren_newsletter_email_to', get_option( 'admin_email' ) );
		if ( $to && is_email( $to ) ) {
			$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
			/* translators: %s: site name. */
			$subject = sprintf( __( '[%s] New newsletter sign-up', 'daren' ), $site );
			/* translators: %s: email address. */
			$body    = sprintf( __( '%s asked to join the newsletter from the sign-up form on the site.', 'daren' ), $email );
			$handled = (bool) wp_mail( $to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email ) );
		}
	}

	daren_form_redirect( $redirect, 'daren-newsletter', $handled ? 'sent' : 'failed', 'daren-newsletter' );
}
add_action( 'template_redirect', 'daren_handle_newsletter' );
