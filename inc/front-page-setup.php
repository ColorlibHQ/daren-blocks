<?php
/**
 * Give a fresh install the site it was shown in the screenshot.
 *
 * A block theme activated on an empty site shows the blog index, which looks
 * nothing like the demo and leaves the owner to assemble a home page from
 * patterns before they can tell whether they like it. This builds the pages
 * once, on first activation, and never touches them again.
 *
 * Patterns are **expanded into real post content** rather than referenced, so
 * every word is editable in the editor without hunting through theme files.
 * That expansion is also why the dynamic parts of the theme are shortcodes:
 * PHP inside stored post content never runs, so a pattern that rendered the
 * contact form inline would freeze its output into the page permanently.
 * `[daren_contact_form]` survives the round trip because a shortcode is
 * expanded at render time, every time. The story sections are query blocks,
 * which are dynamic by nature: the copy around them is stored, the posts are
 * read fresh on every view.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;

const DAREN_SETUP_FLAG = 'daren_front_page_created';

/**
 * Pages to create, in order. Slug => [title, pattern, template].
 *
 * @return array<string, array<string, string>>
 */
function daren_starter_pages() {
	return array(
		'home'    => array(
			'title'    => __( 'Home', 'daren' ),
			'pattern'  => 'daren/page-home',
			'template' => 'page-no-title',
		),
		'about'   => array(
			'title'    => __( 'About', 'daren' ),
			'pattern'  => 'daren/page-about',
			'template' => '',
		),
		'contact' => array(
			'title'    => __( 'Contact', 'daren' ),
			'pattern'  => 'daren/page-contact',
			'template' => '',
		),
		'blog'    => array(
			'title'    => __( 'Journal', 'daren' ),
			'pattern'  => '',
			'template' => '',
		),
	);
}


/**
 * The ID of the page with this slug, in any state but the bin, or 0.
 *
 * Not get_page_by_path(): it searches attachments as well as pages, so a
 * photograph uploaded as contact.jpg (slug `contact`) would count as the
 * Contact page and the real one would never be built.
 *
 * @param string $slug Page slug.
 * @return int
 */
function daren_find_page( $slug ) {
	$found = get_posts(
		array(
			'name'        => $slug,
			'post_type'   => 'page',
			'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Build the starter site, once.
 *
 * Guarded three ways: a one-shot option, a check that this is a genuinely
 * fresh site, and a per-page check that the slug is free. Activating, trying
 * another theme and coming back must not produce a second set of pages or
 * overwrite the first.
 */
function daren_create_front_page() {
	if ( get_option( DAREN_SETUP_FLAG ) ) {
		return;
	}

	// Only on a site that has not been built yet: someone activating Daren
	// over an existing site wants their pages left alone. WordPress's own two
	// pages do not count as "built" — a brand new install has them.
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 5,
			'fields'         => 'ids',
			'exclude'        => array_filter(
				array(
					(int) get_option( 'wp_page_for_privacy_policy' ),
					daren_find_page( 'sample-page' ),
				)
			),
		)
	);

	if ( count( $existing ) > 1 ) {
		update_option( DAREN_SETUP_FLAG, 'skipped: site already had pages', false );
		return;
	}

	$created = array();

	// The pages are the theme's own markup, not a visitor's. Without this the
	// post filter strips what an author without `unfiltered_html` may not post
	// — the contact page's map <iframe> vanished when activation ran with no
	// user (a Playground blueprint) and would for any site admin on multisite,
	// where only super admins hold that capability. Restored straight after.
	$kses = false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' );
	if ( $kses ) {
		kses_remove_filters();
	}

	foreach ( daren_starter_pages() as $slug => $page ) {
		if ( daren_find_page( $slug ) ) {
			continue;
		}

		$content = '';
		if ( $page['pattern'] ) {
			$content = daren_pattern_content( $page['pattern'] );
			if ( '' === $content ) {
				continue;
			}
		}

		// wp_insert_post() unslashes its input. Pattern markup carries JSON escapes
		// such as \u002d in block attributes; without wp_slash() they lose their
		// backslash and every spacer opens as "unexpected or invalid content".
		$id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => wp_slash( $content ),
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		if ( ! is_wp_error( $id ) && $id ) {
			$created[ $slug ] = $id;
			if ( $page['template'] ) {
				update_post_meta( $id, '_wp_page_template', $page['template'] );
			}
		}
	}

	if ( isset( $created['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['home'] );
	}
	if ( isset( $created['blog'] ) ) {
		update_option( 'page_for_posts', $created['blog'] );
	}

	daren_create_primary_menu( $created );

	if ( $kses ) {
		kses_init_filters();
	}

	// Claimed only now, and only if something was actually built. Firing before
	// the pattern registry is ready is a real possibility — the content comes
	// back empty and every page is skipped — and a flag set up front would make
	// that one bad moment permanent. Left unset, the admin_init retry below
	// finishes the job on the next page load.
	if ( $created ) {
		update_option( DAREN_SETUP_FLAG, gmdate( 'c' ), false );
	}
}
add_action( 'after_switch_theme', 'daren_create_front_page' );

/**
 * Second chance.
 *
 * after_switch_theme can fire before the block pattern registry is populated,
 * in which case every page resolves to empty content and nothing is built. This
 * runs once more on the first admin request, by which time patterns are
 * certainly registered, and does nothing at all once the flag is set.
 */
function daren_create_front_page_retry() {
	if ( get_option( DAREN_SETUP_FLAG ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	daren_create_front_page();
}
add_action( 'admin_init', 'daren_create_front_page_retry' );

/**
 * The markup of a registered pattern, with nested pattern references expanded.
 *
 * The page patterns are built out of `<!-- wp:pattern {"slug":"..."} /-->`
 * references. Stored in a post those still *render* — WordPress resolves them
 * on output — but they are not editable: the editor shows one opaque block per
 * section, and changing a word means finding the pattern file in the theme.
 * The whole point of building the starter site as real content is that the
 * owner can rewrite it, so the references are resolved here, recursively.
 *
 * `$seen` guards against a pattern that references itself, directly or through
 * a chain. Without it that is an infinite loop and a white screen, at
 * activation, on someone else's site.
 *
 * @param string   $name Pattern name, e.g. `daren/page-home`.
 * @param string[] $seen Names already being expanded on this branch.
 * @return string Pattern content, or '' when it is not registered.
 */
function daren_pattern_content( $name, $seen = array() ) {
	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return '';
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	if ( ! $registry->is_registered( $name ) ) {
		return '';
	}

	$pattern = $registry->get_registered( $name );
	$content = isset( $pattern['content'] ) ? $pattern['content'] : '';

	if ( '' === $content || in_array( $name, $seen, true ) ) {
		return $content;
	}

	$seen[] = $name;

	return (string) preg_replace_callback(
		'#<!--\s*wp:pattern\s+(\{.*?\})\s*/-->#s',
		static function ( $matches ) use ( $seen ) {
			$attributes = json_decode( $matches[1], true );

			if ( ! is_array( $attributes ) || empty( $attributes['slug'] ) ) {
				return $matches[0];
			}

			$nested = daren_pattern_content( $attributes['slug'], $seen );

			// A reference we cannot resolve is left as it was: it still
			// renders, which is better than deleting the section.
			return '' === $nested ? $matches[0] : $nested;
		},
		$content
	);
}

/**
 * A navigation menu pointing at the pages just created.
 *
 * Block themes use a `wp_navigation` post rather than a nav menu, and the
 * header pattern falls back to a page list when there is none — so this is an
 * improvement on the fallback, not a requirement for the header to work.
 *
 * @param array<string, int> $pages Slug => page ID.
 */
function daren_create_primary_menu( $pages ) {
	if ( ! $pages ) {
		return;
	}

	$order = array( 'home', 'blog', 'about', 'contact' );
	$items = '';

	foreach ( $order as $slug ) {
		if ( ! isset( $pages[ $slug ] ) ) {
			continue;
		}

		$id    = $pages[ $slug ];
		$title = get_the_title( $id );

		// core/home-link rather than a custom link for the front page: only
		// home-link is given `current-menu-item`, so a custom link would never
		// highlight while someone is actually on the home page.
		if ( 'home' === $slug ) {
			$items .= '<!-- wp:home-link {"label":"' . esc_attr( $title ) . '"} /-->';
			continue;
		}

		$items .= sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type"} /-->',
			esc_attr( $title ),
			$id,
			esc_url( get_permalink( $id ) )
		);
	}

	if ( '' === $items ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Primary', 'daren' ),
			'post_name'    => 'primary',
			'post_content' => wp_slash( $items ),
			'post_status'  => 'publish',
			'post_type'    => 'wp_navigation',
		)
	);
}
