<?php
/**
 * The contact form.
 *
 * A journal's contact page exists so that a reader can write to the author,
 * so Daren ships the form rather than requiring a plugin for it.
 *
 * It is a **shortcode**, not inline PHP in the pattern. That is not a style
 * preference: inc/front-page-setup.php expands patterns into real post content
 * so the copy stays editable, and PHP inside stored post content never runs.
 * A pattern that rendered the form inline would freeze whatever it produced at
 * activation into the page forever.
 *
 * A theme must not create database tables or register a post type, so Daren
 * stores nothing. It validates, then hands the message to whoever wants it:
 *
 *   - `daren_contact_handlers` — return true from any handler to say the
 *     message has been dealt with, and the built-in email is skipped. This is
 *     where a CRM, a helpdesk or a webhook hooks in.
 *   - `daren_contact_email_to` / `_subject` / `_body` — adjust the email the
 *     theme sends when nothing else claims the message.
 *   - `daren_contact_fields` — add, remove or relabel fields.
 *
 * The form works with JavaScript off: it is a plain POST to the same URL,
 * answered with a redirect carrying the result.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;

const DAREN_CONTACT_ACTION = 'daren_contact';

/**
 * The fields the form asks for, in the template's order: the message first,
 * then name and email side by side, then the subject.
 *
 * @return array<string, array<string, mixed>>
 */
function daren_contact_fields() {
	$fields = array(
		'message' => array(
			'label'       => __( 'Message', 'daren' ),
			'placeholder' => __( 'Enter message', 'daren' ),
			'type'        => 'textarea',
			'required'    => true,
		),
		'name'    => array(
			'label'        => __( 'Your name', 'daren' ),
			'placeholder'  => __( 'Enter your name', 'daren' ),
			'type'         => 'text',
			'autocomplete' => 'name',
			'required'     => true,
		),
		'email'   => array(
			'label'        => __( 'Email address', 'daren' ),
			'placeholder'  => __( 'Enter email address', 'daren' ),
			'type'         => 'email',
			'autocomplete' => 'email',
			'required'     => true,
		),
		'subject' => array(
			'label'       => __( 'Subject', 'daren' ),
			'placeholder' => __( 'Enter subject', 'daren' ),
			'type'        => 'text',
			'required'    => false,
		),
	);

	/**
	 * Filters the contact form fields.
	 *
	 * @param array $fields Field definitions keyed by name.
	 */
	return apply_filters( 'daren_contact_fields', $fields );
}

/**
 * Build an attribute string from a map, escaping every value.
 *
 * @param array $attributes Attribute map.
 * @return string
 */
function daren_attributes( $attributes ) {
	$out = '';
	foreach ( $attributes as $key => $value ) {
		if ( '' === $value && 'value' !== $key ) {
			continue;
		}
		$out .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}
	return $out;
}

/**
 * Render one field, label included.
 *
 * The design shows placeholders and no labels. The labels are still real
 * <label for> elements — visually hidden, never missing — because a placeholder
 * is not a label: it is unreadable to some screen readers and it disappears the
 * moment the field has content.
 *
 * @param string $prefix Form prefix, for ids and classes.
 * @param string $name   Field name.
 * @param array  $field  Field definition.
 * @return string
 */
function daren_form_field( $prefix, $name, $field ) {
	$id       = $prefix . '-' . $name;
	$required = ! empty( $field['required'] );

	$attributes = array(
		'id'          => $id,
		'name'        => $name,
		'class'       => 'daren-field__control',
		'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
	);

	if ( $required ) {
		$attributes['required'] = 'required';
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$attributes['autocomplete'] = $field['autocomplete'];
	}

	$out  = '<p class="daren-field daren-field--' . esc_attr( $name ) . '">';
	$out .= '<label class="daren-field__label screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] );
	if ( $required ) {
		$out .= ' ' . esc_html__( '(required)', 'daren' );
	}
	$out .= '</label>';

	if ( 'textarea' === $field['type'] ) {
		$attributes['rows'] = 9;
		$out               .= '<textarea' . daren_attributes( $attributes ) . '></textarea>';
	} else {
		$attributes['type'] = $field['type'];
		$out               .= '<input' . daren_attributes( $attributes ) . '>';
	}

	return $out . '</p>';
}

/**
 * The current URL, without any previous result parameter.
 *
 * @param string $param Result parameter to strip.
 * @return string
 */
function daren_current_url( $param ) {
	$url = is_singular() ? get_permalink() : '';

	// The footer's sign-up also appears on archives and search results, which
	// have no permalink. The request path is joined to the site's own scheme and
	// host — never the Host header — because home_url( $path ) would repeat the
	// directory of a site installed in one.
	if ( ! $url ) {
		$home = wp_parse_url( home_url() );
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$url  = ( isset( $home['scheme'] ) ? $home['scheme'] : 'https' ) . '://' . ( isset( $home['host'] ) ? $home['host'] : '' )
			. ( isset( $home['port'] ) ? ':' . $home['port'] : '' ) . $uri;
	}

	return remove_query_arg( array( $param ), $url );
}

/**
 * A result notice, if the redirect carried one.
 *
 * @param string $param    Query parameter holding the result.
 * @param array  $messages Result key => [kind, text].
 * @return string
 */
function daren_form_notice( $param, $messages ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$result = isset( $_GET[ $param ] ) ? sanitize_key( wp_unslash( $_GET[ $param ] ) ) : '';

	if ( ! isset( $messages[ $result ] ) ) {
		return '';
	}

	list( $kind, $text ) = $messages[ $result ];

	return '<p class="daren-notice is-' . esc_attr( $kind ) . '" role="status">' . esc_html( $text ) . '</p>';
}

/**
 * The contact form.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function daren_contact_form( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'button' => __( 'Send Message', 'daren' ),
		),
		$atts,
		'daren_contact_form'
	);

	$here = daren_current_url( 'daren-contact' );

	$out  = '<form class="daren-contact" method="post" action="' . esc_url( $here ) . '#daren-contact">';
	$out .= '<div id="daren-contact" class="daren-form__anchor"></div>';
	$out .= daren_form_notice(
		'daren-contact',
		array(
			'sent'    => array( 'ok', __( 'Thank you — your message is on its way. I read every one and reply to most within a few days.', 'daren' ) ),
			'invalid' => array( 'error', __( 'Please check the form: a message, your name and your email address are all needed.', 'daren' ) ),
			'email'   => array( 'error', __( 'That email address does not look right.', 'daren' ) ),
			'failed'  => array( 'error', __( 'Sorry, the message could not be sent. Please try again later, or write to the address beside the form.', 'daren' ) ),
			'expired' => array( 'error', __( 'That form had been open a while and expired. Please send it again.', 'daren' ) ),
		)
	);
	$out .= wp_nonce_field( DAREN_CONTACT_ACTION, 'daren_contact_nonce', true, false );
	$out .= '<input type="hidden" name="action" value="' . esc_attr( DAREN_CONTACT_ACTION ) . '">';

	// The page to come back to, carried explicitly. wp_get_referer() returns
	// false whenever the referer is the current URL — always, for a form that
	// posts to its own page — so it would drop the visitor on the front page.
	// Validated with wp_validate_redirect() on the way out.
	$out .= '<input type="hidden" name="daren_redirect" value="' . esc_url( $here ) . '">';

	// A field no visitor sees and no visitor fills in. Bots fill everything.
	$out .= '<p class="daren-form__trap" aria-hidden="true">';
	$out .= '<label for="daren-contact-website">' . esc_html__( 'Leave this field empty', 'daren' ) . '</label>';
	$out .= '<input id="daren-contact-website" type="text" name="daren_website" tabindex="-1" autocomplete="off">';
	$out .= '</p>';

	$out .= '<div class="daren-contact__grid">';
	foreach ( daren_contact_fields() as $name => $field ) {
		$out .= daren_form_field( 'daren-contact', $name, $field );
	}
	$out .= '</div>';

	$out .= '<p class="daren-form__actions wp-block-button is-style-daren-arrow">';
	$out .= '<button type="submit" class="wp-block-button__link wp-element-button">' . esc_html( $atts['button'] ) . '</button>';
	$out .= '</p>';

	$out .= '</form>';

	return $out;
}
add_shortcode( 'daren_contact_form', 'daren_contact_form' );

/**
 * Redirect back to a form with a result, and stop.
 *
 * @param string $url    Where to go.
 * @param string $param  Result parameter.
 * @param string $result Result key.
 * @param string $anchor Fragment to land on.
 */
function daren_form_redirect( $url, $param, $result, $anchor ) {
	wp_safe_redirect( add_query_arg( $param, $result, $url ) . '#' . $anchor, 303 );
	exit;
}

/**
 * Where a posted form asked to be sent back to, validated.
 *
 * @param string $param Result parameter to strip.
 * @return string
 */
function daren_posted_redirect( $param ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce checked by the caller; this only chooses where to redirect.
	$posted   = isset( $_POST['daren_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['daren_redirect'] ) ) : '';
	$redirect = wp_validate_redirect( $posted, home_url( '/' ) );
	return remove_query_arg( array( $param ), $redirect );
}

/**
 * Handle a submitted message.
 *
 * Runs on `template_redirect` so it can redirect before anything is sent —
 * the POST/redirect/GET that stops a refresh sending the message twice.
 */
function daren_handle_contact() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked immediately below.
	if ( ! isset( $_POST['action'] ) || DAREN_CONTACT_ACTION !== $_POST['action'] ) {
		return;
	}

	$redirect = daren_posted_redirect( 'daren-contact' );

	$nonce = isset( $_POST['daren_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['daren_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, DAREN_CONTACT_ACTION ) ) {
		daren_form_redirect( $redirect, 'daren-contact', 'expired', 'daren-contact' );
	}

	// Silently accept and discard anything that filled the honeypot: telling a
	// bot it failed only teaches it to try again differently.
	if ( ! empty( $_POST['daren_website'] ) ) {
		daren_form_redirect( $redirect, 'daren-contact', 'sent', 'daren-contact' );
	}

	$message = array();
	foreach ( daren_contact_fields() as $name => $field ) {
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
		$raw = is_string( $raw ) ? $raw : '';

		if ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( ! empty( $field['required'] ) && '' === $value ) {
			daren_form_redirect( $redirect, 'daren-contact', 'invalid', 'daren-contact' );
		}

		$message[ $name ] = $value;
	}

	if ( ! empty( $message['email'] ) && ! is_email( $message['email'] ) ) {
		daren_form_redirect( $redirect, 'daren-contact', 'email', 'daren-contact' );
	}

	/**
	 * Filters whether the message has already been handled.
	 *
	 * Return true from any handler and Daren will not send its own email —
	 * which is how a form plugin, a CRM or a webhook takes this over.
	 *
	 * @param bool  $handled Whether something has dealt with the message.
	 * @param array $message The sanitised message.
	 */
	$handled = apply_filters( 'daren_contact_handlers', false, $message );

	if ( ! $handled ) {
		$handled = daren_contact_email( $message );
	}

	daren_form_redirect( $redirect, 'daren-contact', $handled ? 'sent' : 'failed', 'daren-contact' );
}
add_action( 'template_redirect', 'daren_handle_contact' );

/**
 * Email the message to the site's admin address.
 *
 * From: is the site's own address, never the visitor's. Putting the visitor
 * there fails SPF and DMARC — the mail is sent by this server, not by their
 * provider — and it is the classic route to header injection. Reply-To carries
 * them instead, and wp_mail() rejects a header containing a newline.
 *
 * @param array $message Sanitised message.
 * @return bool
 */
function daren_contact_email( $message ) {
	$to = apply_filters( 'daren_contact_email_to', get_option( 'admin_email' ) );

	if ( ! $to || ! is_email( $to ) ) {
		return false;
	}

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	if ( ! empty( $message['subject'] ) ) {
		/* translators: 1: site name, 2: the subject the visitor wrote. */
		$subject = sprintf( __( '[%1$s] %2$s', 'daren' ), $site, $message['subject'] );
	} else {
		/* translators: %s: site name. */
		$subject = sprintf( __( '[%s] A message from the contact form', 'daren' ), $site );
	}
	$subject = apply_filters( 'daren_contact_email_subject', $subject, $message );

	$lines  = array();
	$fields = daren_contact_fields();
	foreach ( $message as $name => $value ) {
		if ( '' === $value ) {
			continue;
		}
		$label   = isset( $fields[ $name ]['label'] ) ? $fields[ $name ]['label'] : $name;
		$lines[] = $label . ': ' . $value;
	}

	$body = implode( "\n\n", $lines );
	$body = apply_filters( 'daren_contact_email_body', $body, $message );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $message['email'] ) && is_email( $message['email'] ) ) {
		$headers[] = 'Reply-To: ' . $message['email'];
	}

	return (bool) wp_mail( $to, $subject, $body, $headers );
}
