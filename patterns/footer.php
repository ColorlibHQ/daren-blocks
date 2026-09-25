<?php
/**
 * Title: Footer
 * Slug: daren/footer
 * Keywords: footer, newsletter
 * Block Types: core/template-part/footer
 * Description: About, contact details and a newsletter sign-up on the dark ground, with the copyright line beneath.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-footer","backgroundColor":"dark","textColor":"on-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-footer has-on-dark-color has-dark-background-color has-text-color has-background"><!-- wp:columns {"className":"daren-footer__columns","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns daren-footer__columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"daren-footer__title","textColor":"overlay","fontSize":"xx-large"} -->
<h2 class="wp-block-heading daren-footer__title has-overlay-color has-text-color has-xx-large-font-size">About Me</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"on-dark","fontSize":"small"} -->
<p class="has-on-dark-color has-text-color has-small-font-size">I'm Daren Ellis, an illustrator and art director in Lisbon. This journal is where I write about colour: where it comes from, how it is made, and what it does to the people who look at it. A new story most Fridays.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"daren-footer__title","textColor":"overlay","fontSize":"xx-large"} -->
<h2 class="wp-block-heading daren-footer__title has-overlay-color has-text-color has-xx-large-font-size">Contact us</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"daren-detail daren-icon\u002d\u002dhome","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group daren-detail daren-icon--home"><!-- wp:heading {"level":3,"className":"daren-footer__detail-title","textColor":"overlay","fontSize":"medium"} -->
<h3 class="wp-block-heading daren-footer__detail-title has-overlay-color has-text-color has-medium-font-size">Lisbon, Portugal</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"on-dark","fontSize":"small"} -->
<p class="has-on-dark-color has-text-color has-small-font-size">Studio 4, Rua da Rosa 142, Bairro Alto, 1200-389 Lisboa</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-detail daren-icon\u002d\u002dheadphones","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group daren-detail daren-icon--headphones"><!-- wp:heading {"level":3,"className":"daren-footer__detail-title","textColor":"overlay","fontSize":"medium"} -->
<h3 class="wp-block-heading daren-footer__detail-title has-overlay-color has-text-color has-medium-font-size">+351 912 480 316</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"on-dark","fontSize":"small"} -->
<p class="has-on-dark-color has-text-color has-small-font-size">Monday to Friday, 10:00 to 18:00</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"daren-footer__title","textColor":"overlay","fontSize":"xx-large"} -->
<h2 class="wp-block-heading daren-footer__title has-overlay-color has-text-color has-xx-large-font-size">Newsletter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"on-dark","fontSize":"small"} -->
<p class="has-on-dark-color has-text-color has-small-font-size">One email a month: the new stories, a palette I have been using and one thing worth looking at. No noise, and you can leave with one click.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[daren_newsletter_form]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"className":"daren-footer__legal","style":{"typography":{"textAlign":"center"}},"textColor":"on-dark","fontSize":"small"} -->
<p class="has-text-align-center daren-footer__legal has-on-dark-color has-text-color has-small-font-size">Copyright ©<?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( rtrim( get_bloginfo( 'name' ), '.' ) ); ?>. All rights reserved <span class="daren-footer__sep" aria-hidden="true">|</span> Theme made with <span class="daren-heart" aria-hidden="true">♡</span> by <a href="https://colorlib.com/" rel="nofollow">Colorlib</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
