<?php
/**
 * Title: About: introduction
 * Slug: daren/about-intro
 * Categories: daren-sections
 * Keywords: about, author, introduction
 * Description: A photograph beside a short introduction, a signature and two buttons.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"center","className":"daren-about","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|70","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center daren-about"><!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%"><!-- wp:image {"aspectRatio":"750/809","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/studio-set.webp' ) ); ?>" alt="A straw hat and sunglasses on an orange string chair against a pink wall, with paper cocktail umbrellas hanging above it" style="aspect-ratio:750/809;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"daren-kicker","fontSize":"x-small"} -->
<p class="daren-kicker has-x-small-font-size">Hello</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"heading"} -->
<h2 class="wp-block-heading has-heading-font-size">I'm Daren. This is a journal about colour.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For fifteen years I have been paid to choose colours for other people: for book covers, for packaging, for a pool-float company and a tea that tastes of liquorice. This is where I write about the ones I choose for myself.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Most weeks that means a set I have built in the studio, a wall I have walked past too many times to ignore, or a paint I cannot stop using. Every picture here was made or found for the story it sits in.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"daren-signature","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-signature"><!-- wp:image {"width":"72px","aspectRatio":"1/1","scale":"cover","sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
<figure class="wp-block-image size-large is-resized has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/portrait.webp' ) ); ?>" alt="" style="border-radius:50%;aspect-ratio:1/1;object-fit:cover;width:72px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><strong>Daren Ellis</strong><br>Illustrator and art director, Lisbon</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-daren-arrow"} -->
<div class="wp-block-button is-style-daren-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Read the journal</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-daren-outline"} -->
<div class="wp-block-button is-style-daren-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Write to me</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
