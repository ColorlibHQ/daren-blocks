<?php
/**
 * Title: About: three topics
 * Slug: daren/about-topics
 * Categories: daren-sections
 * Keywords: about, topics, categories, features
 * Description: A centred title and three columns, each a photograph, a category, a title and a line of copy.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"620px"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"center"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-center is-style-daren-flag has-xx-large-font-size">What I write about</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center">Three things I cannot stop looking at, and the categories you will find them under.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dspacing\u002d\u002d50)"} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:image {"aspectRatio":"360/172","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/topic-colour.webp' ) ); ?>" alt="A silver spoon heaped with rainbow sprinkles against a magenta background" style="aspect-ratio:360/172;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"daren-kicker","fontSize":"x-small"} -->
<p class="daren-kicker has-x-small-font-size">Still Life</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size">Colour you can hold</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sets built in the studio from whatever is the right colour: eggs, bottles, sprinkles, a chair.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:image {"aspectRatio":"360/172","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/topic-walls.webp' ) ); ?>" alt="Detail of a mural of a bear's face built from triangles of flat orange, blue, green and pink" style="aspect-ratio:360/172;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"daren-kicker","fontSize":"x-small"} -->
<p class="daren-kicker has-x-small-font-size">Street Art</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size">Walls and the people who paint them</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Murals in Lisbon and wherever else I happen to be, looked at closely and slowly.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:image {"aspectRatio":"360/172","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/topic-still-life.webp' ) ); ?>" alt="A striped succulent in a speckled stone pot against a bright blue wall" style="aspect-ratio:360/172;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"daren-kicker","fontSize":"x-small"} -->
<p class="daren-kicker has-x-small-font-size">Illustration</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size">How a picture is made</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Paint, brushes, briefs and the decisions nobody sees in the finished thing.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
