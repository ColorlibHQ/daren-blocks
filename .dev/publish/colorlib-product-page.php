<?php
/**
 * Build the Daren product page on colorlib.com/wp, as a child of the themes
 * listing (5091).
 *
 * Same shape as the Unioncorp page it was made from: what the theme is, what it
 * looks like, what you get, and the questions people ask. Daren leads with what
 * a journal needs most: story layouts that fill themselves from the posts.
 * Near the top, "Two versions of Daren" sets this block theme beside the
 * older Elementor edition, which stays free on GitHub.
 *
 * Every factual claim below was checked against this theme's own code, not
 * carried over from Unioncorp's page. Differences that would have been wrong
 * if copied: Daren has a contact form AND a newsletter sign-up (two handler
 * filters, not one enquiry filter); it has no scroll animations, counters or
 * video popup, so that feature and its FAQ are gone; its fonts are Source
 * Serif Pro and Open Sans; it has 14 inserter patterns plus 12 hidden ones.
 * There is no documentation page yet, so there is no Documentation button.
 *
 * Checked against the Elementor edition's repo (ColorlibHQ/daren, public): its
 * post sections and contact block are Elementor widgets built into the theme,
 * which does nothing with them until the free Elementor plugin is active.
 *
 * Idempotent: creates the page the first time, rewrites it after that, and
 * leaves it a DRAFT. A page that is already published stays published.
 *
 * Images are found by file name, not by attachment ID, and the script refuses
 * to save while any is missing — a product page with a broken hero is worse
 * than no page.
 *
 *   Copy this file to the server under a unique name, then run it with WP-CLI
 *   from the WordPress root, as the user PHP runs as:
 *     wp --url=https://colorlib.com/wp/ eval "require '<unique path>/colorlib-product-page.php';"
 *
 * Use `wp eval "require …"`, not `wp eval-file`. Nothing here relies on globals.
 */

defined( 'ABSPATH' ) || exit;

$slug   = 'daren';
$parent = 5091;

$download   = 'https://updates.colorlib.com/download/theme/daren.zip';
$demo       = 'https://colorlibhub.com/daren-blocks/';
$el_demo    = 'https://colorlibhub.com/daren/';
$el_github  = 'https://github.com/ColorlibHQ/daren';

// ---------------------------------------------------------------------------
// Images, by file name
// ---------------------------------------------------------------------------

/*
 * colorlib.com's uploads have no year/month folder, so `_wp_attached_file` is
 * the bare file name and `LIKE '%/name'` alone never matches. A file over the
 * size threshold is stored as `name-scaled.jpg`, so accept that too.
 */
$find_image = static function ( $file ) {
	global $wpdb;
	$scaled = preg_replace( '/\.(jpe?g|png)$/i', '-scaled.$1', $file );
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			 WHERE meta_key = '_wp_attached_file'
			   AND ( meta_value = %s OR meta_value = %s OR meta_value LIKE %s OR meta_value LIKE %s )
			 ORDER BY post_id DESC LIMIT 1",
			$file,
			$scaled,
			'%/' . $wpdb->esc_like( $file ),
			'%/' . $wpdb->esc_like( $scaled )
		)
	);
};

// The files in .dev/publish/images/, made by shoot.mjs + finish.py from a
// Playground with the demo content imported.
$wanted = array(
	'card'     => 'daren-free-blog-wordpress-theme.jpg',
	'home'     => 'daren-block-theme-home.jpg',
	'stories'  => 'daren-block-theme-story-layouts.jpg',
	'palettes' => 'daren-block-theme-colour-palettes.jpg',
	'dark'     => 'daren-block-theme-dark-mode.jpg',
	'contact'  => 'daren-block-theme-contact-form.jpg',
	'blog'     => 'daren-block-theme-blog.jpg',
	'single'   => 'daren-block-theme-single-post.jpg',
);

$img     = array();
$missing = array();
foreach ( $wanted as $key => $file ) {
	$img[ $key ] = $find_image( $file );
	if ( ! $img[ $key ] ) {
		$missing[] = $file;
	}
}

if ( $missing ) {
	echo "ERROR: not in the media library yet, refusing to build a page with gaps:\n  " . implode( "\n  ", $missing ) . "\n";
	return;
}

$btn_css = 'display:inline-block !important;vertical-align:middle !important;margin-right:12px !important;margin-bottom:10px !important;';
$tint    = '#fff6f6';
$accent  = '#d91010';

/*
 * vc_btn's `link` attribute is WPBakery's own "url:…|title:…|target:…" encoding.
 * It splits on "|" then on the first ":", so a raw URL is cut off at "https:"
 * and the button renders href="http://https". Percent-encode anything in a link.
 *
 * The Download button's title must stay "Download Daren" and its href the
 * updates.colorlib.com zip: the email gate keys on that href.
 */
$btn = static function ( $title, $url, $color, $icon, $css_class ) use ( $btn_css ) {
	return '[vc_btn title="' . esc_attr( $title ) . '" style="flat" color="' . $color . '"'
		. ' link="url:' . rawurlencode( $url ) . '|title:' . rawurlencode( $title ) . '|target:_blank"'
		. ' css=".' . $css_class . '{' . $btn_css . '}" i_icon_fontawesome="fa fa-' . $icon . '" add_icon="true"]';
};

$buttons = static function ( $n ) use ( $btn, $download, $demo ) {
	return $btn( 'Download Daren', $download, 'green', 'download', "vc_custom_dr{$n}a" )
		. $btn( 'Live demo', $demo, 'grey', 'eye', "vc_custom_dr{$n}b" );
};

// Look up by slug AND parent: get_page_by_path( 'daren' ) finds only a
// top-level page (and matches attachments), and this one is a child of 5091.
$found = get_posts(
	array(
		'post_type'   => 'page',
		'name'        => $slug,
		'post_parent' => $parent,
		// An explicit list, not 'any': in WP_Query 'any' leaves drafts out, so an
		// idempotent script would keep re-creating its own draft.
		'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'numberposts' => 1,
	)
);

$existing = $found ? $found[0] : null;
$page_id  = $existing ? $existing->ID : 0;

// ---------------------------------------------------------------------------
// Content
// ---------------------------------------------------------------------------

$features = array(
	array( 'th-large', 'Sections that fill themselves', 'The opening banner, the checkerboard, the category cards and the long list are query blocks. Publish a post with a featured image and it takes its place; nothing on the home page is typed in by hand.' ),
	array( 'paint-brush', 'Eight palettes, five type pairings', 'Scarlet, Cobalt, Jade, Tangerine, Orchid, Ink and two dark palettes. Every one is measured against WCAG AA before the theme is built, in light mode and in dark.' ),
	array( 'moon-o', 'Dark mode', 'A switch for the reader, separate from the palette you chose. It follows their system setting until they choose, and it lifts your palette rather than replacing it.' ),
	array( 'envelope', 'A contact form without a plugin', 'Checked on the server, protected by a nonce and a honeypot, and it works with JavaScript turned off. Messages go to the site’s admin address, or wherever one filter sends them.' ),
	array( 'paper-plane', 'A newsletter sign-up that works', 'The footer’s sign-up checks the address and emails it to you. Hook it to Mailchimp, Sendy or MailPoet with one filter and the theme stops emailing.' ),
	array( 'plug', 'Your form plugin, styled', 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator and Ninja Forms take on the theme’s colours and spacing instead of looking like another website.' ),
	array( 'share-alt', 'Comments, reading time, share', 'Under every story: the comment count, an estimate of the reading time, and a Share link that opens the phone’s share sheet or copies the address.' ),
	array( 'font', 'The wordmark’s red letter', 'Write the site title in camel case, like DarEn., and the capital after a lower-case letter is set in the brand colour. One filter turns it off.' ),
	array( 'shopping-cart', 'WooCommerce ready', 'Prints, zines or workshop places: the shop is styled if you install WooCommerce, and the theme loads nothing for it if you do not.' ),
);

// Three to a row, each row its own [vc_row]. WPBakery columns are floats: nine
// thirds in one row snag on the tallest box above them, and the grid staggers.
$feature_rows = '';
foreach ( array_chunk( $features, 3 ) as $r => $row ) {
	$last          = ( $r === (int) ceil( count( $features ) / 3 ) - 1 );
	$feature_rows .= '[vc_row css=".vc_custom_dr05' . ( $r + 1 ) . '{padding-bottom:' . ( $last ? '40' : '0' ) . 'px !important;}"]';
	foreach ( $row as $f ) {
		list( $icon, $heading, $body ) = $f;
		$feature_rows .= '[vc_column width="1/3"][vcex_icon_box style="two" heading="' . esc_attr( $heading ) . '" heading_type="h3"'
			. ' icon="fa fa-' . $icon . '" icon_color="' . $accent . '" icon_size="28px" heading_size="20px"'
			. ' content_font_size="15px" css=".vc_custom_dr_f_' . sanitize_key( $icon ) . '{margin-bottom:26px !important;}"]'
			. $body . '[/vcex_icon_box][/vc_column]';
	}
	$feature_rows .= '[/vc_row]';
}

$faqs = array(
	array( 'Do I need a plugin?', 'No. The contact form, the newsletter sign-up and dark mode are part of the theme. WooCommerce is styled if you add it, and is not required.' ),
	array( 'The home page is nearly empty. Where are the stories?', 'The home page lists your posts: the two newest in the opening banner, the next three in the checkerboard, three more as cards and the rest in the list beside the sidebar. Publish posts with featured images and it fills itself; with sixteen or more, no story appears twice.' ),
	array( 'Where do contact messages go?', 'To the site’s admin email address by default. If you use a CRM, a helpdesk or a webhook, the <code>daren_contact_handlers</code> filter hands each message to it instead, and Daren sends nothing of its own.' ),
	array( 'Where do newsletter sign-ups go?', 'By default the theme emails you each new address, so nothing is lost and nothing needs installing. To add them to a mailing list instead, hook the <code>daren_newsletter_handlers</code> filter and return true once the address is on your list; the theme then stops emailing you.' ),
	array( 'Why is one letter of my site title red?', 'If the site title is written in camel case — a capital straight after a lower-case letter, as in DarEn. — that capital is set in the brand colour. A title without one is left as it is. To switch it off, add <code>add_filter( \'daren_title_accent\', \'__return_false\' );</code> to a child theme or a small plugin.' ),
	array( 'Which form plugins does it style?', 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator and Ninja Forms are mapped onto the theme’s own colours and spacing, so a form block does not look like a different website.' ),
	array( 'Can I change the colours?', 'Eight palettes and five type pairings ship with the theme, each a one-click choice in the Site Editor under Styles. Beyond that, every colour in the theme is a palette entry you can edit there.' ),
	array( 'Can I turn dark mode off?', 'Yes. Add <code>add_filter( \'daren_enable_dark_mode\', \'__return_false\' );</code> to a child theme or a small plugin.' ),
	array( 'Should I use the block theme or the Elementor edition?', 'For a new site, the block theme: everything is edited in WordPress’s own Site Editor and no plugin is needed. The Elementor edition is the same design built for the free Elementor plugin; it remains free on GitHub for sites already built with it.' ),
	array( 'Is it translation ready?', 'Yes. Every string is translatable and <code>languages/daren.pot</code> is included.' ),
	array( 'Does it check for updates?', 'Yes, twice a day, because it is distributed outside the WordPress.org theme directory. It sends the theme, WordPress and PHP versions, the locale, whether the site is a multisite, and an identifier derived from the site’s address with the site’s own secret key, so it cannot be turned back into the address. The <code>daren_check_for_updates</code> filter switches it off.' ),
);

$toggles = '';
foreach ( $faqs as $faq ) {
	// vcex_toggle takes `heading`: with `title` every toggle renders the
	// shortcode's placeholder. `style="boxed"` and the padding give the grey
	// panels Academia's page has; it ignores a css= class.
	$toggles .= '[vcex_toggle heading="' . esc_attr( $faq[0] ) . '" heading_type="h3" heading_font_size="17px" style="boxed" padding_y="16px" padding_x="20px" bottom_margin="12px"]'
		. $faq[1] . '[/vcex_toggle]';
}

$specs = array(
	'Requires'     => 'WordPress 6.6 or newer',
	'PHP'          => '7.4 or newer',
	'Tested up to' => 'WordPress 7.1',
	'Licence'      => 'GNU General Public License v2 or later',
	'Patterns'     => '14 to insert, plus 12 the templates use',
	'Templates'    => '14, plus 3 template parts',
	'Styles'       => '8 colour palettes × 5 type pairings',
	'Fonts'        => 'Source Serif Pro and Open Sans, self-hosted',
	'Icons'        => 'Tabler Icons (MIT), drawn in the palette’s colours',
	'Form plugins' => 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator, Ninja Forms',
	'Build step'   => 'None — no npm, no SCSS',
);

$spec_rows = '';
foreach ( $specs as $label => $value ) {
	$spec_rows .= '<tr><th style="text-align:left;padding:10px 18px 10px 0;border-bottom:1px solid #f0e4e4;font-weight:600;white-space:nowrap;vertical-align:top;">'
		. esc_html( $label ) . '</th><td style="padding:10px 0;border-bottom:1px solid #f0e4e4;">' . $value . '</td></tr>';
}

$section = static function ( $n, $bg, $heading, $text, $image ) use ( $tint ) {
	$background = $bg ? "background-color:{$tint} !important;" : '';
	return "[vc_row css=\".vc_custom_dr{$n}0{padding-top:56px !important;padding-bottom:20px !important;{$background}}\"][vc_column width=\"1/1\"]"
		. '[vcex_heading text="' . esc_attr( $heading ) . '" tag="h2" font_size="34px" text_align="center" bottom_margin="14px" font_weight="700"]'
		. "[vc_column_text css=\".vc_custom_dr{$n}1{text-align:center !important;max-width:790px !important;margin-left:auto !important;margin-right:auto !important;}\"]{$text}[/vc_column_text]"
		. "[/vc_column][/vc_row][vc_row css=\".vc_custom_dr{$n}2{padding-bottom:56px !important;{$background}}\"][vc_column width=\"1/1\"]"
		. "[vcex_image image_id=\"{$image}\" align=\"center\" border_radius=\"12px\" bottom_margin=\"0px\"][/vc_column][/vc_row]";
};

// Two versions: one [vc_row], two halves.
$version_col = static function ( $n, $kind, $title, $text, $btns ) {
	return '[vc_column width="1/2" css=".vc_custom_dr' . $n . '{padding:28px 30px 18px !important;background-color:#ffffff !important;border:1px solid #f0e4e4 !important;border-radius:12px !important;margin-bottom:20px !important;}"]'
		. '[vc_column_text css=".vc_custom_dr' . $n . 'k{margin-bottom:6px !important;}"]<span style="text-transform:uppercase;letter-spacing:.08em;font-size:13px;font-weight:700;color:#8a8a8a;">' . esc_html( $kind ) . '</span>[/vc_column_text]'
		. '[vcex_heading text="' . esc_attr( $title ) . '" tag="h3" font_size="24px" bottom_margin="10px" font_weight="700"]'
		. '[vc_column_text]' . $text . '[/vc_column_text]'
		. '[vc_column_text css=".vc_custom_dr' . $n . 'b{margin-top:18px !important;}"]' . $btns . '[/vc_column_text]'
		. '[/vc_column]';
};

$versions = "[vc_row css=\".vc_custom_dr020{padding-top:56px !important;padding-bottom:8px !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="Two versions of Daren" tag="h2" font_size="34px" text_align="center" bottom_margin="14px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_dr021{text-align:center !important;max-width:790px !important;margin-left:auto !important;margin-right:auto !important;}"]The same design, built two ways. Both are free.[/vc_column_text]'
	. '[/vc_column][/vc_row]'
	. '[vc_row equal_height="yes" css=".vc_custom_dr022{padding-top:18px !important;padding-bottom:40px !important;}"]'
	. $version_col(
		'023',
		'Recommended for new sites',
		'Block theme',
		'This download. Everything is edited in WordPress’s own Site Editor, with no plugins to install: the story layouts are patterns you insert and rearrange, and the theme brings eight colour palettes, five type pairings and a dark mode for your readers.',
		$buttons( '024' )
	)
	. $version_col(
		'025',
		'For Elementor sites',
		'Elementor edition',
		'The same design built for Elementor. Its post sections and contact block are Elementor widgets built into the theme, so it needs the free Elementor plugin. The source is free on GitHub.',
		$btn( 'Elementor demo', $el_demo, 'grey', 'eye', 'vc_custom_dr026a' ) . $btn( 'Source on GitHub', $el_github, 'grey', 'github', 'vc_custom_dr026b' )
	)
	. '[/vc_row]';

$content = "[vc_row css=\".vc_custom_dr001{padding-top:64px !important;padding-bottom:40px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="A journal theme where the posts lay out the page" tag="h2" font_size="46px" text_align="center" bottom_margin="20px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_dr002{text-align:center !important;font-size:18px !important;max-width:820px !important;margin-left:auto !important;margin-right:auto !important;}"]'
	. 'Daren is a free block theme for a personal blog or magazine about art, design and colour. Its home page is a set of story layouts — a tall photograph beside a wide one, a checkerboard, category cards and a long list — that fill themselves from what you publish. It comes with a contact form and a newsletter sign-up that need no plugin, eight colour palettes, dark mode, and full site editing throughout.'
	. '[/vc_column_text][vc_column_text css=".vc_custom_dr003{text-align:center !important;margin-top:26px !important;}"]' . $buttons( '004' ) . '[/vc_column_text]'
	. "[/vc_column][/vc_row][vc_row css=\".vc_custom_dr006{padding-top:0px !important;padding-bottom:64px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. "[vcex_image image_id=\"{$img['home']}\" align=\"center\" border_radius=\"14px\" bottom_margin=\"0px\"][/vc_column][/vc_row]\n\n"

	. $versions . "\n\n"

	. $section( '01', true, 'Story layouts that fill themselves',
		'Every section of the home page is a query. The two newest posts open it, the next three sit in a checkerboard of photographs and words, three more carry their category on a tab, and the rest run as a list beside the sidebar. Publish a post with a featured image and it finds its place; move or remove a section in the editor like any other block.',
		$img['stories'] ) . "\n\n"

	. $section( '02', false, 'Eight palettes, checked before release',
		'Scarlet, Cobalt, Jade, Tangerine, Orchid and Ink, and two dark palettes, Midnight and Graphite. Every one is measured against WCAG AA before the theme is built — text, links and button labels, in light mode and in dark — and a palette that fails is not written.',
		$img['palettes'] ) . "\n\n"

	. $section( '03', true, 'Dark mode the reader controls',
		'The switch in the header is the reader’s, separate from the palette you chose. It follows their system setting until they decide for themselves, and it lifts your palette rather than replacing it.',
		$img['dark'] ) . "\n\n"

	. $section( '04', false, 'A contact form and a newsletter, without a plugin',
		'The contact page’s form checks every message on the server, carries a nonce and a honeypot, and works with JavaScript turned off. Messages go to the site’s admin address; the footer’s newsletter sign-up does the same with each new address. One filter each hands them to a CRM or a mailing list instead.',
		$img['contact'] ) . "\n\n"

	. "[vc_row css=\".vc_custom_dr050{padding-top:56px !important;padding-bottom:16px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="What you get" tag="h2" font_size="34px" text_align="center" bottom_margin="34px" font_weight="700"][/vc_column][/vc_row]'
	. $feature_rows . "\n\n"

	. $section( '06', false, 'A journal, categories and search, each laid out',
		'The journal runs two stories to a row beside the sidebar; categories and search results use the list. Tag, author and date archives, a 404 and the comments are designed rather than inherited.',
		$img['blog'] ) . "\n\n"

	. $section( '07', true, 'A single post with room to read',
		'The band above the story carries its category, the title sits under the photograph as in the original design, and the row under it gives the byline, the date, the comment count and the reading time. Quotations, lists and headings are styled for long reading, with the author and the comments below.',
		$img['single'] ) . "\n\n"

	. "[vc_row css=\".vc_custom_dr070{padding-top:56px !important;padding-bottom:18px !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="Questions" tag="h2" font_size="34px" text_align="center" bottom_margin="28px" font_weight="700"][/vc_column][/vc_row]'
	. "[vc_row css=\".vc_custom_dr071{padding-bottom:48px !important;}\"][vc_column width=\"1/1\"]{$toggles}[/vc_column][/vc_row]\n\n"

	. "[vc_row css=\".vc_custom_dr080{padding-top:48px !important;padding-bottom:56px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="The details" tag="h2" font_size="34px" text_align="center" bottom_margin="26px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_dr081{max-width:680px !important;margin-left:auto !important;margin-right:auto !important;}"]<table style="width:100%;border-collapse:collapse;">' . $spec_rows . '</table>[/vc_column_text]'
	. '[vc_column_text css=".vc_custom_dr082{text-align:center !important;margin-top:34px !important;}"]' . $buttons( '083' ) . '[/vc_column_text]'
	. '[/vc_column][/vc_row]';

// ---------------------------------------------------------------------------
// Save
// ---------------------------------------------------------------------------

// kses strips the shortcode attributes this page is made of.
$kses = has_filter( 'content_save_pre', 'wp_filter_post_kses' );
if ( $kses ) {
	kses_remove_filters();
}

$args = array(
	'post_title'   => 'Daren',
	'post_name'    => $slug,
	'post_content' => $content,
	// Draft on creation, but never demote a page that is already published:
	// rebuilding the copy of a live page must not take it off the site.
	'post_status'  => $existing ? $existing->post_status : 'draft',
	'post_type'    => 'page',
	'post_parent'  => $parent,
);

// wp_insert_post() and wp_update_post() unslash their input.
if ( $page_id ) {
	$args['ID'] = $page_id;
	$result     = wp_update_post( wp_slash( $args ), true );
} else {
	$result  = wp_insert_post( wp_slash( $args ), true );
	$page_id = is_wp_error( $result ) ? 0 : $result;
}

if ( $kses ) {
	kses_init_filters();
}

if ( is_wp_error( $result ) ) {
	echo 'ERROR: ' . $result->get_error_message() . "\n";
	return;
}

set_post_thumbnail( $page_id, $img['card'] );

// The full-width template and WPBakery's own flag, as Academia, Pato and
// Unioncorp have. Without the template the page renders in the default
// layout's narrow column beside an empty sidebar.
update_post_meta( $page_id, '_wp_page_template', 'templates/no-sidebar.php' );
update_post_meta( $page_id, '_wpb_vc_js_status', 'true' );
update_post_meta( $page_id, '_yoast_wpseo_title', 'Daren – Free Blog WordPress Theme - %%sitename%%' );
update_post_meta( $page_id, '_yoast_wpseo_metadesc', 'A free block theme for a personal blog or journal: story layouts that fill themselves, eight colour palettes, dark mode and a contact form built in.' );

// WPBakery keeps every css="…" rule in _wpb_shortcodes_custom_css and only
// regenerates it when the page is saved through the builder UI. Updating
// post_content programmatically leaves that meta stale.
if ( function_exists( 'visual_composer' ) && method_exists( visual_composer(), 'buildShortcodesCss' ) ) {
	visual_composer()->buildShortcodesCss( $page_id, 'custom' );
	visual_composer()->buildShortcodesCss( $page_id, 'default' );
	echo "custom css rebuilt\n";
} else {
	echo "WARNING: could not rebuild the WPBakery custom css\n";
}

$saved = get_post_field( 'post_content', $page_id );

echo 'page: ' . $page_id . ' (' . get_post_status( $page_id ) . '), parent ' . wp_get_post_parent_id( $page_id ) . "\n";
echo 'images: ' . wp_json_encode( $img ) . "\n";
echo 'length: ' . strlen( $saved ) . "\n";
echo 'icon boxes: ' . substr_count( $saved, '[vcex_icon_box' ) . " (must be 9)\n";
echo 'toggles: ' . substr_count( $saved, '[vcex_toggle' ) . ' (with heading=: ' . substr_count( $saved, '[vcex_toggle heading=' ) . ', boxed: ' . substr_count( $saved, 'style="boxed"' ) . ")\n";
echo 'template: ' . get_post_meta( $page_id, '_wp_page_template', true ) . ', vc_js: ' . get_post_meta( $page_id, '_wpb_vc_js_status', true ) . "\n";
echo 'section images: ' . substr_count( $saved, '[vcex_image' ) . " (must be 7)\n";
echo 'buttons: ' . substr_count( $saved, '[vc_btn' ) . " (must be 8)\n";
echo 'download buttons: ' . substr_count( $saved, 'title="Download Daren"' ) . ' (must be 3), gated href: ' . substr_count( $saved, rawurlencode( $download ) ) . " (must be 3)\n";
echo 'half columns: ' . substr_count( $saved, '[vc_column width="1/2"' ) . " (must be 2)\n";
echo 'documentation buttons: ' . substr_count( $saved, 'Documentation' ) . " (must be 0)\n";
echo 'unbalanced rows: ' . ( substr_count( $saved, '[vc_row' ) - substr_count( $saved, '[/vc_row]' ) ) . "\n";
