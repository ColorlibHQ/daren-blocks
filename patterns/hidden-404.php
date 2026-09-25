<?php
/**
 * Title: 404 content
 * Slug: daren/hidden-404
 * Description: What a visitor sees when nothing is there.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"620px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:paragraph {"className":"daren-kicker","style":{"typography":{"textAlign":"center"}},"fontSize":"x-small"} -->
<p class="has-text-align-center daren-kicker has-x-small-font-size">Error 404</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"textAlign":"center"}},"fontSize":"heading"} -->
<h2 class="wp-block-heading has-text-align-center has-heading-font-size">This page has wandered off</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"textAlign":"center"}}} -->
<p class="has-text-align-center">The address may be old, or the story may have been renamed. Try a search, or start again from the journal.</p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search keyword","buttonText":"Search","buttonUseIcon":true,"className":"daren-sidebar__search"} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-daren-arrow"} -->
<div class="wp-block-button is-style-daren-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to the journal</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
