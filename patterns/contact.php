<?php
/**
 * Title: Contact: map, form and details
 * Slug: daren/contact
 * Categories: daren-sections
 * Keywords: contact, form, map, address
 * Description: A map across the top, then the contact form beside the address, phone and email.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"},"anchor":"contact"} -->
<div class="wp-block-group alignfull" id="contact" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:html -->
<iframe class="daren-map" title="Map of Bairro Alto, Lisbon, where the studio is" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=-9.1500%2C38.7095%2C-9.1405%2C38.7160&amp;layer=mapnik&amp;marker=38.71275%2C-9.14530" style="width:100%;height:480px;border:0"></iframe>
<!-- /wp:html -->

<!-- wp:spacer {"height":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dspacing\u002d\u002d60)"} -->
<div style="height:var(--wp--preset--spacing--60)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"className":"daren-with-sidebar","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns daren-with-sidebar"><!-- wp:column {"width":"67.5%"} -->
<div class="wp-block-column" style="flex-basis:67.5%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"title"} -->
<h2 class="wp-block-heading has-title-font-size">Get in Touch</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[daren_contact_form]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"32.5%"} -->
<div class="wp-block-column" style="flex-basis:32.5%"><!-- wp:group {"className":"daren-contact-details","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group daren-contact-details"><!-- wp:group {"className":"daren-contact-info daren-icon\u002d\u002dhome","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group daren-contact-info daren-icon--home"><!-- wp:heading {"level":3,"className":"daren-contact-info__title","fontSize":"medium"} -->
<h3 class="wp-block-heading daren-contact-info__title has-medium-font-size">Bairro Alto, Lisbon</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Studio 4, Rua da Rosa 142, 1200-389 Lisboa</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-contact-info daren-icon\u002d\u002ddevice-mobile","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group daren-contact-info daren-icon--device-mobile"><!-- wp:heading {"level":3,"className":"daren-contact-info__title","fontSize":"medium"} -->
<h3 class="wp-block-heading daren-contact-info__title has-medium-font-size">+351 912 480 316</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Monday to Friday, 10:00 to 18:00</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-contact-info daren-icon\u002d\u002dmail","style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group daren-contact-info daren-icon--mail"><!-- wp:heading {"level":3,"className":"daren-contact-info__title","fontSize":"medium"} -->
<h3 class="wp-block-heading daren-contact-info__title has-medium-font-size">hello@yourdomain.com</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Commissions, questions and good walls to look at.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
