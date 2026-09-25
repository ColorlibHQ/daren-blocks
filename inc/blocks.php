<?php
/**
 * Behaviour the design asks of a few core blocks.
 *
 * Two pieces of the HTML template have no core block that exists in every
 * WordPress this theme supports (6.6 and later):
 *
 * 1. **The wordmark's red letter.** The template's logo is "DarEn." with the E in
 *    red. A site title written in camel case gets the same treatment: the first
 *    capital that follows a lower-case letter is set in the accent colour. A
 *    title with no such capital is left exactly as it is.
 *
 * 2. **The row under every story** — comments, reading time, share. Core's
 *    Comments Link and Time to Read blocks arrived in WordPress 6.9, so a
 *    pattern that used them would show "unsupported block" on 6.6 to 6.8. The
 *    row is built from `core/read-more` instead, which every supported version
 *    has and which already knows which post it belongs to; a class on the block
 *    says which job it does, and the filter below fills in the number. The
 *    label stays editable in the editor and the icon shows there too, because
 *    both come from the block itself.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;

/**
 * Set the camel-case capital of the site title in the accent colour.
 *
 * Switch it off with:
 *
 *     add_filter( 'daren_title_accent', '__return_false' );
 *
 * @param string $content Rendered block.
 * @return string
 */
function daren_site_title_accent( $content ) {
	if ( ! apply_filters( 'daren_title_accent', true ) ) {
		return $content;
	}

	// Only the text between the tags is touched, and only its first match.
	return (string) preg_replace_callback(
		'#>([^<>]+)<#u',
		static function ( $matches ) {
			$text = preg_replace( '/(?<=\p{Ll})(\p{Lu})/u', '<span class="daren-title-accent">$1</span>', $matches[1], 1 );
			return '>' . $text . '<';
		},
		$content,
		1
	);
}
add_filter( 'render_block_core/site-title', 'daren_site_title_accent' );

/**
 * Which job a read-more block does, from its class.
 *
 * @param array $block Parsed block.
 * @return string '' when it is an ordinary read-more link.
 */
function daren_action_kind( $block ) {
	$classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
	foreach ( array( 'comments', 'time', 'share' ) as $kind ) {
		if ( false !== strpos( $classes, 'daren-action--' . $kind ) ) {
			return $kind;
		}
	}
	return '';
}

/**
 * Minutes to read a post, at 220 words a minute, never less than one.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function daren_reading_minutes( $post_id ) {
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $post_id ) ) ) );

	/**
	 * Filters the reading speed used for the reading time, in words a minute.
	 *
	 * @param int $speed Words a minute.
	 */
	$speed = max( 1, (int) apply_filters( 'daren_words_per_minute', 220 ) );

	return max( 1, (int) ceil( $words / $speed ) );
}

/**
 * Fill in the comments, reading-time and share links.
 *
 * @param string   $content  Rendered block.
 * @param array    $block    Parsed block.
 * @param WP_Block $instance Block instance, which carries the post ID.
 * @return string
 */
function daren_render_action( $content, $block, $instance = null ) {
	$kind = daren_action_kind( $block );
	if ( '' === $kind ) {
		return $content;
	}

	$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : get_the_ID();
	if ( ! $post_id ) {
		return $content;
	}

	$label = isset( $block['attrs']['content'] ) ? trim( wp_strip_all_tags( $block['attrs']['content'] ) ) : '';
	$title = get_the_title( $post_id );
	$class = 'wp-block-read-more daren-action daren-action--' . $kind;
	if ( ! empty( $block['attrs']['className'] ) ) {
		$class .= ' ' . $block['attrs']['className'];
	}
	$class = implode( ' ', array_unique( preg_split( '/\s+/', trim( $class ) ) ) );

	if ( 'comments' === $kind ) {
		// Nothing to count and nowhere to write: say nothing rather than "0".
		if ( ! comments_open( $post_id ) && ! get_comments_number( $post_id ) ) {
			return '';
		}
		$count = (int) get_comments_number( $post_id );
		$count_text = number_format_i18n( $count );
		if ( '' === $label || 'Comments' === $label ) {
			/* translators: %s: number of comments. */
			$text = sprintf( _n( '%s Comment', '%s Comments', $count, 'daren' ), $count_text );
		} else {
			$text = $count_text . ' ' . $label;
		}
		/* translators: %s: post title. */
		$hidden = sprintf( __( ' on %s', 'daren' ), $title );
		$href   = $count ? get_comments_link( $post_id ) : get_permalink( $post_id ) . '#respond';
		return sprintf(
			'<a class="%1$s" href="%2$s">%3$s<span class="screen-reader-text">%4$s</span></a>',
			esc_attr( $class ),
			esc_url( $href ),
			esc_html( $text ),
			esc_html( $hidden )
		);
	}

	if ( 'time' === $kind ) {
		$minutes = daren_reading_minutes( $post_id );
		$minutes_text = number_format_i18n( $minutes );
		if ( '' === $label || 'min read' === $label ) {
			/* translators: %s: number of minutes. */
			$text = sprintf( __( '%s min read', 'daren' ), $minutes_text );
		} else {
			$text = $minutes_text . ' ' . $label;
		}
		return sprintf(
			'<a class="%1$s" href="%2$s">%3$s<span class="screen-reader-text">%4$s</span></a>',
			esc_attr( $class ),
			esc_url( get_permalink( $post_id ) ),
			esc_html( $text ),
			/* translators: %s: post title. */
			esc_html( sprintf( __( ': %s', 'daren' ), $title ) )
		);
	}

	// Share: a link to the post, which assets/js/interactions.js turns into the
	// system share sheet, or a copy-the-link button where there is none.
	return sprintf(
		'<a class="%1$s" href="%2$s" data-daren-share="%3$s">%4$s<span class="screen-reader-text">%5$s</span></a>',
		esc_attr( $class ),
		esc_url( get_permalink( $post_id ) ),
		esc_attr( wp_strip_all_tags( $title ) ),
		esc_html( '' === $label ? __( 'Share', 'daren' ) : $label ),
		/* translators: %s: post title. */
		esc_html( sprintf( __( ': %s', 'daren' ), $title ) )
	);
}
add_filter( 'render_block_core/read-more', 'daren_render_action', 10, 3 );
