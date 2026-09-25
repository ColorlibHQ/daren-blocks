<?php
/**
 * Render a Colorlib block theme's product page (WPBakery) from a spec JSON, so
 * every theme page shares one layout: the same spacing scale, backgrounds that
 * alternate by section (heading and body always share one), a unique css= class
 * for every row (WPBakery keys its custom CSS by class name, so a reused name
 * silently gives two rows the same padding and background), and one design for
 * the "Two versions" cards.
 *
 *   CLT_SPEC=/path/spec-<slug>.json wp --url=https://colorlib.com/wp/ eval "require '/path/render-page.php';"
 *
 * Updates the existing page (slug under 5091) in place and keeps its status,
 * title, thumbnail and SEO meta. Refuses to save if any image is missing.
 */

defined( 'ABSPATH' ) || exit;

$spec = json_decode( (string) file_get_contents( getenv( 'CLT_SPEC' ) ), true );
if ( ! is_array( $spec ) ) {
	echo "ERROR: no spec\n";
	return;
}
$slug = $spec['slug'];

$found = get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_parent' => 5091, 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ), 'numberposts' => 1 ) );
if ( ! $found ) {
	echo "ERROR: no page $slug under 5091\n";
	return;
}
$page_id = getenv( 'CLT_PAGE_ID' ) ? (int) getenv( 'CLT_PAGE_ID' ) : $found[0]->ID;

// ---------------------------------------------------------------- images
global $wpdb;
$missing = array();
$img     = static function ( $file ) use ( $wpdb, &$missing ) {
	$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND ( meta_value = %s OR meta_value LIKE %s ) ORDER BY post_id DESC LIMIT 1", $file, '%/' . $wpdb->esc_like( $file ) ) );
	if ( ! $id ) {
		$missing[] = $file;
	}
	return $id;
};

// ---------------------------------------------------------------- helpers
$prefix = 'p' . substr( md5( $slug ), 0, 4 );
$n      = 0;
$cls    = static function () use ( $prefix, &$n ) {
	return sprintf( '.vc_custom_%s%03d', $prefix, ++$n );
};
$tint   = $spec['tint'];
$accent = $spec['accent'];
$line   = '#e7e9ef';

// One vertical rhythm for the whole page. Total gives every column a 40px
// bottom margin of its own, so a row's effective bottom space is its padding
// + 40: 'bottom' => 40 reads as 80, the same as 'top'.
$S = array( 'hero_top' => 72, 'top' => 80, 'bottom' => 40 );

$row = static function ( $inner, $bg, $pt, $pb, $extra = '', $side = 0 ) use ( $cls ) {
	$background = $bg ? "background-color:{$bg} !important;" : '';
	// Total pads rows that have a background; a white row that must line up
	// with them (the version cards) takes the same inset explicitly.
	$side        = $side ? $side : ( $bg ? 0 : 15 );
	$background .= $side ? "padding-left:{$side}px !important;padding-right:{$side}px !important;" : '';
	return '[vc_row' . $extra . ' css="' . $cls() . "{padding-top:{$pt}px !important;padding-bottom:{$pb}px !important;{$background}}\"]" . $inner . '[/vc_row]';
};
$col = static function ( $inner, $width = '1/1', $extra = '' ) {
	return '[vc_column width="' . $width . '"' . $extra . ']' . $inner . '[/vc_column]';
};
// Responsive sizes in Total's d/tl/tp/pl form (desktop, tablet landscape,
// tablet portrait, phone): a 44px statement is five lines on a phone.
$h2 = static function ( $text, $size = 34, $gap = 14 ) {
	$sizes = 44 === $size ? 'd:44px|tl:40px|tp:34px|pl:28px' : 'd:' . $size . 'px|tl:' . $size . 'px|tp:30px|pl:25px';
	return '[vcex_heading text="' . esc_attr( $text ) . '" tag="h2" font_size="' . $sizes . '" text_align="center" bottom_margin="' . $gap . 'px" font_weight="700"]';
};
$lede = static function ( $html, $size = 17, $max = 780 ) use ( $cls ) {
	return '[vc_column_text css="' . $cls() . "{text-align:center !important;font-size:{$size}px !important;line-height:1.65 !important;max-width:{$max}px !important;margin-left:auto !important;margin-right:auto !important;margin-bottom:0 !important;}\"]" . $html . '[/vc_column_text]';
};
$btn_css = 'display:inline-block !important;vertical-align:middle !important;margin:0 12px 10px 0 !important;';
$btn     = static function ( $title, $url, $color, $icon, $blank = true ) use ( $cls, $btn_css ) {
	$link = 'url:' . rawurlencode( $url ) . '|title:' . rawurlencode( $title ) . ( $blank ? '|target:_blank' : '' );
	return '[vc_btn title="' . esc_attr( $title ) . '" style="flat" color="' . $color . '" link="' . $link . '" css="' . $cls() . '{' . $btn_css . '}" i_icon_fontawesome="fa fa-' . $icon . '" add_icon="true"]';
};
$buttons = static function ( $align = 'center', $top = 26 ) use ( $spec, $btn, $cls ) {
	return '[vc_column_text css="' . $cls() . "{text-align:{$align} !important;margin-top:{$top}px !important;margin-bottom:0 !important;}\"]"
		. $btn( 'Download ' . $spec['name'], $spec['download'], 'green', 'download' )
		. $btn( 'Live demo', $spec['demo'], 'grey', 'eye' )
		. '[/vc_column_text]';
};
$image = static function ( $id ) {
	return '[vcex_image image_id="' . (int) $id . '" align="center" border_radius="12px" bottom_margin="0px"]';
};

// ---------------------------------------------------------------- content
$out = '';

// Hero: statement, intro, buttons, then the home page screenshot, on the tint.
$out .= $row( $col( $h2( $spec['hero']['heading'], 44, 20 ) . $lede( $spec['hero']['text'], 18, 820 ) . $buttons() ), $tint, $S['hero_top'], 8 );
$out .= $row( $col( $image( $img( $spec['hero']['image'] ) ), '1/1', ' el_class="clt-shot"' ), $tint, 0, $S['bottom'] );

// Two versions: two matching cards on white.
$v    = $spec['versions'];
$card = static function ( $c, $btns ) use ( $cls, $accent, $line ) {
	return '[vc_column_text css="' . $cls() . "{margin-bottom:10px !important;}\"]"
		. '<p style="margin:0;font-size:12px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:' . $accent . ';">' . esc_html( $c['kicker'] ) . '</p>[/vc_column_text]'
		. '[vcex_heading text="' . esc_attr( $c['title'] ) . '" tag="h3" font_size="24px" bottom_margin="12px" font_weight="700"]'
		. '[vc_column_text css="' . $cls() . "{font-size:16px !important;line-height:1.65 !important;margin-bottom:0 !important;}\"]<p>" . $c['text'] . '</p>[/vc_column_text]'
		. '[vc_column_text el_class="clt-card-btns" css="' . $cls() . "{padding-top:22px !important;margin-bottom:0 !important;}\"]" . $btns . '[/vc_column_text]';
};
$card_col = static function ( $inner ) use ( $cls, $line ) {
	return '[vc_column width="1/2" el_class="clt-card" css="' . $cls() . "{border:1px solid {$line} !important;border-radius:14px !important;padding:34px 34px 24px !important;margin-bottom:0 !important;background-color:#ffffff !important;}\"]" . $inner . '[/vc_column]';
};
// The cards are equal height (row equal_height); this keeps each card's
// buttons on its bottom edge whatever the length of its text. Inline because
// WPBakery's page-CSS meta is not printed on every view.
$card_css = '<style>.clt-card>.vc_column-inner{display:flex;flex-direction:column}.clt-card>.vc_column-inner>.wpb_wrapper{display:flex;flex-direction:column;flex:1 1 auto}.clt-card .clt-card-btns{margin-top:auto !important}@media (min-width:768px){.clt-card:first-child>.vc_column-inner{margin-right:12px}.clt-card:last-child>.vc_column-inner{margin-left:12px}}@media (max-width:767px){.clt-card:first-child>.vc_column-inner{margin-bottom:20px !important}}.clt-shot img{border:1px solid #e3e6ee;box-shadow:0 22px 44px -24px rgba(20,24,40,.28)}</style>';
$out .= $row( $col( $h2( $v['heading'], 34, 12 ) . $lede( $v['lede'] . $card_css ) ), '', $S['top'], 0 );
$out .= $row(
	$card_col( $card( $v['block'], $btn( 'Download ' . $spec['name'], $spec['download'], 'green', 'download' ) . $btn( 'Live demo', $spec['demo'], 'grey', 'eye' ) ) )
	. $card_col( $card( $v['elementor'], $btn( 'Elementor demo', $spec['elementor_demo'], 'grey', 'eye' ) . $btn( 'Source on GitHub', $spec['elementor_repo'], 'grey', 'github' ) ) ),
	'',
	0,
	$S['bottom'] + 5,
	' equal_height="yes"',
	30
);

// Sections, features and questions, alternating tint / white.
$on = true;
foreach ( $spec['blocks'] as $b ) {
	$bg = $on ? $tint : '';
	if ( 'section' === $b['type'] ) {
		$out .= $row( $col( $h2( $b['heading'] ) . $lede( $b['text'] ) ), $bg, $S['top'], 0 );
		$out .= $row( $col( $image( $img( $b['image'] ) ), '1/1', ' el_class="clt-shot"' ), $bg, 0, $S['bottom'] );
	} elseif ( 'features' === $b['type'] ) {
		$out  .= $row( $col( $h2( $b['heading'], 34, 0 ) ), $bg, $S['top'], 8 );
		$rows  = array_chunk( $spec['features'], 3 );
		foreach ( $rows as $r => $three ) {
			$cells = '';
			foreach ( $three as $f ) {
				$cells .= $col(
					'[vcex_icon_box style="two" heading="' . esc_attr( $f['heading'] ) . '" heading_type="h3" icon="' . esc_attr( $f['icon'] ) . '" icon_color="' . $accent . '"'
					. ' icon_size="28px" heading_size="20px" content_font_size="15px" css="' . $cls() . '{margin-bottom:0 !important;}"]' . $f['text'] . '[/vcex_icon_box]',
					'1/3'
				);
			}
			$last = ( count( $rows ) - 1 === $r );
			$out .= $row( $cells, $bg, 0, $last ? $S['bottom'] - 40 : 0 );
		}
	} elseif ( 'faqs' === $b['type'] ) {
		$out .= $row( $col( $h2( $b['heading'], 34, 0 ) ), $bg, $S['top'], 0 );
		$toggles = '';
		foreach ( $spec['faqs'] as $q ) {
			$toggles .= '[vcex_toggle heading="' . esc_attr( $q['q'] ) . '" heading_type="h3" heading_font_size="17px" style="boxed" padding_y="16px" padding_x="20px" bottom_margin="12px"]' . $q['a'] . '[/vcex_toggle]';
		}
		$out .= $row( $col( $toggles, '2/3', ' offset="vc_col-sm-offset-2"' ), $bg, 0, $S['bottom'] );
	}
	$on = ! $on;
}

// The details, always on the opposite ground of the block above, with the buttons.
$bg   = $on ? $tint : '';
$out .= $row(
	$col(
		$h2( 'The details', 34, 26 )
		. '[vc_column_text css="' . $cls() . '{max-width:680px !important;margin-left:auto !important;margin-right:auto !important;margin-bottom:0 !important;}"]' . $spec['details'] . '[/vc_column_text]'
		. $buttons( 'center', 36 )
	),
	$bg,
	$S['top'],
	$S['bottom'] - 10
);

if ( $missing ) {
	echo "ERROR: not in the media library: " . implode( ', ', array_unique( $missing ) ) . "\n";
	return;
}

// ---------------------------------------------------------------- save
$before = get_post_field( 'post_content', $page_id );
$kses   = has_filter( 'content_save_pre', 'wp_filter_post_kses' );
if ( $kses ) {
	kses_remove_filters();
}
$result = wp_update_post( array( 'ID' => $page_id, 'post_content' => wp_slash( $out ) ), true );
if ( $kses ) {
	kses_init_filters();
}
if ( is_wp_error( $result ) ) {
	echo 'ERROR: ' . $result->get_error_message() . "\n";
	return;
}
delete_post_meta( $page_id, '_wpb_post_custom_css' );
if ( function_exists( 'visual_composer' ) && method_exists( visual_composer(), 'buildShortcodesCss' ) ) {
	visual_composer()->buildShortcodesCss( $page_id, 'custom' );
	visual_composer()->buildShortcodesCss( $page_id, 'default' );
}
$saved = get_post_field( 'post_content', $page_id );
preg_match_all( '~\.vc_custom_[a-z0-9]+\{~', $saved, $m );
echo "page $page_id ($slug, " . get_post_status( $page_id ) . '): ' . strlen( $before ) . ' -> ' . strlen( $saved ) . " bytes\n";
echo 'css classes: ' . count( $m[0] ) . ', unique: ' . count( array_unique( $m[0] ) ) . "\n";
echo 'rows: ' . substr_count( $saved, '[vc_row' ) . ', unbalanced: ' . ( substr_count( $saved, '[vc_row' ) - substr_count( $saved, '[/vc_row]' ) ) . "\n";
echo 'images: ' . substr_count( $saved, '[vcex_image' ) . ', boxes: ' . substr_count( $saved, '[vcex_icon_box' ) . ', toggles: ' . substr_count( $saved, '[vcex_toggle' ) . "\n";
echo 'download buttons: ' . substr_count( $saved, rawurlencode( $spec['download'] ) ) . " (must be 3)\n";
