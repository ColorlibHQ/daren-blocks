<?php
/**
 * Title: Stories: latest three
 * Slug: daren/stories-latest
 * Categories: daren-posts
 * Keywords: latest, posts, cards, blog
 * Description: A titled row of the three newest stories, as cards.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-cards-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-cards-section" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">From the journal</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":17,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-cards","layout":{"type":"default"}} -->
<div class="wp-block-query daren-cards"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-card","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card"><!-- wp:group {"className":"daren-card__media","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card__media"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"360/336"} /-->

<!-- wp:post-terms {"term":"category","className":"daren-tab"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card__body"><!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"xx-large"} /-->

<!-- wp:group {"className":"daren-actions","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-actions"><!-- wp:read-more {"content":"Comments","className":"daren-action\u002d\u002dcomments"} /-->

<!-- wp:read-more {"content":"min read","className":"daren-action\u002d\u002dtime"} /-->

<!-- wp:read-more {"content":"Share","className":"daren-action\u002d\u002dshare"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
